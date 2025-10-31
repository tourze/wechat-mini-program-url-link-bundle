<?php

namespace WechatMiniProgramUrlLinkBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class DailyStatusFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Get reference from PromotionCodeFixtures
        $promotionCode = $this->getReference(PromotionCodeFixtures::PROMOTION_CODE_TEST, PromotionCode::class);

        $dailyStatus = new DailyStatus();
        $dailyStatus->setCode($promotionCode);
        $dailyStatus->setDate(new \DateTimeImmutable('2024-01-01'));
        $dailyStatus->setTotal(10);
        $manager->persist($dailyStatus);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            PromotionCodeFixtures::class,
        ];
    }
}
