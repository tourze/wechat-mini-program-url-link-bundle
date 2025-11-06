<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramUrlLinkBundle\Service\AccountConverter;

/**
 * @internal
 */
#[CoversClass(AccountConverter::class)]
final class AccountConverterTest extends TestCase
{
    private AccountConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->converter = new AccountConverter();
    }

    public function testToAccountWithValidAccount(): void
    {
        $account = new Account();
        $result = $this->converter->toAccount($account);

        self::assertSame($account, $result);
    }

    public function testToAccountWithInvalidAccount(): void
    {
        $miniProgram = $this->createMock(MiniProgramInterface::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected instance of WechatMiniProgramBundle\Entity\Account');

        $this->converter->toAccount($miniProgram);
    }

    public function testIsValidAccountWithValidAccount(): void
    {
        $account = new Account();

        self::assertTrue($this->converter->isValidAccount($account));
    }

    public function testIsValidAccountWithInvalidAccount(): void
    {
        $miniProgram = $this->createMock(MiniProgramInterface::class);

        self::assertFalse($this->converter->isValidAccount($miniProgram));
    }

    public function testIsValidAccountWithNull(): void
    {
        self::assertFalse($this->converter->isValidAccount(null));
    }

    public function testConvertToAccountWithValidAccount(): void
    {
        $account = new Account();
        $result = $this->converter->convertToAccount($account);

        self::assertSame($account, $result);
    }

    public function testConvertToAccountWithInvalidAccount(): void
    {
        $miniProgram = $this->createMock(MiniProgramInterface::class);
        $result = $this->converter->convertToAccount($miniProgram);

        self::assertNull($result);
    }

    public function testConvertToAccountWithNull(): void
    {
        $result = $this->converter->convertToAccount(null);

        self::assertNull($result);
    }

    public function testGetValidationInfoWithValidAccount(): void
    {
        $account = new Account();
        $info = $this->converter->getValidationInfo($account);

        self::assertTrue($info['isAccount']);
        self::assertSame(Account::class, $info['class']);
        self::assertTrue($info['supportsClient']);
        self::assertArrayHasKey('appId', $info);
    }

    public function testGetValidationInfoWithInvalidAccount(): void
    {
        $miniProgram = $this->createMock(MiniProgramInterface::class);
        $miniProgram->method('getAppId')->willReturn('test_app_id');

        $info = $this->converter->getValidationInfo($miniProgram);

        self::assertFalse($info['isAccount']);
        self::assertSame(get_class($miniProgram), $info['class']);
        self::assertSame('test_app_id', $info['appId']);
        self::assertFalse($info['supportsClient']);
    }
}