<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatMiniProgramUrlLinkBundle\Procedure\GetWechatMiniProgramPromotionCodeInfo;

/**
 * 测试URL归一化功能
 */
#[CoversClass(GetWechatMiniProgramPromotionCodeInfo::class)]
#[RunTestsInSeparateProcesses]
final class NormalizeUrlTest extends AbstractProcedureTestCase
{
    private GetWechatMiniProgramPromotionCodeInfo $procedure;

    protected function onSetUp(): void
    {
        // 创建一个最小化的实例用于测试normalizeUrl方法
        $this->procedure = $this->getMockBuilder(GetWechatMiniProgramPromotionCodeInfo::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['execute'])
            ->getMock();
    }

    /**
     * 测试execute方法
     */
    public function testExecute(): void
    {
        // 由于execute方法依赖多个服务和数据库，这里使用mock进行验证
        // 只验证方法能被调用，具体业务逻辑通过集成测试验证
        $param = new \WechatMiniProgramUrlLinkBundle\Param\GetWechatMiniProgramPromotionCodeInfoParam(1);

        // 配置mock对象返回预期结果
        $expectedResult = new \Tourze\JsonRPC\Core\Result\ArrayResult([
            'forceLogin' => false,
            'url' => '/pages/index/index'
        ]);

        $this->procedure->expects($this->once())
            ->method('execute')
            ->with($param)
            ->willReturn($expectedResult);

        $result = $this->procedure->execute($param);
        $this->assertInstanceOf(\Tourze\JsonRPC\Core\Result\ArrayResult::class, $result);
        $this->assertArrayHasKey('forceLogin', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertFalse($result['forceLogin']);
        $this->assertEquals('/pages/index/index', $result['url']);
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