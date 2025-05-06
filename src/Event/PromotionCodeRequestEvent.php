<?php

namespace WechatMiniProgramUrlLinkBundle\Event;

use AppBundle\Entity\BizUser;
use Symfony\Contracts\EventDispatcher\Event;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;

class PromotionCodeRequestEvent extends Event
{
    private array $result = [];

    public function getResult(): array
    {
        return $this->result;
    }

    public function setResult(array $result): void
    {
        $this->result = $result;
    }

    private PromotionCode $code;

    private ?BizUser $user;

    public function getCode(): PromotionCode
    {
        return $this->code;
    }

    public function setCode(PromotionCode $code): void
    {
        $this->code = $code;
    }

    public function getUser(): ?BizUser
    {
        return $this->user;
    }

    public function setUser(?BizUser $user): void
    {
        $this->user = $user;
    }
}
