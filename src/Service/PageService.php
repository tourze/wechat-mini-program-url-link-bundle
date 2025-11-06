<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

class PageService
{
    private string $redirectPath;

    public function __construct(?string $redirectPath = null)
    {
        $this->redirectPath = $redirectPath ?? 'pages/redirect/index';
    }

    public function getRedirectPage(): string
    {
        $basePath = trim($this->redirectPath, '/');

        return "/{$basePath}";
    }
}
