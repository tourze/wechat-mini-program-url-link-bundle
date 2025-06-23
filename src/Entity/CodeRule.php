<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatMiniProgramUrlLinkBundle\Repository\CodeRuleRepository;

#[ORM\Entity(repositoryClass: CodeRuleRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code_rule', options: ['comment' => '推广码规则'])]
class CodeRule implements Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    use TimestampableAware;
    use BlameableAware;

    #[TrackColumn]
    private ?bool $valid = false;

    #[ORM\ManyToOne(inversedBy: 'promotionCodeRules')]
    private ?CodeRuleTag $ruleTag = null;

    private ?string $linkUrl = null;

    #[Ignore]
    #[ORM\ManyToOne(inversedBy: 'rules')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?PromotionCode $promotionCodeRule = null;


    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(string $linkUrl): self
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getRuleTag(): ?CodeRuleTag
    {
        return $this->ruleTag;
    }

    public function setRuleTag(?CodeRuleTag $ruleTag): static
    {
        $this->ruleTag = $ruleTag;

        return $this;
    }

    public function getPromotionCodeRule(): ?PromotionCode
    {
        return $this->promotionCodeRule;
    }

    public function setPromotionCodeRule(?PromotionCode $promotionCodeRule): static
    {
        $this->promotionCodeRule = $promotionCodeRule;

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
