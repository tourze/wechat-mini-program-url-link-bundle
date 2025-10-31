<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidAccountException;
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

/**
 * @internal
 */
#[CoversClass(UrlLinkService::class)]
#[RunTestsInSeparateProcesses]
final class UrlLinkServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service test does not need specific setup
    }

    public function testServiceIsAvailable(): void
    {
        $service = self::getContainer()->get(UrlLinkService::class);

        $this->assertInstanceOf(UrlLinkService::class, $service);
    }

    public function testApiCheckWithInvalidAccount(): void
    {
        $service = self::getContainer()->get(UrlLinkService::class);
        $this->assertInstanceOf(UrlLinkService::class, $service);

        $urlLink = new UrlLink();
        $urlLink->setUrlLink('https://example.com/test');

        $this->expectException(InvalidAccountException::class);
        $service->apiCheck($urlLink);
    }
}
