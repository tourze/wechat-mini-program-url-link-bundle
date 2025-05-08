<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use AntdCpBundle\Builder\Field\DynamicFieldSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ImportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Filter\Keyword;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[AsPermission(title: '推广码')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: PromotionCodeRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code', options: ['comment' => '推广码'])]
class PromotionCode implements AdminArrayInterface
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[FormField(title: '小程序')]
    #[ListColumn(title: '小程序')]
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    #[FormField(span: 14)]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[FormField(span: 10)]
    #[SnowflakeColumn(length: 10)]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '唯一码'])]
    private ?string $code = '';

    #[FormField]
    #[Keyword]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 2000, options: ['comment' => '推广链接'])]
    private ?string $linkUrl = null;

    #[PictureColumn]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '推广码'])]
    private ?string $imageUrl = null;

    #[FormField(span: 10, showExpression: "env('SHOW_PROMOTION_START')")]
    #[ImportColumn]
    #[ListColumn(showExpression: "env('SHOW_PROMOTION_START')")]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[FormField(span: 10, showExpression: "env('SHOW_PROMOTION_END')")]
    #[ImportColumn]
    #[ListColumn(showExpression: "env('SHOW_PROMOTION_END')")]
    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[FormField(span: 6)]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    private ?EnvVersion $envVersion = null;

    /**
     * @var Collection<VisitLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'code', targetEntity: VisitLog::class, orphanRemoval: true)]
    private Collection $visitLogs;

    #[FormField]
    #[ListColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => '0', 'comment' => '强制授权'])]
    private ?bool $forceLogin = null;

    /**
     * @DynamicFieldSet()
     *
     * @var Collection<CodeRule>
     */
    #[FormField(title: '额外规则')]
    #[ORM\OneToMany(mappedBy: 'promotionCodeRule', targetEntity: CodeRule::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $rules;

    #[ListColumn(tooltipDesc: '有效期30天')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短链(临时)'])]
    private ?string $shortLinkTemp = null;

    #[ListColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '短链生成时间'])]
    private ?\DateTimeInterface $shortLinkTempCreateTime = null;

    #[ListColumn(tooltipDesc: '每个小程序只能生成10万次')]
    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短链(永久)'])]
    private ?string $shortLinkPermanent = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

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

    public function __construct()
    {
        $this->visitLogs = new ArrayCollection();
        $this->rules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): self
    {
        $this->account = $account;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getEnvVersion(): ?EnvVersion
    {
        return $this->envVersion;
    }

    public function setEnvVersion(?EnvVersion $envVersion): self
    {
        $this->envVersion = $envVersion;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    #[ListColumn(title: 'H5外链')]
    public function renderShortLink(UrlGeneratorInterface $urlGenerator): string
    {
        return $urlGenerator->generate('wechat-mini-program-promotion-short-link', [], UrlGeneratorInterface::ABSOLUTE_URL) . '?' . $this->getCode();
    }

    /**
     * @return Collection<int, VisitLog>
     */
    public function getVisitLogs(): Collection
    {
        return $this->visitLogs;
    }

    public function addVisitLog(VisitLog $visitLog): self
    {
        if (!$this->visitLogs->contains($visitLog)) {
            $this->visitLogs[] = $visitLog;
            $visitLog->setCode($this);
        }

        return $this;
    }

    public function removeVisitLog(VisitLog $visitLog): self
    {
        if ($this->visitLogs->removeElement($visitLog)) {
            // set the owning side to null (unless already changed)
            if ($visitLog->getCode() === $this) {
                $visitLog->setCode(null);
            }
        }

        return $this;
    }

    public function isForceLogin(): ?bool
    {
        return $this->forceLogin;
    }

    public function setForceLogin(bool $forceLogin): self
    {
        $this->forceLogin = $forceLogin;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return Collection<int, CodeRule>
     */
    public function getRules(): Collection
    {
        return $this->rules;
    }

    public function addRule(CodeRule $rule): static
    {
        if (!$this->rules->contains($rule)) {
            $this->rules->add($rule);
            $rule->setPromotionCodeRule($this);
        }

        return $this;
    }

    public function removeRule(CodeRule $rule): static
    {
        if ($this->rules->removeElement($rule)) {
            // set the owning side to null (unless already changed)
            if ($rule->getPromotionCodeRule() === $this) {
                $rule->setPromotionCodeRule(null);
            }
        }

        return $this;
    }

    public function getShortLinkPermanent(): ?string
    {
        return $this->shortLinkPermanent;
    }

    public function setShortLinkPermanent(?string $shortLinkPermanent): static
    {
        $this->shortLinkPermanent = $shortLinkPermanent;

        return $this;
    }

    public function getShortLinkTemp(): ?string
    {
        return $this->shortLinkTemp;
    }

    public function setShortLinkTemp(?string $shortLinkTemp): static
    {
        $this->shortLinkTemp = $shortLinkTemp;

        return $this;
    }

    public function getShortLinkTempCreateTime(): ?\DateTimeInterface
    {
        return $this->shortLinkTempCreateTime;
    }

    public function setShortLinkTempCreateTime(?\DateTimeInterface $shortLinkTempCreateTime): static
    {
        $this->shortLinkTempCreateTime = $shortLinkTempCreateTime;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
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

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

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

    public function retrieveAdminArray(): array
    {
        return [
            'id' => $this->getId(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
            'valid' => $this->isValid(),
            'code' => $this->getCode(),
            'startTime' => $this->getStartTime()?->format('Y-m-d H:i:s'),
            'endTime' => $this->getEndTime()?->format('Y-m-d H:i:s'),
            'name' => $this->getName(),
            'linkUrl' => $this->getLinkUrl(),
            'imageUrl' => $this->getImageUrl(),
            'envVersion' => $this->getEnvVersion(),
            'forceLogin' => $this->isForceLogin(),
            'shortLinkTemp' => $this->getShortLinkTemp(),
            'shortLinkTempCreateTime' => $this->getShortLinkTempCreateTime()?->format('Y-m-d H:i:s'),
            'shortLinkPermanent' => $this->getShortLinkPermanent(),
            'createdFromIp' => $this->getCreatedFromIp(),
            'updatedFromIp' => $this->getUpdatedFromIp(),
        ];
    }
}
