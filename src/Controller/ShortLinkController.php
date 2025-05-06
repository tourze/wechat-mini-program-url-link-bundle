<?php

namespace WechatMiniProgramUrlLinkBundle\Controller;

use Carbon\Carbon;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Tourze\DoctrineAsyncBundle\Service\DoctrineService;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Request\GenerateUrlLinkRequest;
use WeuiBundle\Service\NoticeService;

/**
 * @see https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/url-link/urllink.generate.html
 */
class ShortLinkController extends AbstractController
{
    public function __construct(private readonly NoticeService $noticeService)
    {
    }

    /**
     * @see https://developers.weixin.qq.com/miniprogram/dev/framework/open-ability/url-link.html
     */
    #[Route('/t.htm', name: 'wechat-mini-program-promotion-short-link')]
    public function revokeCallback(
        Request $request,
        LoggerInterface $logger,
        PromotionCodeRepository $promotionCodeRepository,
        DoctrineService $doctrineService,
        Client $client,
    ): Response {
        $data = mb_substr($request->getUri(), mb_strpos($request->getUri(), '?'));
        $data = trim($data, '?');
        $data = trim($data, '=');
        $data = trim($data, '#');
        if (empty($data)) {
            return $this->noticeService->weuiError('打开失败', '找不到参数');
        }
        $logger->info('短链参数', [
            'data' => $data,
        ]);

        $code = $promotionCodeRepository->findOneBy(['code' => $data]);
        if (!$code) {
            return $this->noticeService->weuiError('打开失败', '找不到推广码');
        }

        // 活动时间内有效
        if (!empty($code->getStartTime()) && !empty($code->getEndTime())) {
            $now = Carbon::now();
            if ($code->getStartTime() > $now || $code->getEndTime() < $now) {
                return $this->noticeService->weuiError('打开失败', '找不到推广码');
            }
        }

        // 因为 code2session 的逻辑是放在中转页面做的，所以最好我们还是别直接跳转走，经一次中转页比较好
        $basePath = $_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH'] ?? 'pages/redirect/index';
        $basePath = trim($basePath, '/'); // 兼容写错的情况

        $path = "/{$basePath}";
        $query = "id={$code->getId()}";

        $generateRequest = new GenerateUrlLinkRequest();
        $generateRequest->setAccount($code->getAccount());
        $generateRequest->setEnvVersion($code->getEnvVersion()->value);
        $generateRequest->setExpireType(1);
        $generateRequest->setExpireInterval(1);
        $generateRequest->setPath($path);
        $generateRequest->setQuery($query);
        $res = $client->request($generateRequest);

        $logger->info('短链生成成功', [
            'res' => $res,
            'code' => $code,
        ]);

        if (!isset($res['url_link'])) {
            // 生成失败原因很多，可能是路径不存在，或小程序未上线
            return $this->noticeService->weuiError('打开失败', '请返回重试');
        }

        $urlLink = new UrlLink();
        $urlLink->setAccount($code->getAccount());
        $urlLink->setEnvVersion($code->getEnvVersion());
        $urlLink->setUrlLink($res['url_link']);
        $urlLink->setPath($path);
        $urlLink->setQuery($query);
        $urlLink->setRawData($generateRequest->getRequestOptions());
        $urlLink->setChecked(false);
        $doctrineService->asyncInsert($urlLink);

        return $this->redirect($urlLink->getUrlLink());
    }
}
