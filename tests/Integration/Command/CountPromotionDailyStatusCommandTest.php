<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use WechatMiniProgramUrlLinkBundle\Command\CountPromotionDailyStatusCommand;
use WechatMiniProgramUrlLinkBundle\Repository\DailyStatusRepository;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Repository\VisitLogRepository;

class CountPromotionDailyStatusCommandTest extends TestCase
{
    public function testCommandCreation(): void
    {
        $visitLogRepository = $this->createMock(VisitLogRepository::class);
        $codeRepository = $this->createMock(PromotionCodeRepository::class);
        $dailyStatusRepository = $this->createMock(DailyStatusRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $command = new CountPromotionDailyStatusCommand(
            $visitLogRepository,
            $codeRepository,
            $dailyStatusRepository,
            $entityManager
        );
        
        $this->assertInstanceOf(Command::class, $command);
        $this->assertSame(CountPromotionDailyStatusCommand::NAME, $command->getName());
    }

    public function testCommandExecution(): void
    {
        $visitLogRepository = $this->createMock(VisitLogRepository::class);
        $codeRepository = $this->createMock(PromotionCodeRepository::class);
        $dailyStatusRepository = $this->createMock(DailyStatusRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $visitLogRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->once())
            ->method('select')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->exactly(2))
            ->method('setParameter')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('groupBy')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->once())
            ->method('getResult')
            ->willReturn([]);
        
        $command = new CountPromotionDailyStatusCommand(
            $visitLogRepository,
            $codeRepository,
            $dailyStatusRepository,
            $entityManager
        );
        
        $application = new Application();
        $application->add($command);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}