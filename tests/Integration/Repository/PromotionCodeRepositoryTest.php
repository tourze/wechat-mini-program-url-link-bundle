<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

class PromotionCodeRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(PromotionCodeRepository::class));
    }
}