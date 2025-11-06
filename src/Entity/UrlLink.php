<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\WechatMiniProgramAppIDContracts\MiniProgramInterface;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;

/**
 * 这里记录的是微信短链的打开记录
 */
#[ORM\Entity(repositoryClass: UrlLinkRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_url_link', options: ['comment' => '推广码UrlLink'])]
class UrlLink implements \Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[ORM\ManyToOne(targetEntity: MiniProgramInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?MiniProgramInterface $account = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    #[Assert\Choice(callback: [EnvVersion::class, 'cases'])]
    private ?EnvVersion $envVersion = null;

    #[ORM\Column(type: Types::STRING, length: 150, unique: true, options: ['comment' => 'Url Link'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    #[Assert\Url]
    private ?string $urlLink = null;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '打开路径'])]
    #[Assert\Length(max: 1000)]
    private ?string $path = null;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '打开参数'])]
    #[Assert\Length(max: 1000)]
    private ?string $query = null;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    #[Assert\Type(type: 'array')]
    private ?array $rawData = [];

    #[ORM\Column(type: Types::STRING, length: 70, nullable: true, options: ['comment' => '访问者OpenId'])]
    #[Assert\Length(max: 70)]
    private ?string $visitOpenId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, options: ['comment' => '是否已检查'])]
    #[Assert\Type(type: 'bool')]
    private bool $checked = false;

    public function getAccount(): ?MiniProgramInterface
    {
        return $this->account;
    }

    public function setAccount(?MiniProgramInterface $account): void
    {
        $this->account = $account;
    }

    public function getEnvVersion(): ?EnvVersion
    {
        return $this->envVersion;
    }

    public function setEnvVersion(?EnvVersion $envVersion): void
    {
        $this->envVersion = $envVersion;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): void
    {
        $this->query = $query;
    }

    public function getUrlLink(): ?string
    {
        return $this->urlLink;
    }

    public function setUrlLink(string $urlLink): void
    {
        $this->urlLink = $urlLink;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    /**
     * @param array<string, mixed>|null $rawData
     */
    public function setRawData(?array $rawData): void
    {
        $this->rawData = $rawData;
    }

    public function getVisitOpenId(): ?string
    {
        return $this->visitOpenId;
    }

    public function setVisitOpenId(?string $visitOpenId): void
    {
        $this->visitOpenId = $visitOpenId;
    }

    public function isChecked(): bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): void
    {
        $this->checked = $checked;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
