<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidAccountException;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidRequestParameterException;
use WechatMiniProgramUrlLinkBundle\Request\QueryUrlLinkRequest;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'wechat_mini_program_url_link')]
readonly class UrlLinkService
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager,
        private Client $client,
    ) {
    }

    /**
     * 检查UrlLink的访问情况，需要调用微信接口去确认
     */
    public function apiCheck(UrlLink $urlLink): void
    {
        $request = new QueryUrlLinkRequest();
        $account = $urlLink->getAccount();
        if (!$account instanceof Account) {
            throw new InvalidAccountException();
        }
        $request->setAccount($account);
        $urlLinkValue = $urlLink->getUrlLink();
        if (null === $urlLinkValue) {
            throw new InvalidRequestParameterException('URL Link 值不能为空');
        }
        $request->setUrlLink($urlLinkValue);

        try {
            $response = $this->client->request($request);
        } catch (\Throwable $exception) {
            $this->logger->warning('查找短链状态时报错，可能还未有人访问或已过期', [
                'exception' => $exception,
                'urlLink' => $urlLink,
            ]);

            return;
        }

        if (is_array($response) && isset($response['visit_openid']) && is_string($response['visit_openid'])) {
            $urlLink->setVisitOpenId($response['visit_openid']);
        }

        $urlLink->setChecked(true);
        $this->entityManager->persist($urlLink);
        $this->entityManager->flush();
    }
}
