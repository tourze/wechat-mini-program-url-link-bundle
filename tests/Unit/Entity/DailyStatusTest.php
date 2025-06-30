<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class DailyStatusTest extends TestCase
{
    private DailyStatus $dailyStatus;

    protected function setUp(): void
    {
        $this->dailyStatus = new DailyStatus();
    }

    public function testIdGetter(): void
    {
        $this->assertNull($this->dailyStatus->getId());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->assertNull($this->dailyStatus->getCode());
        
        $promotionCode = new PromotionCode();
        $this->dailyStatus->setCode($promotionCode);
        $this->assertSame($promotionCode, $this->dailyStatus->getCode());
        
        $this->dailyStatus->setCode(null);
        $this->assertNull($this->dailyStatus->getCode());
    }

    public function testDateGetterAndSetter(): void
    {
        $this->assertNull($this->dailyStatus->getDate());
        
        $date = new DateTimeImmutable();
        $this->dailyStatus->setDate($date);
        $this->assertSame($date, $this->dailyStatus->getDate());
    }

    public function testTotalGetterAndSetter(): void
    {
        $this->assertNull($this->dailyStatus->getTotal());
        
        $total = 100;
        $this->dailyStatus->setTotal($total);
        $this->assertSame($total, $this->dailyStatus->getTotal());
    }

    public function testToString(): void
    {
        $this->assertSame('', (string) $this->dailyStatus);
    }
}