<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Event\PromotionCodeRequestEvent;

class PromotionCodeRequestEventTest extends TestCase
{
    private PromotionCodeRequestEvent $event;

    protected function setUp(): void
    {
        $this->event = new PromotionCodeRequestEvent();
    }

    public function testIsEvent(): void
    {
        $this->assertInstanceOf(Event::class, $this->event);
    }

    public function testResultGetterAndSetter(): void
    {
        $this->assertSame([], $this->event->getResult());
        
        $result = ['key' => 'value', 'status' => 'success'];
        $this->event->setResult($result);
        $this->assertSame($result, $this->event->getResult());
    }

    public function testCodeGetterAndSetter(): void
    {
        $code = new PromotionCode();
        $this->event->setCode($code);
        $this->assertSame($code, $this->event->getCode());
    }

    public function testUserGetterAndSetter(): void
    {
        $this->assertNull($this->event->getUser());
        
        $user = $this->createMock(UserInterface::class);
        $this->event->setUser($user);
        $this->assertSame($user, $this->event->getUser());
        
        $this->event->setUser(null);
        $this->assertNull($this->event->getUser());
    }
}