<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Event;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Tourze\PHPUnitSymfonyUnitTest\AbstractEventTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Event\PromotionCodeRequestEvent;

/**
 * @internal
 */
#[CoversClass(PromotionCodeRequestEvent::class)]
final class PromotionCodeRequestEventTest extends AbstractEventTestCase
{
    private PromotionCodeRequestEvent $event;

    private function initializeTestObject(): void
    {
        $this->event = new PromotionCodeRequestEvent();
    }

    public function testIsEvent(): void
    {
        $this->initializeTestObject();

        $this->assertInstanceOf(PromotionCodeRequestEvent::class, $this->event);
        $this->assertSame([], $this->event->getResult());
        $this->assertNull($this->event->getUser());
    }

    public function testResultGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertSame([], $this->event->getResult());

        $result = ['key' => 'value', 'status' => 'success'];
        $this->event->setResult($result);
        $this->assertSame($result, $this->event->getResult());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $code = new PromotionCode();
        $this->event->setCode($code);
        $this->assertSame($code, $this->event->getCode());
    }

    public function testUserGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->event->getUser());

        $user = $this->createMock(UserInterface::class);
        $this->event->setUser($user);
        $this->assertSame($user, $this->event->getUser());

        $this->event->setUser(null);
        $this->assertNull($this->event->getUser());
    }
}
