<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

/**
 * @internal
 */
#[CoversClass(DailyStatus::class)]
final class DailyStatusTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new DailyStatus();
    }

    /**
     * @return iterable<array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            'createTime' => ['createTime', new \DateTimeImmutable()],
            'updateTime' => ['updateTime', new \DateTimeImmutable()],
        ];
    }

    private DailyStatus $dailyStatus;

    private function initializeTestObject(): void
    {
        $this->dailyStatus = new DailyStatus();
    }

    public function testIdGetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->dailyStatus->getId());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->dailyStatus->getCode());

        $promotionCode = new PromotionCode();
        $this->dailyStatus->setCode($promotionCode);
        $this->assertSame($promotionCode, $this->dailyStatus->getCode());

        $this->dailyStatus->setCode(null);
        $this->assertNull($this->dailyStatus->getCode());
    }

    public function testDateGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->dailyStatus->getDate());

        $date = new \DateTimeImmutable();
        $this->dailyStatus->setDate($date);
        $this->assertSame($date, $this->dailyStatus->getDate());
    }

    public function testTotalGetterAndSetter(): void
    {
        $this->initializeTestObject();

        $this->assertNull($this->dailyStatus->getTotal());

        $total = 100;
        $this->dailyStatus->setTotal($total);
        $this->assertSame($total, $this->dailyStatus->getTotal());
    }

    public function testToString(): void
    {
        $this->initializeTestObject();

        $this->assertSame('', (string) $this->dailyStatus);
    }
}
