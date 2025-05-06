<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use AppBundle\Entity\BizUser;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\VisitLogRepository;

#[AsScheduleClean(expression: '14 3 * * *', defaultKeepDay: 90, keepDayEnv: 'PROMOTION_VISIT_LOG_PERSIST_DAY_NUM')]
#[AsPermission(title: '推广码访问记录')]
#[ORM\Entity(repositoryClass: VisitLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_visit_log', options: ['comment' => '推广码访问记录'])]
class VisitLog
{
    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'restful_read'])]
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
    use LaunchOptionsAware;

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[Groups(['restful_read', 'admin_curd', 'recursive_view', 'api_tree'])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: PromotionCode::class, inversedBy: 'visitLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PromotionCode $code = null;

    #[FormField(span: 6)]
    #[ListColumn]
    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    private ?EnvVersion $envVersion = null;

    #[ORM\Column(type: Types::JSON)]
    private array $response = [];

    #[ORM\ManyToOne(targetEntity: BizUser::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?BizUser $user = null;

    #[CreateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '创建者IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(type: Types::STRING, length: 128, nullable: true, options: ['comment' => '更新者IP'])]
    private ?string $updatedFromIp = null;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getCode(): ?PromotionCode
    {
        return $this->code;
    }

    public function setCode(?PromotionCode $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getResponse(): ?array
    {
        return $this->response;
    }

    public function setResponse(array $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getUser(): ?BizUser
    {
        return $this->user;
    }

    public function setUser(?BizUser $user): self
    {
        $this->user = $user;

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
