<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Entity;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRule;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRuleTag;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class CodeRuleTest extends TestCase
{
    private CodeRule $codeRule;

    protected function setUp(): void
    {
        $this->codeRule = new CodeRule();
    }

    public function testIdGetterAndSetter(): void
    {
        $this->assertSame(0, $this->codeRule->getId());
    }

    public function testValidGetterAndSetter(): void
    {
        $this->assertFalse($this->codeRule->isValid());
        
        $this->codeRule->setValid(true);
        $this->assertTrue($this->codeRule->isValid());
        
        $this->codeRule->setValid(false);
        $this->assertFalse($this->codeRule->isValid());
    }

    public function testLinkUrlGetterAndSetter(): void
    {
        $this->assertNull($this->codeRule->getLinkUrl());
        
        $url = 'https://example.com';
        $this->codeRule->setLinkUrl($url);
        $this->assertSame($url, $this->codeRule->getLinkUrl());
    }

    public function testRuleTagGetterAndSetter(): void
    {
        $this->assertNull($this->codeRule->getRuleTag());
        
        $ruleTag = new CodeRuleTag();
        $this->codeRule->setRuleTag($ruleTag);
        $this->assertSame($ruleTag, $this->codeRule->getRuleTag());
        
        $this->codeRule->setRuleTag(null);
        $this->assertNull($this->codeRule->getRuleTag());
    }

    public function testPromotionCodeRuleGetterAndSetter(): void
    {
        $this->assertNull($this->codeRule->getPromotionCodeRule());
        
        $promotionCode = new PromotionCode();
        $this->codeRule->setPromotionCodeRule($promotionCode);
        $this->assertSame($promotionCode, $this->codeRule->getPromotionCodeRule());
        
        $this->codeRule->setPromotionCodeRule(null);
        $this->assertNull($this->codeRule->getPromotionCodeRule());
    }

    public function testToString(): void
    {
        $this->assertSame('0', (string) $this->codeRule);
    }
}