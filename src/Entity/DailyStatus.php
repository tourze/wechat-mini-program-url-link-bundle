<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use WechatMiniProgramUrlLinkBundle\Repository\DailyStatusRepository;

#[ORM\Entity(repositoryClass: DailyStatusRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_daily_status', options: ['comment' => '推广码访问记录统计'])]
class DailyStatus implements Stringable
{
    use TimestampableAware;
    use SnowflakeKeyAware;

    #[ORM\ManyToOne(targetEntity: PromotionCode::class, inversedBy: 'visitLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PromotionCode $code = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '统计日期'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(options: ['comment' => '数量'])]
    private ?int $total = null;


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

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
