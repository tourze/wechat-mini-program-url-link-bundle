<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;

/**
 * 这里记录的是微信短链的打开记录
 */
#[AsPermission(title: '推广码UrlLink')]
#[ORM\Entity(repositoryClass: UrlLinkRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_url_link', options: ['comment' => '推广码UrlLink'])]
class UrlLink
{
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

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[FormField(title: '小程序')]
    #[ListColumn(title: '小程序')]
    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

    #[FormField(span: 6)]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    private ?EnvVersion $envVersion = null;

    #[ORM\Column(type: Types::STRING, length: 150, unique: true, options: ['comment' => 'Url Link'])]
    private ?string $urlLink = null;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '打开路径'])]
    private ?string $path = null;

    #[ORM\Column(type: Types::STRING, length: 1000, nullable: true, options: ['comment' => '打开参数'])]
    private ?string $query = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '原始数据'])]
    private ?array $rawData = [];

    #[ORM\Column(type: Types::STRING, length: 70, nullable: true, options: ['comment' => '访问者OpenId'])]
    private ?string $visitOpenId = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '是否已检查'])]
    private ?bool $checked = false;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getEnvVersion(): ?EnvVersion
    {
        return $this->envVersion;
    }

    public function setEnvVersion(?EnvVersion $envVersion): self
    {
        $this->envVersion = $envVersion;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function setQuery(?string $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function getUrlLink(): ?string
    {
        return $this->urlLink;
    }

    public function setUrlLink(string $urlLink): self
    {
        $this->urlLink = $urlLink;

        return $this;
    }

    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    public function setRawData(?array $rawData): self
    {
        $this->rawData = $rawData;

        return $this;
    }

    public function getVisitOpenId(): ?string
    {
        return $this->visitOpenId;
    }

    public function setVisitOpenId(?string $visitOpenId): self
    {
        $this->visitOpenId = $visitOpenId;

        return $this;
    }

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked): self
    {
        $this->checked = $checked;

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
    }
}
