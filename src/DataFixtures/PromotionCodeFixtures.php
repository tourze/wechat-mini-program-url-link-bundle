<?php

namespace WechatMiniProgramUrlLinkBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class PromotionCodeFixtures extends Fixture
{
    public const PROMOTION_CODE_TEST = 'promotion-code-test';

    public function load(ObjectManager $manager): void
    {
        $promotionCode = new PromotionCode();
        $promotionCode->setName('测试推广码');
        $promotionCode->setLinkUrl('https://weixin.qq.com/promotion/test');
        $promotionCode->setCode('TEST_PROMO_' . uniqid());

        $manager->persist($promotionCode);
        $manager->flush();

        // Add reference for other fixtures
        $this->addReference(self::PROMOTION_CODE_TEST, $promotionCode);
    }
}
