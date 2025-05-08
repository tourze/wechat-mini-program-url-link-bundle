<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatMiniProgramUrlLinkBundle\Repository\CodeRuleTagRepository;

#[AsPermission(title: '推广码规则标签')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: CodeRuleTagRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code_rule_tag', options: ['comment' => '推广码规则标签'])]
class CodeRuleTag implements \Stringable
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(length: 100, unique: true, options: ['comment' => '编码'])]
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
        if (!$this->id) {
            return '';
        }

        return $this->getName();
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
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
