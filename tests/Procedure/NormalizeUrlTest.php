<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Procedure\GetWechatMiniProgramPromotionCodeInfo;

/**
 * 测试URL归一化功能
 */
#[CoversClass(GetWechatMiniProgramPromotionCodeInfo::class)]
final class NormalizeUrlTest extends TestCase
{
    private GetWechatMiniProgramPromotionCodeInfo $procedure;

    protected function setUp(): void
    {
        // 创建一个最小化的实例用于测试normalizeUrl方法
        $this->procedure = $this->getMockBuilder(GetWechatMiniProgramPromotionCodeInfo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['execute'])
            ->getMock();
    }

    /**
     * 使用反射来测试私有方法normalizeUrl
     */
    public function testNormalizeUrl(): void
    {
        $reflection = new \ReflectionClass($this->procedure);
        $method = $reflection->getMethod('normalizeUrl');
        $method->setAccessible(true);

        // 测试完整URL应该保持不变
        $this->assertEquals(
            'https://example.com',
            $method->invoke($this->procedure, 'https://example.com')
        );

        $this->assertEquals(
            'http://example.com/path',
            $method->invoke($this->procedure, 'http://example.com/path')
        );

        // 测试相对路径应该被处理
        $this->assertEquals(
            '/pages/index/index',
            $method->invoke($this->procedure, 'pages/index/index')
        );

        $this->assertEquals(
            '/pages/index/index',
            $method->invoke($this->procedure, '/pages/index/index')
        );

        $this->assertEquals(
            '/pages/index/index',
            $method->invoke($this->procedure, '//pages/index/index//')
        );

        // 测试空字符串
        $this->assertEquals(
            '/',
            $method->invoke($this->procedure, '')
        );
    }
}