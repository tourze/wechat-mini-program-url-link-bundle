<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Controller;

use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use WechatMiniProgramUrlLinkBundle\Controller\ShortLinkController;
use WeuiBundle\Service\NoticeService;

class ShortLinkControllerTest extends TestCase
{
    public function testControllerCreation(): void
    {
        $noticeService = $this->createMock(NoticeService::class);
        
        $controller = new ShortLinkController($noticeService);
        
        $this->assertInstanceOf(AbstractController::class, $controller);
        $this->assertInstanceOf(ShortLinkController::class, $controller);
    }
}