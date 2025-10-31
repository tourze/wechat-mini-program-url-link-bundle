<?php

namespace WechatMiniProgramUrlLinkBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;

class VisitLogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $promotionCode = new PromotionCode();
        $promotionCode->setName('测试推广码用于访问日志');
        $promotionCode->setLinkUrl('https://weixin.qq.com/promotion/for-visit-log');
        $promotionCode->setCode('VISIT_LOG_' . uniqid());
        $manager->persist($promotionCode);

        $visitLog = new VisitLog();
        $visitLog->setCode($promotionCode);
        $visitLog->setResponse(['test' => 'data']);

        $manager->persist($visitLog);
        $manager->flush();
    }
}
