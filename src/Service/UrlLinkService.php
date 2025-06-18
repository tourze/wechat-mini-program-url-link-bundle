<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Request\QueryUrlLinkRequest;

class UrlLinkService
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly EntityManagerInterface $entityManager,
        private readonly Client $client,
    ) {
    }

    /**
     * 检查UrlLink的访问情况，需要调用微信接口去确认
     */
    public function apiCheck(UrlLink $urlLink): void
    {
        $request = new QueryUrlLinkRequest();
        $request->setAccount($urlLink->getAccount());
        $request->setUrlLink($urlLink->getUrlLink());

        try {
            $response = $this->client->request($request);
        } catch (\Throwable $exception) {
            $this->logger->warning('查找短链状态时报错，可能还未有人访问或已过期', [
                'exception' => $exception,
                'urlLink' => $urlLink,
            ]);

            return;
        }

        if ((bool) isset($response['visit_openid'])) {
            $urlLink->setVisitOpenId($response['visit_openid']);
        }

        $urlLink->setChecked(true);
        $this->entityManager->persist($urlLink);
        $this->entityManager->flush();
    }
}
