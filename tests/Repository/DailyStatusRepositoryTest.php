<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Repository\DailyStatusRepository;

/**
 * @internal
 */
#[CoversClass(DailyStatusRepository::class)]
#[RunTestsInSeparateProcesses]
final class DailyStatusRepositoryTest extends AbstractRepositoryTestCase
{
    private DailyStatusRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(DailyStatusRepository::class);
        $this->assertInstanceOf(DailyStatusRepository::class, $repository);
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

        $dailyStatus = new DailyStatus();
        $dailyStatus->setCode($promotionCode);
        $dailyStatus->setDate(new \DateTimeImmutable());
        $dailyStatus->setTotal(10);

        return $dailyStatus;
    }

    protected function onTearDown(): void
    {
    }

    protected function getRepository(): DailyStatusRepository
    {
        return $this->repository;
    }

    public function testRepositoryIsService(): void
    {
        $this->assertInstanceOf(DailyStatusRepository::class, $this->repository);
    }
}
