<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Repository;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;

/**
 * @internal
 */
#[CoversClass(UrlLinkRepository::class)]
#[RunTestsInSeparateProcesses]
final class UrlLinkRepositoryTest extends AbstractRepositoryTestCase
{
    private UrlLinkRepository $repository;

    protected function onSetUp(): void
    {
        $repository = self::getContainer()->get(UrlLinkRepository::class);
        $this->assertInstanceOf(UrlLinkRepository::class, $repository);
        $this->repository = $repository;
    }

    protected function createNewEntity(): object
    {
        $urlLink = new UrlLink();
        $urlLink->setUrlLink('https://example.com/test/' . uniqid());

        return $urlLink;
    }

    protected function onTearDown(): void
    {
    }

    protected function getRepository(): UrlLinkRepository
    {
        return $this->repository;
    }

    public function testRepositoryIsService(): void
    {
        $this->assertInstanceOf(UrlLinkRepository::class, $this->repository);
    }
}
