<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Procedure;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tourze\DoctrineAsyncInsertBundle\Service\AsyncInsertService;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Procedure\GetWechatMiniProgramPromotionCodeInfo;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

class GetWechatMiniProgramPromotionCodeInfoTest extends TestCase
{
    private GetWechatMiniProgramPromotionCodeInfo $procedure;
    private PromotionCodeRepository $promotionCodeRepository;
    private AsyncInsertService $doctrineService;
    private Security $security;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->promotionCodeRepository = $this->createMock(PromotionCodeRepository::class);
        $this->doctrineService = $this->createMock(AsyncInsertService::class);
        $this->security = $this->createMock(Security::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->procedure = new GetWechatMiniProgramPromotionCodeInfo(
            $this->promotionCodeRepository,
            $this->doctrineService,
            $this->security,
            $this->eventDispatcher,
            $this->logger
        );
    }

    public function testIsProcedure(): void
    {
        $this->assertInstanceOf(BaseProcedure::class, $this->procedure);
    }

    public function testExecuteWithNonExistentCode(): void
    {
        $this->procedure->id = 999;
        
        $this->promotionCodeRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);
        
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('找不到推广码');
        
        $this->procedure->execute();
    }

    public function testExecuteWithValidCode(): void
    {
        $this->procedure->id = 1;
        
        $promotionCode = new PromotionCode();
        $promotionCode->setValid(true);
        $promotionCode->setLinkUrl('pages/test/index');
        $promotionCode->setForceLogin(false);
        
        $this->promotionCodeRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($promotionCode);
        
        $this->security->expects($this->exactly(2))
            ->method('getUser')
            ->willReturn(null);
        
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch');
        
        $this->doctrineService->expects($this->once())
            ->method('asyncInsert');
        
        $result = $this->procedure->execute();
        
        $this->assertArrayHasKey('forceLogin', $result);
        $this->assertFalse($result['forceLogin']);
    }
}