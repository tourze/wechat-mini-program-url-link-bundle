<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatMiniProgramUrlLinkBundle\Repository\CodeRuleTagRepository;

#[ORM\Entity(repositoryClass: CodeRuleTagRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code_rule_tag', options: ['comment' => '推广码规则标签'])]
class CodeRuleTag implements \Stringable
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

    private ?string $name = null;

    private ?string $code = null;

    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'ruleTag', targetEntity: CodeRule::class)]
    private Collection $promotionCodeRules;

    public function __construct()
    {
        $this->promotionCodeRules = new ArrayCollection();
    }

    public function __toString()
    {
        if (null === $this->id || 0 === $this->id) {
            return '';
        }

        return $this->getName();
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, CodeRule>
     */
    public function getPromotionCodeRules(): Collection
    {
        return $this->promotionCodeRules;
    }

    public function addPromotionCodeRule(CodeRule $promotionCodeRule): static
    {
        if (!$this->promotionCodeRules->contains($promotionCodeRule)) {
            $this->promotionCodeRules->add($promotionCodeRule);
            $promotionCodeRule->setRuleTag($this);
        }

        return $this;
    }

    public function removePromotionCodeRule(CodeRule $promotionCodeRule): static
    {
        if ($this->promotionCodeRules->removeElement($promotionCodeRule)) {
            // set the owning side to null (unless already changed)
            if ($promotionCodeRule->getRuleTag() === $this) {
                $promotionCodeRule->setRuleTag(null);
            }
        }

        return $this;
    }
}
