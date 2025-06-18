<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;

class UrlLinkTest extends TestCase
{
    private UrlLink $urlLink;

    protected function setUp(): void
    {
        $this->urlLink = new UrlLink();
    }

    /**
     * 测试设置和获取 ID
     */
    public function testGetId_returnsNull_byDefault(): void
    {
        $this->assertNull($this->urlLink->getId());
    }

    /**
     * 测试设置和获取 Account
     */
    public function testSetAndGetAccount_storesAndReturnsValue(): void
    {
        $account = $this->createMock(Account::class);
        
        $result = $this->urlLink->setAccount($account);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($account, $this->urlLink->getAccount());
    }

    /**
     * 测试设置和获取 EnvVersion
     */
    public function testSetAndGetEnvVersion_storesAndReturnsValue(): void
    {
        $envVersion = EnvVersion::RELEASE;
        
        $result = $this->urlLink->setEnvVersion($envVersion);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($envVersion, $this->urlLink->getEnvVersion());
    }

    /**
     * 测试设置和获取 Path
     */
    public function testSetAndGetPath_storesAndReturnsValue(): void
    {
        $path = 'pages/index/index';
        
        $result = $this->urlLink->setPath($path);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($path, $this->urlLink->getPath());
    }

    /**
     * 测试设置和获取 Query
     */
    public function testSetAndGetQuery_storesAndReturnsValue(): void
    {
        $query = 'id=123&type=product';
        
        $result = $this->urlLink->setQuery($query);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($query, $this->urlLink->getQuery());
    }

    /**
     * 测试设置和获取 UrlLink
     */
    public function testSetAndGetUrlLink_storesAndReturnsValue(): void
    {
        $urlLink = 'https://example.com/test-url-link';
        
        $result = $this->urlLink->setUrlLink($urlLink);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($urlLink, $this->urlLink->getUrlLink());
    }

    /**
     * 测试设置和获取 RawData
     */
    public function testSetAndGetRawData_storesAndReturnsValue(): void
    {
        $rawData = ['key1' => 'value1', 'key2' => 'value2'];
        
        $result = $this->urlLink->setRawData($rawData);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($rawData, $this->urlLink->getRawData());
    }

    /**
     * 测试设置和获取 VisitOpenId
     */
    public function testSetAndGetVisitOpenId_storesAndReturnsValue(): void
    {
        $visitOpenId = 'open_id_test_123';
        
        $result = $this->urlLink->setVisitOpenId($visitOpenId);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($visitOpenId, $this->urlLink->getVisitOpenId());
    }

    /**
     * 测试设置和获取 Checked 标志
     */
    public function testSetAndIsChecked_storesAndReturnsValue(): void
    {
        $checked = true;
        
        $result = $this->urlLink->setChecked($checked);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($checked, $this->urlLink->isChecked());
    }

    /**
     * 测试默认 Checked 值
     */
    public function testIsChecked_returnsFalse_byDefault(): void
    {
        $this->assertFalse($this->urlLink->isChecked());
    }

    /**
     * 测试设置和获取 CreatedFromIp
     */
    public function testSetAndGetCreatedFromIp_storesAndReturnsValue(): void
    {
        $createdFromIp = '192.168.1.1';
        
        $result = $this->urlLink->setCreatedFromIp($createdFromIp);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($createdFromIp, $this->urlLink->getCreatedFromIp());
    }

    /**
     * 测试设置和获取 UpdatedFromIp
     */
    public function testSetAndGetUpdatedFromIp_storesAndReturnsValue(): void
    {
        $updatedFromIp = '192.168.1.2';
        
        $result = $this->urlLink->setUpdatedFromIp($updatedFromIp);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($updatedFromIp, $this->urlLink->getUpdatedFromIp());
    }

    /**
     * 测试设置和获取 CreateTime
     */
    public function testSetAndGetCreateTime_storesAndReturnsValue(): void
    {
        $createTime = new \DateTimeImmutable();
        
        $this->urlLink->setCreateTime($createTime);
        
        $this->assertSame($createTime, $this->urlLink->getCreateTime());
    }

    /**
     * 测试设置和获取 UpdateTime
     */
    public function testSetAndGetUpdateTime_storesAndReturnsValue(): void
    {
        $updateTime = new \DateTimeImmutable();
        
        $this->urlLink->setUpdateTime($updateTime);
        
        $this->assertSame($updateTime, $this->urlLink->getUpdateTime());
    }

    /**
     * 测试所有设置器链式调用
     */
    public function testFluentInterface_allowsChainedMethodCalls(): void
    {
        $account = $this->createMock(Account::class);
        $envVersion = EnvVersion::TRIAL;
        $path = 'pages/product/detail';
        $query = 'id=456';
        $urlLinkValue = 'https://example.com/link';
        $rawData = ['data' => 'value'];
        $visitOpenId = 'open_id_456';
        $checked = true;
        $createdFromIp = '10.0.0.1';
        $updatedFromIp = '10.0.0.2';
        
        $result = $this->urlLink
            ->setAccount($account)
            ->setEnvVersion($envVersion)
            ->setPath($path)
            ->setQuery($query)
            ->setUrlLink($urlLinkValue)
            ->setRawData($rawData)
            ->setVisitOpenId($visitOpenId)
            ->setChecked($checked)
            ->setCreatedFromIp($createdFromIp)
            ->setUpdatedFromIp($updatedFromIp);
        
        $this->assertSame($this->urlLink, $result);
        $this->assertSame($account, $this->urlLink->getAccount());
        $this->assertSame($envVersion, $this->urlLink->getEnvVersion());
        $this->assertSame($path, $this->urlLink->getPath());
        $this->assertSame($query, $this->urlLink->getQuery());
        $this->assertSame($urlLinkValue, $this->urlLink->getUrlLink());
        $this->assertSame($rawData, $this->urlLink->getRawData());
        $this->assertSame($visitOpenId, $this->urlLink->getVisitOpenId());
        $this->assertSame($checked, $this->urlLink->isChecked());
        $this->assertSame($createdFromIp, $this->urlLink->getCreatedFromIp());
        $this->assertSame($updatedFromIp, $this->urlLink->getUpdatedFromIp());
    }
} 