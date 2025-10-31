<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;
use WechatMiniProgramUrlLinkBundle\Repository\VisitLogRepository;

/**
 * @internal
 */
#[CoversClass(VisitLogRepository::class)]
#[RunTestsInSeparateProcesses]
final class VisitLogRepositoryTest extends AbstractRepositoryTestCase
{
    private VisitLogRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(VisitLogRepository::class);
        $this->assertInstanceOf(VisitLogRepository::class, $repository);
        $this->repository = $repository;
    }

    protected function createNewEntity(): object
    {
        $promotionCode = new PromotionCode();
        $promotionCode->setName('Test Promotion Code ' . uniqid());
        $promotionCode->setLinkUrl('https://example.com/test/' . uniqid());
        $promotionCode->setCode(uniqid());
        self::getEntityManager()->persist($promotionCode);
        self::getEntityManager()->flush();

        $visitLog = new VisitLog();
        $visitLog->setCode($promotionCode);
        $visitLog->setEnvVersion(EnvVersion::RELEASE);

        return $visitLog;
    }

    protected function onTearDown(): void
    {
    }

    protected function getRepository(): VisitLogRepository
    {
        return $this->repository;
    }

    public function testRepositoryCanAccessEntityManager(): void
    {
        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(\Doctrine\ORM\EntityManagerInterface::class, $entityManager);
    }

    public function testRepositoryIsService(): void
    {
        $this->assertInstanceOf(VisitLogRepository::class, $this->repository);
    }
}
