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
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

#[ORM\Entity(repositoryClass: PromotionCodeRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code', options: ['comment' => '推广码'])]
class PromotionCode implements AdminArrayInterface
, \Stringable{
    use TimestampableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    private ?string $name = null;

    #[SnowflakeColumn(length: 10)]
    private string $code = '';

    private ?string $linkUrl = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '推广码'])]
    private ?string $imageUrl = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Groups(['restful_read', 'admin_curd'])]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    private ?EnvVersion $envVersion = null;

    /**
     * @var Collection<VisitLog>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'code', targetEntity: VisitLog::class, orphanRemoval: true)]
    private Collection $visitLogs;

    private ?bool $forceLogin = null;

    /**
     * @DynamicFieldSet()
     *
     * @var Collection<CodeRule>
     */
    #[ORM\OneToMany(mappedBy: 'promotionCodeRule', targetEntity: CodeRule::class, cascade: ['persist'], fetch: 'EXTRA_LAZY', orphanRemoval: true)]
    private Collection $rules;

    private ?string $shortLinkTemp = null;

    private ?\DateTimeInterface $shortLinkTempCreateTime = null;

    private ?string $shortLinkPermanent = null;

    #[TrackColumn]
    private ?bool $valid = false;
    use BlameableAware;

    #[CreateIpColumn]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->visitLogs = new ArrayCollection();
        $this->rules = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
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
    }public function retrieveAdminArray(): array
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

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
