<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Repository;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Repository\CodeRuleRepository;

class CodeRuleRepositoryTest extends TestCase
{
    public function testClass_存在()
    {
        $this->assertTrue(class_exists(CodeRuleRepository::class));
    }
}