<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRule;
use WechatMiniProgramUrlLinkBundle\Entity\CodeRuleTag;

class CodeRuleTagTest extends TestCase
{
    private CodeRuleTag $codeRuleTag;

    protected function setUp(): void
    {
        $this->codeRuleTag = new CodeRuleTag();
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->codeRuleTag->getPromotionCodeRules());
        $this->assertCount(0, $this->codeRuleTag->getPromotionCodeRules());
    }

    public function testIdGetter(): void
    {
        $this->assertSame(0, $this->codeRuleTag->getId());
    }

    public function testNameGetterAndSetter(): void
    {
        $this->assertNull($this->codeRuleTag->getName());
        
        $name = 'Test Name';
        $this->codeRuleTag->setName($name);
        $this->assertSame($name, $this->codeRuleTag->getName());
    }

    public function testCodeGetterAndSetter(): void
    {
        $this->assertNull($this->codeRuleTag->getCode());
        
        $code = 'TEST_CODE';
        $this->codeRuleTag->setCode($code);
        $this->assertSame($code, $this->codeRuleTag->getCode());
    }

    public function testAddPromotionCodeRule(): void
    {
        $codeRule = new CodeRule();
        
        $this->codeRuleTag->addPromotionCodeRule($codeRule);
        
        $this->assertCount(1, $this->codeRuleTag->getPromotionCodeRules());
        $this->assertTrue($this->codeRuleTag->getPromotionCodeRules()->contains($codeRule));
        $this->assertSame($this->codeRuleTag, $codeRule->getRuleTag());
    }

    public function testAddPromotionCodeRuleDuplicate(): void
    {
        $codeRule = new CodeRule();
        
        $this->codeRuleTag->addPromotionCodeRule($codeRule);
        $this->codeRuleTag->addPromotionCodeRule($codeRule);
        
        $this->assertCount(1, $this->codeRuleTag->getPromotionCodeRules());
    }

    public function testRemovePromotionCodeRule(): void
    {
        $codeRule = new CodeRule();
        
        $this->codeRuleTag->addPromotionCodeRule($codeRule);
        $this->assertCount(1, $this->codeRuleTag->getPromotionCodeRules());
        
        $this->codeRuleTag->removePromotionCodeRule($codeRule);
        $this->assertCount(0, $this->codeRuleTag->getPromotionCodeRules());
        $this->assertNull($codeRule->getRuleTag());
    }

    public function testRemovePromotionCodeRuleNotExist(): void
    {
        $codeRule = new CodeRule();
        
        $this->codeRuleTag->removePromotionCodeRule($codeRule);
        $this->assertCount(0, $this->codeRuleTag->getPromotionCodeRules());
    }

    public function testToStringWithoutIdOrName(): void
    {
        $this->assertSame('', (string) $this->codeRuleTag);
    }

    public function testToStringWithName(): void
    {
        $name = 'Test Name';
        $this->codeRuleTag->setName($name);
        
        $reflectionProperty = new \ReflectionProperty(CodeRuleTag::class, 'id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->codeRuleTag, 1);
        
        $this->assertSame($name, (string) $this->codeRuleTag);
    }
}