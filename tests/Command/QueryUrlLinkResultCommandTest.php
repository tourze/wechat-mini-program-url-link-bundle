<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;
use WechatMiniProgramUrlLinkBundle\Command\QueryUrlLinkResultCommand;

/**
 * @internal
 */
#[CoversClass(QueryUrlLinkResultCommand::class)]
#[RunTestsInSeparateProcesses]
final class QueryUrlLinkResultCommandTest extends AbstractCommandTestCase
{
    private ?CommandTester $commandTester = null;

    protected function onSetUp(): void        // Command 测试的初始化逻辑
    {
    }

    protected function getCommandTester(): CommandTester
    {
        if (null === $this->commandTester) {
            $command = self::getContainer()->get(QueryUrlLinkResultCommand::class);
            $this->assertInstanceOf(QueryUrlLinkResultCommand::class, $command);
            $application = new Application();
            $application->addCommand($command);
            $this->commandTester = new CommandTester($command);
        }

        return $this->commandTester;
    }

    public function testCommandCreation(): void
    {
        $command = self::getContainer()->get(QueryUrlLinkResultCommand::class);

        $this->assertInstanceOf(QueryUrlLinkResultCommand::class, $command);
        $this->assertSame(QueryUrlLinkResultCommand::NAME, $command->getName());
    }

    public function testCommandExecution(): void
    {
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $entityManager->createQuery('DELETE FROM WechatMiniProgramUrlLinkBundle\Entity\UrlLink')->execute();

        $command = self::getContainer()->get(QueryUrlLinkResultCommand::class);
        $this->assertInstanceOf(QueryUrlLinkResultCommand::class, $command);

        $application = new Application();
        $application->addCommand($command);

        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testArgumentLimit(): void
    {
        // Clean database first to avoid account-related exceptions
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $entityManager->createQuery('DELETE FROM WechatMiniProgramUrlLinkBundle\Entity\UrlLink')->execute();

        $commandTester = $this->getCommandTester();

        // Test with custom limit
        $commandTester->execute(['limit' => '100']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        // Test with default limit (should work without specifying limit)
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }

    public function testArgumentMinute(): void
    {
        // Clean database first to avoid account-related exceptions
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(EntityManagerInterface::class, $entityManager);
        $entityManager->createQuery('DELETE FROM WechatMiniProgramUrlLinkBundle\Entity\UrlLink')->execute();

        $commandTester = $this->getCommandTester();

        // Test with custom minute
        $commandTester->execute(['minute' => '30']);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());

        // Test with default minute (should work without specifying minute)
        $commandTester->execute([]);

        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}
