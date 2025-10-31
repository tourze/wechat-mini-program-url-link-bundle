<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;

/**
 * @internal
 */
#[CoversClass(UrlLink::class)]
final class UrlLinkTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new UrlLink();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'checked' => ['checked', true],
        ];
    }

    private UrlLink $urlLink;

    private function initializeTestObject(): void
    {
        $this->urlLink = new UrlLink();
    }

    /**
     * 测试设置和获取 ID
     */
    public function testGetIdReturnsNullByDefault(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->urlLink->getId());
    }

    /**
     * 测试设置和获取 Account
     */
    public function testSetAndGetAccountStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        /*
         * 使用具体类 Account 进行 mock 的原因：
         * 1. 该类没有对应的接口定义，直接继承自 Doctrine Repository 或其他基础类
         * 2. 测试需要模拟该类的特定方法行为，使用具体类是合理的测试实践
         * 3. 在单元测试中使用具体类 mock 可以更好地隔离被测试的功能
         */
        $account = $this->createMock(Account::class);

        $this->urlLink->setAccount($account);
        $this->assertSame($account, $this->urlLink->getAccount());
    }

    /**
     * 测试设置和获取 EnvVersion
     */
    public function testSetAndGetEnvVersionStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $envVersion = EnvVersion::RELEASE;

        $this->urlLink->setEnvVersion($envVersion);
        $this->assertSame($envVersion, $this->urlLink->getEnvVersion());
    }

    /**
     * 测试设置和获取 Path
     */
    public function testSetAndGetPathStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $path = 'pages/index/index';

        $this->urlLink->setPath($path);
        $this->assertSame($path, $this->urlLink->getPath());
    }

    /**
     * 测试设置和获取 Query
     */
    public function testSetAndGetQueryStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $query = 'id=123&type=product';

        $this->urlLink->setQuery($query);
        $this->assertSame($query, $this->urlLink->getQuery());
    }

    /**
     * 测试设置和获取 UrlLink
     */
    public function testSetAndGetUrlLinkStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $urlLink = 'https://example.com/test-url-link';

        $this->urlLink->setUrlLink($urlLink);
        $this->assertSame($urlLink, $this->urlLink->getUrlLink());
    }

    /**
     * 测试设置和获取 RawData
     */
    public function testSetAndGetRawDataStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $rawData = ['key1' => 'value1', 'key2' => 'value2'];

        $this->urlLink->setRawData($rawData);
        $this->assertSame($rawData, $this->urlLink->getRawData());
    }

    /**
     * 测试设置和获取 VisitOpenId
     */
    public function testSetAndGetVisitOpenIdStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $visitOpenId = 'open_id_test_123';

        $this->urlLink->setVisitOpenId($visitOpenId);
        $this->assertSame($visitOpenId, $this->urlLink->getVisitOpenId());
    }

    /**
     * 测试设置和获取 Checked 标志
     */
    public function testSetAndIsCheckedStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $checked = true;

        $this->urlLink->setChecked($checked);
        $this->assertSame($checked, $this->urlLink->isChecked());
    }

    /**
     * 测试默认 Checked 值
     */
    public function testIsCheckedReturnsFalseByDefault(): void
    {
        $this->initializeTestObject();

        $this->assertFalse($this->urlLink->isChecked());
    }

    /**
     * 测试设置和获取 CreatedFromIp
     */
    public function testSetAndGetCreatedFromIpStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $createdFromIp = '192.168.1.1';

        $this->urlLink->setCreatedFromIp($createdFromIp);
        $this->assertSame($createdFromIp, $this->urlLink->getCreatedFromIp());
    }

    /**
     * 测试设置和获取 UpdatedFromIp
     */
    public function testSetAndGetUpdatedFromIpStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $updatedFromIp = '192.168.1.2';

        $this->urlLink->setUpdatedFromIp($updatedFromIp);
        $this->assertSame($updatedFromIp, $this->urlLink->getUpdatedFromIp());
    }

    /**
     * 测试设置和获取 CreateTime
     */
    public function testSetAndGetCreateTimeStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $createTime = new \DateTimeImmutable();

        $this->urlLink->setCreateTime($createTime);

        $this->assertSame($createTime, $this->urlLink->getCreateTime());
    }

    /**
     * 测试设置和获取 UpdateTime
     */
    public function testSetAndGetUpdateTimeStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $updateTime = new \DateTimeImmutable();

        $this->urlLink->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->urlLink->getUpdateTime());
    }

    /**
     * 测试所有设置器链式调用
     */
    public function testFluentInterfaceAllowsChainedMethodCalls(): void
    {
        $this->initializeTestObject();

        /*
         * 使用具体类 Account 进行 mock 的原因：
         * 1. 该类没有对应的接口定义，直接继承自 Doctrine Repository 或其他基础类
         * 2. 测试需要模拟该类的特定方法行为，使用具体类是合理的测试实践
         * 3. 在单元测试中使用具体类 mock 可以更好地隔离被测试的功能
         */
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

        $this->urlLink->setAccount($account);
        $this->urlLink->setEnvVersion($envVersion);
        $this->urlLink->setPath($path);
        $this->urlLink->setQuery($query);
        $this->urlLink->setUrlLink($urlLinkValue);
        $this->urlLink->setRawData($rawData);
        $this->urlLink->setVisitOpenId($visitOpenId);
        $this->urlLink->setChecked($checked);
        $this->urlLink->setCreatedFromIp($createdFromIp);
        $this->urlLink->setUpdatedFromIp($updatedFromIp);
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
