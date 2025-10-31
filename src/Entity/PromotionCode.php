<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Attribute\SnowflakeColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;

/**
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: PromotionCodeRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_code', options: ['comment' => '推广码'])]
class PromotionCode implements AdminArrayInterface, \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: MiniProgramInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?MiniProgramInterface $account = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    #[Assert\NotBlank(message: '名称不能为空')]
    #[Assert\Length(max: 100)]
    private ?string $name = null;

    #[SnowflakeColumn(length: 10)]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => '唯一码'])]
    #[Assert\NotBlank(message: '唯一编码不能为空')]
    #[Assert\Length(max: 64)]
    private string $code = '';

    /**
     * @var string|null 这里的推广链接，是微信小程序上用的，所以不是标准的URL
     */
    #[ORM\Column(type: Types::STRING, length: 2000, options: ['comment' => '推广链接'])]
    #[Assert\NotBlank(message: '推广链接不能为空')]
    #[Assert\Length(max: 2000)]
    #[Assert\Url]
    private ?string $linkUrl = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '推广码'])]
    #[Assert\Length(max: 255)]
    #[Assert\Url]
    private ?string $imageUrl = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '开始时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '结束时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeInterface $endTime = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    #[Assert\Choice(callback: [EnvVersion::class, 'cases'])]
    private ?EnvVersion $envVersion = null;

    /**
     * @var Collection<int, VisitLog>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: VisitLog::class, mappedBy: 'code', orphanRemoval: true)]
    private Collection $visitLogs;

    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['default' => '0', 'comment' => '强制授权'])]
    #[Assert\Type(type: 'bool')]
    private ?bool $forceLogin = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短链(临时)'])]
    #[Assert\Length(max: 255)]
    private ?string $shortLinkTemp = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '短链生成时间'])]
    #[Assert\Type(type: '\DateTimeInterface')]
    private ?\DateTimeInterface $shortLinkTempCreateTime = null;

    #[ORM\Column(length: 255, nullable: true, options: ['comment' => '短链(永久)'])]
    #[Assert\Length(max: 255)]
    private ?string $shortLinkPermanent = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[Assert\Type(type: 'bool')]
    private ?bool $valid = false;

    public function __construct()
    {
        $this->visitLogs = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getAccount(): ?MiniProgramInterface
    {
        return $this->account;
    }

    public function setAccount(?MiniProgramInterface $account): void
    {
        $this->account = $account;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(string $linkUrl): void
    {
        $this->linkUrl = $linkUrl;
    }

    public function getEnvVersion(): ?EnvVersion
    {
        return $this->envVersion;
    }

    public function setEnvVersion(?EnvVersion $envVersion): void
    {
        $this->envVersion = $envVersion;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
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

    public function addVisitLog(VisitLog $visitLog): void
    {
        if (!$this->visitLogs->contains($visitLog)) {
            $this->visitLogs->add($visitLog);
            $visitLog->setCode($this);
        }
    }

    public function removeVisitLog(VisitLog $visitLog): void
    {
        if ($this->visitLogs->removeElement($visitLog)) {
            // set the owning side to null (unless already changed)
            if ($visitLog->getCode() === $this) {
                $visitLog->setCode(null);
            }
        }
    }

    public function isForceLogin(): ?bool
    {
        return $this->forceLogin;
    }

    public function setForceLogin(bool $forceLogin): void
    {
        $this->forceLogin = $forceLogin;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getShortLinkPermanent(): ?string
    {
        return $this->shortLinkPermanent;
    }

    public function setShortLinkPermanent(?string $shortLinkPermanent): void
    {
        $this->shortLinkPermanent = $shortLinkPermanent;
    }

    public function getShortLinkTemp(): ?string
    {
        return $this->shortLinkTemp;
    }

    public function setShortLinkTemp(?string $shortLinkTemp): void
    {
        $this->shortLinkTemp = $shortLinkTemp;
    }

    public function getShortLinkTempCreateTime(): ?\DateTimeInterface
    {
        return $this->shortLinkTempCreateTime;
    }

    public function setShortLinkTempCreateTime(?\DateTimeInterface $shortLinkTempCreateTime): void
    {
        $this->shortLinkTempCreateTime = $shortLinkTempCreateTime;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    /**
     * @return array<string, mixed>
     */
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

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
