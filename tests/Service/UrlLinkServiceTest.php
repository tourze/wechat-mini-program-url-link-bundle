<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Service\Client;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Request\QueryUrlLinkRequest;
use WechatMiniProgramUrlLinkBundle\Service\UrlLinkService;

class UrlLinkServiceTest extends TestCase
{
    private MockObject|LoggerInterface $logger;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|Client $client;
    private UrlLinkService $service;
    private UrlLink $urlLink;
    private MockObject|Account $account;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->client = $this->createMock(Client::class);
        $this->account = $this->createMock(Account::class);
        $this->service = new UrlLinkService($this->logger, $this->entityManager, $this->client);
        
        // 创建 UrlLink 对象用于测试
        $this->urlLink = new UrlLink();
        $this->urlLink->setAccount($this->account);
        $this->urlLink->setUrlLink('https://example.com/test-url-link');
    }

    /**
     * 测试成功检查 URL Link 并更新访问者 OpenID
     */
    public function testApiCheck_withValidResponse_updatesVisitOpenId(): void
    {
        // 模拟 API 调用响应
        $response = ['visit_openid' => 'test_open_id_123'];
        
        // 配置 Client 模拟对象的行为
        $this->client->expects($this->once())
            ->method('request')
            ->with($this->callback(function (QueryUrlLinkRequest $request) {
                return $request->getUrlLink() === 'https://example.com/test-url-link';
            }))
            ->willReturn($response);
            
        // 验证 EntityManager 的方法调用
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (UrlLink $urlLink) {
                return $urlLink->getVisitOpenId() === 'test_open_id_123' && $urlLink->isChecked() === true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        // 执行测试
        $this->service->apiCheck($this->urlLink);
        
        // 验证实体状态
        $this->assertEquals('test_open_id_123', $this->urlLink->getVisitOpenId());
        $this->assertTrue($this->urlLink->isChecked());
    }
    
    /**
     * 测试API调用异常情况
     */
    public function testApiCheck_whenApiThrowsException_logsWarning(): void
    {
        // 模拟 API 调用抛出异常
        $exception = new \Exception('API error');
        $this->client->expects($this->once())
            ->method('request')
            ->willThrowException($exception);
            
        // 验证日志记录
        $this->logger->expects($this->once())
            ->method('warning')
            ->with(
                $this->equalTo('查找短链状态时报错，可能还未有人访问或已过期'),
                $this->callback(function (array $context) use ($exception) {
                    return $context['exception'] === $exception && $context['urlLink'] === $this->urlLink;
                })
            );
            
        // EntityManager 不应该被调用
        $this->entityManager->expects($this->never())->method('persist');
        $this->entityManager->expects($this->never())->method('flush');
            
        // 执行测试
        $this->service->apiCheck($this->urlLink);
        
        // URL Link 的状态不应该改变
        $this->assertNull($this->urlLink->getVisitOpenId());
        $this->assertFalse($this->urlLink->isChecked());
    }
    
    /**
     * 测试无 visit_openid 的响应情况
     */
    public function testApiCheck_withResponseWithoutVisitOpenId_onlySetsCheckedFlag(): void
    {
        // 模拟 API 调用响应，但没有 visit_openid
        $response = ['some_other_data' => 'value'];
        
        // 配置 Client 模拟对象的行为
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($response);
            
        // 验证 EntityManager 的方法调用
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function (UrlLink $urlLink) {
                return $urlLink->getVisitOpenId() === null && $urlLink->isChecked() === true;
            }));
            
        $this->entityManager->expects($this->once())
            ->method('flush');
            
        // 执行测试
        $this->service->apiCheck($this->urlLink);
        
        // 验证实体状态
        $this->assertNull($this->urlLink->getVisitOpenId());
        $this->assertTrue($this->urlLink->isChecked());
    }
}