<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Psr\Link\LinkInterface;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramUrlLinkBundle\Service\PathParser;

/**
 * @internal
 */
#[CoversClass(PathParser::class)]
#[RunTestsInSeparateProcesses]
final class PathParserTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service test does not need specific setup
    }

    public function testServiceIsAvailable(): void
    {
        $service = self::getContainer()->get(PathParser::class);

        $this->assertInstanceOf(PathParser::class, $service);
    }

    public function testParsePathWithRegularPath(): void
    {
        $service = self::getContainer()->get(PathParser::class);
        $this->assertInstanceOf(PathParser::class, $service);

        $path = 'pages/index/index';
        $query = ['foo' => 'bar'];

        $result = $service->parsePath($path, $query);

        $this->assertInstanceOf(LinkInterface::class, $result);
    }

    public function testParsePathWithRedirectPath(): void
    {
        $service = self::getContainer()->get(PathParser::class);
        $this->assertInstanceOf(PathParser::class, $service);

        $path = 'pages/redirect/index';
        $query = ['scene' => '999'];

        $result = $service->parsePath($path, $query);

        $this->assertInstanceOf(LinkInterface::class, $result);
    }
}
