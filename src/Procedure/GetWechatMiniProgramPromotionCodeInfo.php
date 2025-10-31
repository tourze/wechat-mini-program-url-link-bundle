<?php

namespace WechatMiniProgramUrlLinkBundle\Procedure;

use Carbon\CarbonImmutable;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use WechatMiniProgramBundle\Procedure\LaunchOptionsAware;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;
use WechatMiniProgramUrlLinkBundle\Event\PromotionCodeRequestEvent;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '获取小程序推广码配置信息')]
#[MethodExpose(method: 'GetWechatMiniProgramPromotionCodeInfo')]
#[WithMonologChannel(channel: 'wechat_mini_program_url_link')]
class GetWechatMiniProgramPromotionCodeInfo extends BaseProcedure
{
    use LaunchOptionsAware;

    #[MethodParam(description: '码ID')]
    public int $id;

    public function __construct(
        private readonly PromotionCodeRepository $promotionCodeRepository,
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $code = $this->findPromotionCode();
        $this->validateCodeActivity($code);

        $log = $this->createVisitLog($code);

        if (false === $code->isValid()) {
            return $this->handleInvalidCode($code, $log);
        }

        return $this->handleValidCode($code, $log);
    }

    private function findPromotionCode(): PromotionCode
    {
        $code = $this->promotionCodeRepository->find($this->id);
        if (null === $code) {
            throw new ApiException('找不到推广码');
        }

        return $code;
    }

    private function validateCodeActivity(PromotionCode $code): void
    {
        if (null !== $code->getStartTime() && null !== $code->getEndTime()) {
            $now = CarbonImmutable::now();
            if ($code->getStartTime() > $now || $code->getEndTime() < $now) {
                throw new ApiException('活动已结束', 1001);
            }
        }
    }

    private function createVisitLog(PromotionCode $code): VisitLog
    {
        $log = new VisitLog();
        $log->setCode($code);
        $log->setEnvVersion($code->getEnvVersion());
        $log->setLaunchOptions($this->launchOptions);
        $log->setEnterOptions($this->enterOptions);
        if (null !== $this->security->getUser()) {
            $log->setUser($this->security->getUser());
        }

        return $log;
    }

    /**
     * @return array<string, mixed>
     */
    private function handleInvalidCode(PromotionCode $code, VisitLog $log): array
    {
        $indexPage = $_ENV['WECHAT_MINI_PROGRAM_INDEX_PAGE'] ?? null;
        $defaultPage = $_ENV['WECHAT_MINI_PROGRAM_DEFAULT_PAGE'] ?? null;
        $finalPage = $indexPage ?? $defaultPage ?? 'pages/index/index';

        $url = is_string($finalPage) ? $this->normalizeUrl($finalPage) : $this->normalizeUrl('pages/index/index');

        $response = [
            'forceLogin' => $code->isForceLogin(),
            'url' => $url,
        ];

        $log->setResponse($response);
        $this->saveLog($log);

        return $response;
    }

    /**
     * @return array<string, mixed>
     */
    private function handleValidCode(PromotionCode $code, VisitLog $log): array
    {
        $eventResult = $this->dispatchPromotionEvent($code);
        if ([] !== $eventResult) {
            $response = [
                'forceLogin' => $code->isForceLogin(),
                ...$eventResult,
            ];

            $log->setResponse($response);
            $this->saveLog($log);

            return $response;
        }

        return $this->handleDefaultRedirect($code, $log);
    }

    /**
     * @return array<string, mixed>
     */
    private function dispatchPromotionEvent(PromotionCode $code): array
    {
        $event = new PromotionCodeRequestEvent();
        $event->setCode($code);
        $event->setUser($this->security->getUser());
        $this->eventDispatcher->dispatch($event);

        return $event->getResult();
    }

    /**
     * @return array<string, mixed>
     */
    private function handleDefaultRedirect(PromotionCode $code, VisitLog $log): array
    {
        $linkUrl = $code->getLinkUrl();
        if (null === $linkUrl) {
            throw new ApiException('推广链接未设置');
        }

        $url = $this->normalizeUrl($linkUrl);

        $response = [
            'forceLogin' => $code->isForceLogin(),
            'url' => $url,
        ];

        $log->setResponse($response);
        $this->saveLog($log);

        return $response;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url, '/');

        return "/{$url}";
    }

    private function saveLog(VisitLog $log): void
    {
        try {
            $this->doctrineService->asyncInsert($log);
        } catch (\Throwable $exception) {
            $this->logger->error('保存记录时发生错误', [
                'log' => $log,
                'exception' => $exception,
            ]);
        }
    }
}
