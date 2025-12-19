<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Procedure;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\PHPUnitJsonRPC\AbstractProcedureTestCase;
use WechatMiniProgramUrlLinkBundle\Procedure\GetWechatMiniProgramPromotionCodeInfo;

/**
 * @internal
 */
#[CoversClass(GetWechatMiniProgramPromotionCodeInfo::class)]
#[RunTestsInSeparateProcesses]
final class GetWechatMiniProgramPromotionCodeInfoTest extends AbstractProcedureTestCase
{
    protected function getProcedureClass(): string
    {
        return GetWechatMiniProgramPromotionCodeInfo::class;
    }

    protected function onSetUp(): void
    {
        // 集成测试设置
    }

    public function testProcedureIsRegistered(): void
    {
        $procedure = self::getService(GetWechatMiniProgramPromotionCodeInfo::class);
        $this->assertInstanceOf(GetWechatMiniProgramPromotionCodeInfo::class, $procedure);
    }

    public function testExecuteWithInvalidId(): void
    {
        $procedure = self::getService(GetWechatMiniProgramPromotionCodeInfo::class);

        // 创建参数对象
        $param = new \WechatMiniProgramUrlLinkBundle\Param\GetWechatMiniProgramPromotionCodeInfoParam(999999);

        try {
            $procedure->execute($param);
            self::fail('Expected exception was not thrown');
        } catch (\Exception $e) {
            // 在集成测试中，可能抛出数据库异常或API异常
            $this->assertTrue(
                $e instanceof ApiException || $e instanceof \Doctrine\DBAL\Exception,
                'Expected ApiException or Doctrine Exception, got: ' . get_class($e)
            );
        }
    }
}
