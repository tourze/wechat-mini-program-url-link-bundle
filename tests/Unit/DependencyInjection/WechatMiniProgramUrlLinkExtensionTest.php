<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use WechatMiniProgramUrlLinkBundle\DependencyInjection\WechatMiniProgramUrlLinkExtension;

class WechatMiniProgramUrlLinkExtensionTest extends TestCase
{
    private WechatMiniProgramUrlLinkExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatMiniProgramUrlLinkExtension();
        $this->container = new ContainerBuilder();
    }

    public function testIsExtension(): void
    {
        $this->assertInstanceOf(Extension::class, $this->extension);
    }

    public function testLoad(): void
    {
        $configs = [];
        
        $this->extension->load($configs, $this->container);
        
        $this->assertTrue(true);
    }
}