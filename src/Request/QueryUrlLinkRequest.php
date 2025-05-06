<?php

namespace WechatMiniProgramUrlLinkBundle\Request;

use WechatMiniProgramBundle\Request\WithAccountRequest;

class QueryUrlLinkRequest extends WithAccountRequest
{
    /**
     * @var string 要检查的地址
     */
    private string $urlLink;

    public function getRequestPath(): string
    {
        return '/wxa/query_urllink';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => [
                'url_link' => $this->getUrlLink(),
            ],
        ];
    }

    public function getUrlLink(): string
    {
        return $this->urlLink;
    }

    public function setUrlLink(string $urlLink): void
    {
        $this->urlLink = $urlLink;
    }
}
