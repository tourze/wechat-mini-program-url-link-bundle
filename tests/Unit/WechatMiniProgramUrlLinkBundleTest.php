<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatMiniProgramUrlLinkBundle\WechatMiniProgramUrlLinkBundle;

class WechatMiniProgramUrlLinkBundleTest extends TestCase
{
    public function testBundleCreation(): void
    {
        $bundle = new WechatMiniProgramUrlLinkBundle();
        
        $this->assertInstanceOf(Bundle::class, $bundle);
        $this->assertInstanceOf(WechatMiniProgramUrlLinkBundle::class, $bundle);
    }
}