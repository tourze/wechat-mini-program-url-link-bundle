<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Exception;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;
use WechatMiniProgramUrlLinkBundle\Exception\InvalidAccountException;

/**
 * @internal
 */
#[CoversClass(InvalidAccountException::class)]
final class InvalidAccountExceptionTest extends AbstractExceptionTestCase
{
    public function testConstruct(): void
    {
        $exception = new InvalidAccountException();

        $this->assertSame('账户信息错误', $exception->getMessage());
        $this->assertSame(0, $exception->getCode());
    }

    public function testConstructWithCustomMessage(): void
    {
        $message = 'Custom error message';
        $exception = new InvalidAccountException($message);

        $this->assertSame($message, $exception->getMessage());
    }

    public function testConstructWithCustomCode(): void
    {
        $code = 123;
        $exception = new InvalidAccountException('Error', $code);

        $this->assertSame($code, $exception->getCode());
    }

    public function testConstructWithPrevious(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = new InvalidAccountException('Error', 0, $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
