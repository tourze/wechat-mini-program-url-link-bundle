<?php

namespace WechatMiniProgramUrlLinkBundle\Controller;

use Carbon\CarbonImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService as DoctrineService;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Request\GenerateUrlLinkRequest;
use WechatMiniProgramUrlLinkBundle\Service\PageService;
use WeuiBundle\Service\NoticeService;

/**
 * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/url-link/urllink.generate.html
 */
final class ShortLinkController extends AbstractController
{
    public function __construct(
        private readonly NoticeService $noticeService,
        private readonly PageService $pageService,
    ) {
    }

    /**
     * @see https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/url-link.html
     */
    #[Route(path: '/t.htm', name: 'wechat-mini-program-promotion-short-link', methods: ['GET'])]
    public function __invoke(
        Request $request,
        LoggerInterface $logger,
        PromotionCodeRepository $promotionCodeRepository,
        DoctrineService $doctrineService,
        Client $client,
    ): Response {
        $data = $this->extractParameterFromUri($request->getUri());
        if ('' === $data) {
            return $this->noticeService->weuiError('打开失败', '找不到参数');
        }

        $logger->info('短链参数', ['data' => $data]);

        $code = $promotionCodeRepository->findOneBy(['code' => $data]);
        if (null === $code) {
            return $this->noticeService->weuiError('打开失败', '找不到推广码');
        }

        $timeValidationError = $this->validateCodeTimeRange($code);
        if (null !== $timeValidationError) {
            return $timeValidationError;
        }

        $generateRequest = $this->createGenerateRequest($code);
        $validationError = $this->validateGenerateRequest($generateRequest, $code);
        if (null !== $validationError) {
            return $validationError;
        }

        $res = $client->request($generateRequest);
        $logger->info('短链生成成功', ['res' => $res, 'code' => $code]);

        if (!is_array($res) || !isset($res['url_link'])) {
            return $this->noticeService->weuiError('打开失败', '请返回重试');
        }

        /** @var array<string, mixed> $res */
        $urlLink = $this->createUrlLinkEntity($code, $generateRequest, $res);
        $doctrineService->asyncInsert($urlLink);

        $redirectUrl = $urlLink->getUrlLink();
        if (null === $redirectUrl) {
            return $this->noticeService->weuiError('打开失败', '链接生成错误');
        }

        return $this->redirect($redirectUrl);
    }

    private function extractParameterFromUri(string $uri): string
    {
        $questionMarkPos = strpos($uri, '?');
        $data = false !== $questionMarkPos ? substr($uri, $questionMarkPos) : '';
        $data = trim($data, '?');
        $data = trim($data, '=');

        return trim($data, '#');
    }

    private function validateCodeTimeRange(PromotionCode $code): ?Response
    {
        if (null === $code->getStartTime() || null === $code->getEndTime()) {
            return null;
        }

        $now = CarbonImmutable::now();
        if ($code->getStartTime() > $now || $code->getEndTime() < $now) {
            return $this->noticeService->weuiError('打开失败', '找不到推广码');
        }

        return null;
    }

    private function createGenerateRequest(PromotionCode $code): GenerateUrlLinkRequest
    {
        $path = $this->pageService->getRedirectPage();
        $query = "id={$code->getId()}";

        $generateRequest = new GenerateUrlLinkRequest();
        $generateRequest->setExpireType(1);
        $generateRequest->setExpireInterval(1);
        $generateRequest->setPath($path);
        $generateRequest->setQuery($query);

        return $generateRequest;
    }

    private function validateGenerateRequest(GenerateUrlLinkRequest $generateRequest, PromotionCode $code): ?Response
    {
        $account = $code->getAccount();
        if (!$account instanceof Account) {
            return $this->noticeService->weuiError('打开失败', '账户信息错误');
        }
        $generateRequest->setAccount($account);

        $envVersion = $code->getEnvVersion();
        if (null === $envVersion) {
            return $this->noticeService->weuiError('打开失败', '环境版本配置错误');
        }
        $generateRequest->setEnvVersion($envVersion->value);

        return null;
    }

    /**
     * @param array<string, mixed> $res
     */
    private function createUrlLinkEntity(PromotionCode $code, GenerateUrlLinkRequest $generateRequest, array $res): UrlLink
    {
        $urlLink = new UrlLink();
        $urlLink->setAccount($code->getAccount());
        $urlLink->setEnvVersion($code->getEnvVersion());
        $urlLink->setUrlLink(is_string($res['url_link']) ? $res['url_link'] : '');
        $urlLink->setPath($generateRequest->getPath());
        $urlLink->setQuery($generateRequest->getQuery());
        $urlLink->setRawData($generateRequest->getRequestOptions());
        $urlLink->setChecked(false);

        return $urlLink;
    }
}
