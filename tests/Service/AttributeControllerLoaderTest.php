<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Routing\RouteCollection;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramUrlLinkBundle\Service\AttributeControllerLoader;

/**
 * @internal
 */
#[CoversClass(AttributeControllerLoader::class)]
#[RunTestsInSeparateProcesses]
final class AttributeControllerLoaderTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service test does not need specific setup
    }

    public function testServiceIsAvailable(): void
    {
        $service = self::getContainer()->get(AttributeControllerLoader::class);

        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoload(): void
    {
        $service = self::getContainer()->get(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
        $result = $service->autoload();

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testLoad(): void
    {
        $service = self::getContainer()->get(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
        $result = $service->load('WechatMiniProgramUrlLinkBundle\Controller\\');

        $this->assertInstanceOf(RouteCollection::class, $result);
    }

    public function testSupports(): void
    {
        $service = self::getContainer()->get(AttributeControllerLoader::class);
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
        $result = $service->supports('test-resource');

        $this->assertFalse($result);
    }
}
