<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Unit\Exception;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidRequestParameterException;

class InvalidRequestParameterExceptionTest extends TestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new InvalidRequestParameterException();
        
        $this->assertInstanceOf(InvalidArgumentException::class, $exception);
        $this->assertInstanceOf(InvalidRequestParameterException::class, $exception);
    }

    public function testExceptionWithMessage(): void
    {
        $message = 'Invalid parameter provided';
        $exception = new InvalidRequestParameterException($message);
        
        $this->assertSame($message, $exception->getMessage());
    }

    public function testExceptionWithMessageAndCode(): void
    {
        $message = 'Invalid parameter provided';
        $code = 400;
        $exception = new InvalidRequestParameterException($message, $code);
        
        $this->assertSame($message, $exception->getMessage());
        $this->assertSame($code, $exception->getCode());
    }
}