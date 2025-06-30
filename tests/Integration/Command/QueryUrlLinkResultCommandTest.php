<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Command;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use WechatMiniProgramUrlLinkBundle\Command\QueryUrlLinkResultCommand;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

class QueryUrlLinkResultCommandTest extends TestCase
{
    public function testCommandCreation(): void
    {
        $linkRepository = $this->createMock(UrlLinkRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $urlLinkService = $this->createMock(UrlLinkService::class);
        
        $command = new QueryUrlLinkResultCommand(
            $linkRepository,
            $entityManager,
            $urlLinkService
        );
        
        $this->assertInstanceOf(Command::class, $command);
        $this->assertSame(QueryUrlLinkResultCommand::NAME, $command->getName());
    }

    public function testCommandExecution(): void
    {
        $linkRepository = $this->createMock(UrlLinkRepository::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $urlLinkService = $this->createMock(UrlLinkService::class);
        
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $query = $this->createMock(\Doctrine\ORM\Query::class);
        
        $linkRepository->expects($this->once())
            ->method('createQueryBuilder')
            ->willReturn($queryBuilder);
        
        $queryBuilder->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('orderBy')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('setMaxResults')
            ->willReturnSelf();
        
        $queryBuilder->expects($this->once())
            ->method('getQuery')
            ->willReturn($query);
        
        $query->expects($this->once())
            ->method('toIterable')
            ->willReturn([]);
        
        $command = new QueryUrlLinkResultCommand(
            $linkRepository,
            $entityManager,
            $urlLinkService
        );
        
        $application = new Application();
        $application->add($command);
        
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
        
        $this->assertSame(Command::SUCCESS, $commandTester->getStatusCode());
    }
}