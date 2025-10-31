<?php

namespace WechatMiniProgramUrlLinkBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;

class UrlLinkFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $urlLink = new UrlLink();
        $urlLink->setUrlLink('https://weixin.qq.com/test-url-link');

        $manager->persist($urlLink);
        $manager->flush();
    }
}
