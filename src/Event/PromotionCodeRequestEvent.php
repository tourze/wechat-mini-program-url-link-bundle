<?php

namespace WechatMiniProgramUrlLinkBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class PromotionCodeRequestEvent extends Event
{
    /**
     * @var array<string, mixed>
     */
    private array $result = [];

    /**
     * @return array<string, mixed>
     */
    public function getResult(): array
    {
        return $this->result;
    }

    /**
     * @param array<string, mixed> $result
     */
    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    private PromotionCode $code;

    private ?UserInterface $user = null;

    public function getCode(): PromotionCode
    {
        return $this->code;
    }

    public function setCode(PromotionCode $code): void
    {
        $this->code = $code;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): void
    {
        $this->user = $user;
    }
}
