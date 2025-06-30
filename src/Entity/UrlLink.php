<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatMiniProgramBundle\Entity\Account;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\UrlLinkRepository;

/**
 * 这里记录的是微信短链的打开记录
 */
#[ORM\Entity(repositoryClass: UrlLinkRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_url_link', options: ['comment' => '推广码UrlLink'])]
class UrlLink implements Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(targetEntity: Account::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Account $account = null;

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
    private bool $checked = false;

    #[CreateIpColumn]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    private ?string $updatedFromIp = null;


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

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
