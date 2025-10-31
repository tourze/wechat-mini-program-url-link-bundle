<?php

namespace WechatMiniProgramUrlLinkBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\ScheduleEntityCleanBundle\Attribute\AsScheduleClean;
use WechatMiniProgramBundle\Entity\LaunchOptionsAware;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Repository\VisitLogRepository;

#[AsScheduleClean(expression: '14 3 * * *', defaultKeepDay: 90, keepDayEnv: 'PROMOTION_VISIT_LOG_PERSIST_DAY_NUM')]
#[ORM\Entity(repositoryClass: VisitLogRepository::class)]
#[ORM\Table(name: 'wechat_mini_program_promotion_visit_log', options: ['comment' => '推广码访问记录'])]
class VisitLog implements \Stringable
{
    use TimestampableAware;
    use LaunchOptionsAware;
    use SnowflakeKeyAware;
    use IpTraceableAware;

    #[ORM\ManyToOne(targetEntity: PromotionCode::class, inversedBy: 'visitLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PromotionCode $code = null;

    #[ORM\Column(type: Types::STRING, length: 30, nullable: true, enumType: EnvVersion::class, options: ['default' => 'release', 'comment' => '打开版本'])]
    #[Assert\Choice(callback: [EnvVersion::class, 'cases'])]
    private ?EnvVersion $envVersion = null;

    /**
     * @var array<string, mixed>
     */
    #[ORM\Column(type: Types::JSON, options: ['comment' => '字段说明'])]
    #[Assert\Type(type: 'array')]
    private array $response = [];

    #[ORM\ManyToOne(targetEntity: UserInterface::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?UserInterface $user = null;

    public function getEnvVersion(): ?EnvVersion
    {
        return $this->envVersion;
    }

    public function setEnvVersion(?EnvVersion $envVersion): void
    {
        $this->envVersion = $envVersion;
    }

    public function getCode(): ?PromotionCode
    {
        return $this->code;
    }

    public function setCode(?PromotionCode $code): void
    {
        $this->code = $code;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @param array<string, mixed> $response
     */
    public function setResponse(array $response): void
    {
        $this->response = $response;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }

    public function __toString(): string
    {
        return (string) $this->id;
    }
}
