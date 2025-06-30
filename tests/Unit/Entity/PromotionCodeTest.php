<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRule;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;

class PromotionCodeTest extends TestCase
{
    private PromotionCode $promotionCode;

    protected function setUp(): void
    {
        $this->promotionCode = new PromotionCode();
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->promotionCode->getVisitLogs());
        $this->assertInstanceOf(ArrayCollection::class, $this->promotionCode->getRules());
        $this->assertCount(0, $this->promotionCode->getVisitLogs());
        $this->assertCount(0, $this->promotionCode->getRules());
    }

    public function testIdGetter(): void
    {
        $this->assertSame(0, $this->promotionCode->getId());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->assertSame('', $this->promotionCode->getCode());
        
        $code = 'TEST123';
        $this->promotionCode->setCode($code);
        $this->assertSame($code, $this->promotionCode->getCode());
    }

    public function testNameGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getName());
        
        $name = 'Test Name';
        $this->promotionCode->setName($name);
        $this->assertSame($name, $this->promotionCode->getName());
    }

    public function testLinkUrlGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getLinkUrl());
        
        $url = 'https://example.com';
        $this->promotionCode->setLinkUrl($url);
        $this->assertSame($url, $this->promotionCode->getLinkUrl());
    }

    public function testImageUrlGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getImageUrl());
        
        $imageUrl = 'https://example.com/image.jpg';
        $this->promotionCode->setImageUrl($imageUrl);
        $this->assertSame($imageUrl, $this->promotionCode->getImageUrl());
        
        $this->promotionCode->setImageUrl(null);
        $this->assertNull($this->promotionCode->getImageUrl());
    }

    public function testStartTimeGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getStartTime());
        
        $startTime = new DateTimeImmutable();
        $this->promotionCode->setStartTime($startTime);
        $this->assertSame($startTime, $this->promotionCode->getStartTime());
        
        $this->promotionCode->setStartTime(null);
        $this->assertNull($this->promotionCode->getStartTime());
    }

    public function testEndTimeGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getEndTime());
        
        $endTime = new DateTimeImmutable();
        $this->promotionCode->setEndTime($endTime);
        $this->assertSame($endTime, $this->promotionCode->getEndTime());
        
        $this->promotionCode->setEndTime(null);
        $this->assertNull($this->promotionCode->getEndTime());
    }

    public function testEnvVersionGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getEnvVersion());
        
        $envVersion = EnvVersion::RELEASE;
        $this->promotionCode->setEnvVersion($envVersion);
        $this->assertSame($envVersion, $this->promotionCode->getEnvVersion());
        
        $this->promotionCode->setEnvVersion(null);
        $this->assertNull($this->promotionCode->getEnvVersion());
    }

    public function testForceLoginGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->isForceLogin());
        
        $this->promotionCode->setForceLogin(true);
        $this->assertTrue($this->promotionCode->isForceLogin());
        
        $this->promotionCode->setForceLogin(false);
        $this->assertFalse($this->promotionCode->isForceLogin());
    }

    public function testValidGetterAndSetter(): void
    {
        $this->assertFalse($this->promotionCode->isValid());
        
        $this->promotionCode->setValid(true);
        $this->assertTrue($this->promotionCode->isValid());
        
        $this->promotionCode->setValid(false);
        $this->assertFalse($this->promotionCode->isValid());
    }

    public function testAddVisitLog(): void
    {
        $visitLog = new VisitLog();
        
        $this->promotionCode->addVisitLog($visitLog);
        
        $this->assertCount(1, $this->promotionCode->getVisitLogs());
        $this->assertTrue($this->promotionCode->getVisitLogs()->contains($visitLog));
        $this->assertSame($this->promotionCode, $visitLog->getCode());
    }

    public function testRemoveVisitLog(): void
    {
        $visitLog = new VisitLog();
        
        $this->promotionCode->addVisitLog($visitLog);
        $this->assertCount(1, $this->promotionCode->getVisitLogs());
        
        $this->promotionCode->removeVisitLog($visitLog);
        $this->assertCount(0, $this->promotionCode->getVisitLogs());
        $this->assertNull($visitLog->getCode());
    }

    public function testAddRule(): void
    {
        $rule = new CodeRule();
        
        $this->promotionCode->addRule($rule);
        
        $this->assertCount(1, $this->promotionCode->getRules());
        $this->assertTrue($this->promotionCode->getRules()->contains($rule));
        $this->assertSame($this->promotionCode, $rule->getPromotionCodeRule());
    }

    public function testRemoveRule(): void
    {
        $rule = new CodeRule();
        
        $this->promotionCode->addRule($rule);
        $this->assertCount(1, $this->promotionCode->getRules());
        
        $this->promotionCode->removeRule($rule);
        $this->assertCount(0, $this->promotionCode->getRules());
        $this->assertNull($rule->getPromotionCodeRule());
    }

    public function testShortLinkPermanentGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getShortLinkPermanent());
        
        $shortLink = 'https://short.link/permanent';
        $this->promotionCode->setShortLinkPermanent($shortLink);
        $this->assertSame($shortLink, $this->promotionCode->getShortLinkPermanent());
        
        $this->promotionCode->setShortLinkPermanent(null);
        $this->assertNull($this->promotionCode->getShortLinkPermanent());
    }

    public function testShortLinkTempGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getShortLinkTemp());
        
        $shortLink = 'https://short.link/temp';
        $this->promotionCode->setShortLinkTemp($shortLink);
        $this->assertSame($shortLink, $this->promotionCode->getShortLinkTemp());
        
        $this->promotionCode->setShortLinkTemp(null);
        $this->assertNull($this->promotionCode->getShortLinkTemp());
    }

    public function testShortLinkTempCreateTimeGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getShortLinkTempCreateTime());
        
        $createTime = new DateTimeImmutable();
        $this->promotionCode->setShortLinkTempCreateTime($createTime);
        $this->assertSame($createTime, $this->promotionCode->getShortLinkTempCreateTime());
        
        $this->promotionCode->setShortLinkTempCreateTime(null);
        $this->assertNull($this->promotionCode->getShortLinkTempCreateTime());
    }

    public function testCreatedFromIpGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getCreatedFromIp());
        
        $ip = '192.168.1.1';
        $this->promotionCode->setCreatedFromIp($ip);
        $this->assertSame($ip, $this->promotionCode->getCreatedFromIp());
        
        $this->promotionCode->setCreatedFromIp(null);
        $this->assertNull($this->promotionCode->getCreatedFromIp());
    }

    public function testUpdatedFromIpGetterAndSetter(): void
    {
        $this->assertNull($this->promotionCode->getUpdatedFromIp());
        
        $ip = '192.168.1.1';
        $this->promotionCode->setUpdatedFromIp($ip);
        $this->assertSame($ip, $this->promotionCode->getUpdatedFromIp());
        
        $this->promotionCode->setUpdatedFromIp(null);
        $this->assertNull($this->promotionCode->getUpdatedFromIp());
    }

    public function testRenderShortLink(): void
    {
        $code = 'TEST123';
        $baseUrl = 'https://example.com/short';
        
        $this->promotionCode->setCode($code);
        
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->expects($this->once())
            ->method('generate')
            ->with('wechat-mini-program-promotion-short-link', [], UrlGeneratorInterface::ABSOLUTE_URL)
            ->willReturn($baseUrl);
        
        $result = $this->promotionCode->renderShortLink($urlGenerator);
        $this->assertSame($baseUrl . '?' . $code, $result);
    }

    public function testRetrieveAdminArray(): void
    {
        $adminArray = $this->promotionCode->retrieveAdminArray();
        
        $this->assertArrayHasKey('id', $adminArray);
        $this->assertArrayHasKey('createTime', $adminArray);
        $this->assertArrayHasKey('code', $adminArray);
        $this->assertArrayHasKey('valid', $adminArray);
        $this->assertSame(0, $adminArray['id']);
        $this->assertSame('', $adminArray['code']);
        $this->assertFalse($adminArray['valid']);
    }

    public function testToString(): void
    {
        $this->assertSame('0', (string) $this->promotionCode);
    }
}