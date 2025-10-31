<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Service\PageService;

/**
 * @internal
 */
#[CoversClass(PageService::class)]
final class PageServiceTest extends TestCase
{
    private PageService $pageService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pageService = new PageService();
    }

    public function testGetRedirectPageWithDefaultPath(): void
    {
        // 清除环境变量
        unset($_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH']);

        $result = $this->pageService->getRedirectPage();

        self::assertSame('/pages/redirect/index', $result);
    }

    public function testGetRedirectPageWithCustomPath(): void
    {
        $_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH'] = 'custom/path';

        $result = $this->pageService->getRedirectPage();

        self::assertSame('/custom/path', $result);

        // 清理环境变量
        unset($_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH']);
    }

    public function testGetRedirectPageWithPathWithSlashes(): void
    {
        $_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH'] = '/custom/path/';

        $result = $this->pageService->getRedirectPage();

        self::assertSame('/custom/path', $result);

        // 清理环境变量
        unset($_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH']);
    }

    public function testGetRedirectPageWithEmptyPath(): void
    {
        $_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH'] = '';

        $result = $this->pageService->getRedirectPage();

        self::assertSame('/', $result);

        // 清理环境变量
        unset($_ENV['WECHAT_MINI_PROGRAM_PROMOTION_REDIRECT_PATH']);
    }
}
