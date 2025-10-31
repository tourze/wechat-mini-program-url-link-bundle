<?php

namespace WechatMiniProgramUrlLinkBundle\Exception;

class InvalidAccountException extends \RuntimeException
{
    public function __construct(string $message = '账户信息错误', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
