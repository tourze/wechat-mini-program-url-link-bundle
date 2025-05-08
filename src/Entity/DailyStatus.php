<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use WechatMiniProgramUrlLinkBundle\Repository\DailyStatusRepository;

#[AsPermission(title: '推广码访问记录统计')]
#[ORM\Entity(repositoryClass: DailyStatusRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_daily_status', options: ['comment' => '推广码访问记录统计'])]
class DailyStatus
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

    #[ORM\ManyToOne(targetEntity: PromotionCode::class, inversedBy: 'visitLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PromotionCode $code = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options: ['comment' => '统计日期'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(options: ['comment' => '数量'])]
    private ?int $total = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCode(): ?PromotionCode
    {
        return $this->code;
    }

    public function setCode(?PromotionCode $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }
}
