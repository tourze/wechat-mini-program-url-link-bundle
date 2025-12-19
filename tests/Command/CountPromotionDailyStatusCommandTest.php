<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Command;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramUrlLinkBundle\Command\CountPromotionDailyStatusCommand;

/**
 * @internal
 */
#[CoversClass(CountPromotionDailyStatusCommand::class)]
#[RunTestsInSeparateProcesses]
final class CountPromotionDailyStatusCommandTest extends AbstractCommandTestCase
{
    private ?CommandTester $commandTester = null;

    protected function onSetUp(): void        // Command 测试的初始化逻辑
    {
    }

    protected function getCommandTester(): CommandTester
    {
        if (null === $this->commandTester) {
            $command = self::getContainer()->get(CountPromotionDailyStatusCommand::class);
            $this->assertInstanceOf(CountPromotionDailyStatusCommand::class, $command);
            $application = new Application();
            $application->addCommand($command);
            $this->commandTester = new CommandTester($command);
        }

        return $this->commandTester;
    }

    public function testCommandCreation(): void
    {
        $command = self::getContainer()->get(CountPromotionDailyStatusCommand::class);

        $this->assertInstanceOf(CountPromotionDailyStatusCommand::class, $command);
        $this->assertSame(CountPromotionDailyStatusCommand::NAME, $command->getName());
    }

    public function testCommandExecution(): void
    {
        $command = self::getContainer()->get(CountPromotionDailyStatusCommand::class);
        $this->assertInstanceOf(CountPromotionDailyStatusCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
