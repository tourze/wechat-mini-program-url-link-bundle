<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidRequestParameterException;

/**
 * @internal
 */
#[CoversClass(InvalidRequestParameterException::class)]
final class InvalidRequestParameterExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionCreation(): void
    {
        $exception = new InvalidRequestParameterException();

        $this->assertSame('', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
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
