<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

class PageService
{
    public function getRedirectPage(): string
    {
        $envValue = $_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH'] ?? 'pages/redirect/index';
        $basePath = is_string($envValue) ? trim($envValue, '/') : 'pages/redirect/index'; // 兼容写错的情况

        return "/{$basePath}";
    }
}
