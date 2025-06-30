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
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;
use WechatMiniProgramUrlLinkBundle\Event\PromotionCodeRequestEvent;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[MethodTag(name: '微信小程序')]
#[MethodDoc(summary: '获取小程序推广码配置信息')]
#[MethodExpose(method: 'GetWechatMiniProgramPromotionCodeInfo')]
#[WithMonologChannel(channel: 'procedure')]
class GetWechatMiniProgramPromotionCodeInfo extends BaseProcedure
{
    use LaunchOptionsAware;

    #[MethodParam(description: '活动ID')]
    public int $id;

    public function __construct(
        private readonly PromotionCodeRepository $promotionCodeRepository,
        private readonly DoctrineService $doctrineService,
        private readonly Security $security,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function execute(): array
    {
        $code = $this->promotionCodeRepository->find($this->id);
        if (null === $code) {
            throw new ApiException('找不到推广码');
        }

        // 活动时间内有效
        if (!empty($code->getStartTime()) && !empty($code->getEndTime())) {
            $now = CarbonImmutable::now();
            if ($code->getStartTime() > $now || $code->getEndTime() < $now) {
                throw new ApiException('活动已结束', 1001);
            }
        }

        // 同时保存访问记录
        $log = new VisitLog();
        $log->setCode($code);
        $log->setEnvVersion($code->getEnvVersion());
        $log->setLaunchOptions($this->launchOptions);
        $log->setEnterOptions($this->enterOptions);
        if (null !== $this->security->getUser()) {
            $log->setUser($this->security->getUser());
        }

        if (!$code->isValid()) {
            // 码是无效的话，我们就跳转走
            $log->setResponse([
                'forceLogin' => $code->isForceLogin(),
                '__reLaunch' => [
                    'url' => $_ENV['WECHAT_MINI_PROGRAM_INDEX_PAGE'] ?? '/pages/index/index',
                ],
            ]);

            try {
                $this->doctrineService->asyncInsert($log);
            } catch (\Throwable $exception) {
                $this->logger->error('保存记录时发生错误', [
                    'log' => $log,
                    'exception' => $exception,
                ]);
            }

            return $log->getResponse();
        }

        $event = new PromotionCodeRequestEvent();
        $event->setCode($code);
        $event->setUser($this->security->getUser());
        $this->eventDispatcher->dispatch($event);
        if (!empty($event->getResult())) {
            $log->setResponse([
                'forceLogin' => $code->isForceLogin(),
                ...$event->getResult(),
            ]);
            try {
                $this->doctrineService->asyncInsert($log);
            } catch (\Throwable $exception) {
                $this->logger->error('保存记录时发生错误', [
                    'log' => $log,
                    'exception' => $exception,
                ]);
            }

            return $log->getResponse();
        }

        // 这里只处理了默认的情形，如果要跳转到tab页，需要自己订阅事件来进行处理
        $url = $code->getLinkUrl();
        $url = trim($url, '/');
        $url = "/{$url}";

        $log->setResponse([
            'forceLogin' => $code->isForceLogin(),
            '__redirectTo' => [
                'url' => $url,
            ],
        ]);
        $tabPages = [
            '/pages/index/index',
            '/pages/block/block',
            '/pages/validate/validate',
            '/pages/myCenter/myCenter',
            '/pages/my/index',
        ];
        foreach ($tabPages as $tabPage) {
            if (str_starts_with($url, $tabPage)) {
                $log->setResponse([
                    'forceLogin' => $code->isForceLogin(),
                    '__reLaunch' => [
                        'url' => $url,
                    ],
                ]);
                break;
            }
        }

        try {
            $this->doctrineService->asyncInsert($log);
        } catch (\Throwable $exception) {
            $this->logger->error('保存记录时发生错误', [
                'log' => $log,
                'exception' => $exception,
            ]);
        }

        return $log->getResponse();
    }
}
