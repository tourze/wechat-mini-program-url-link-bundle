<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;

/**
 * @internal
 */
#[CoversClass(VisitLog::class)]
final class VisitLogTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new VisitLog();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'response' => ['response', ['key' => 'value']],
        ];
    }

    private VisitLog $visitLog;

    private function initializeTestObject(): void
    {
        $this->visitLog = new VisitLog();
    }

    public function testIdGetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getId());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getCode());

        $promotionCode = new PromotionCode();
        $this->visitLog->setCode($promotionCode);
        $this->assertSame($promotionCode, $this->visitLog->getCode());

        $this->visitLog->setCode(null);
        $this->assertNull($this->visitLog->getCode());
    }

    public function testEnvVersionGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getEnvVersion());

        $envVersion = EnvVersion::RELEASE;
        $this->visitLog->setEnvVersion($envVersion);
        $this->assertSame($envVersion, $this->visitLog->getEnvVersion());

        $this->visitLog->setEnvVersion(null);
        $this->assertNull($this->visitLog->getEnvVersion());
    }

    public function testResponseGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertSame([], $this->visitLog->getResponse());

        $response = ['key' => 'value', 'status' => 'success'];
        $this->visitLog->setResponse($response);
        $this->assertSame($response, $this->visitLog->getResponse());
    }

    public function testUserGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getUser());

        $user = $this->createMock(UserInterface::class);
        $this->visitLog->setUser($user);
        $this->assertSame($user, $this->visitLog->getUser());

        $this->visitLog->setUser(null);
        $this->assertNull($this->visitLog->getUser());
    }

    public function testCreatedFromIpGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getCreatedFromIp());

        $ip = '192.168.1.1';
        $this->visitLog->setCreatedFromIp($ip);
        $this->assertSame($ip, $this->visitLog->getCreatedFromIp());

        $this->visitLog->setCreatedFromIp(null);
        $this->assertNull($this->visitLog->getCreatedFromIp());
    }

    public function testUpdatedFromIpGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->visitLog->getUpdatedFromIp());

        $ip = '192.168.1.1';
        $this->visitLog->setUpdatedFromIp($ip);
        $this->assertSame($ip, $this->visitLog->getUpdatedFromIp());

        $this->visitLog->setUpdatedFromIp(null);
        $this->assertNull($this->visitLog->getUpdatedFromIp());
    }

    public function testToString(): void
    {
        $this->initializeTestObject();

        $this->assertSame('', (string) $this->visitLog);
    }
}
