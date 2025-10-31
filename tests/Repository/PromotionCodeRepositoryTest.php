<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

/**
 * @internal
 */
#[CoversClass(PromotionCodeRepository::class)]
#[RunTestsInSeparateProcesses]
final class PromotionCodeRepositoryTest extends AbstractRepositoryTestCase
{
    private PromotionCodeRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(PromotionCodeRepository::class);
        $this->assertInstanceOf(PromotionCodeRepository::class, $repository);
        $this->repository = $repository;
    }

    protected function createNewEntity(): object
    {
        $entity = new PromotionCode();
        $entity->setName('Test Promotion Code ' . uniqid());
        $entity->setCode('code_' . uniqid());
        $entity->setLinkUrl('https://example.com/test');

        return $entity;
    }

    protected function getRepository(): PromotionCodeRepository
    {
        return $this->repository;
    }

    public function testRepositoryIsService(): void
    {
        $this->assertInstanceOf(PromotionCodeRepository::class, $this->repository);
    }
}
