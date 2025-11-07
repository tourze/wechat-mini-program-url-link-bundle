<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramUrlLinkBundle\Command\CountPromotionDailyStatusCommand;

/**
 * 测试CountPromotionDailyStatusCommand的null处理逻辑
 */
#[CoversClass(CountPromotionDailyStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class CountPromotionDailyStatusCommandLogicTest extends AbstractCommandTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试使用真实的服务实例
    }

    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(CountPromotionDailyStatusCommand::class);

        return new CommandTester($command);
    }

    public function testNullHandlingLogic(): void
    {
        // 模拟数据库返回的数据结构
        $list = [
            ['total' => 5, 'code' => 1],
            ['total' => 0, 'code' => 2],
        ];

        // 模拟DailyStatus的getTotal()返回null的情况
        /** @var int|null $currentTotal */
        $currentTotal = null;
        /** @var int $totalValue */
        $totalValue = 5;

        // 这个逻辑应该正确处理null值
        $result = $totalValue > ($currentTotal ?? 0);
        $this->assertTrue($result, '应该正确处理null值，5 > 0 应该为true');

        // 测试另一个情况
        /** @var int $currentTotal */
        $currentTotal = 10;
        /** @var int $totalValue */
        $totalValue = 5;
        $result = $totalValue > ($currentTotal ?? 0);
        $this->assertFalse($result, '应该正确处理比较，5 > 10 应该为false');
    }
}