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
    public function testGetRedirectPageWithDefaultPath(): void
    {
        $pageService = new PageService();
        $result = $pageService->getRedirectPage();

        self::assertSame('/pages/redirect/index', $result);
    }

    public function testGetRedirectPageWithCustomPath(): void
    {
        $pageService = new PageService('custom/path');
        $result = $pageService->getRedirectPage();

        self::assertSame('/custom/path', $result);
    }

    public function testGetRedirectPageWithPathWithSlashes(): void
    {
        $pageService = new PageService('/custom/path/');
        $result = $pageService->getRedirectPage();

        self::assertSame('/custom/path', $result);
    }

    public function testGetRedirectPageWithEmptyPath(): void
    {
        $pageService = new PageService('');
        $result = $pageService->getRedirectPage();

        self::assertSame('/', $result);
    }
}
