<?php

declare(strict_types=1);

namespace WechatMiniProgramUrlLinkBundle\Tests\Param;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Tourze\JsonRPC\Core\Contracts\RpcParamInterface;
use WechatMiniProgramUrlLinkBundle\Param\GetWechatMiniProgramPromotionCodeInfoParam;

/**
 * GetWechatMiniProgramPromotionCodeInfoParam 单元测试
 *
 * @internal
 */
#[CoversClass(GetWechatMiniProgramPromotionCodeInfoParam::class)]
final class GetWechatMiniProgramPromotionCodeInfoParamTest extends TestCase
{
    public function testImplementsRpcParamInterface(): void
    {
        $param = new GetWechatMiniProgramPromotionCodeInfoParam(id: 123);

        $this->assertInstanceOf(RpcParamInterface::class, $param);
    }

    public function testConstructorWithIdParameter(): void
    {
        $param = new GetWechatMiniProgramPromotionCodeInfoParam(id: 456);

        $this->assertSame(456, $param->id);
    }

    public function testClassIsReadonly(): void
    {
        $reflection = new \ReflectionClass(GetWechatMiniProgramPromotionCodeInfoParam::class);

        $this->assertTrue($reflection->isReadOnly());
    }

    public function testPropertiesArePublicReadonly(): void
    {
        $reflection = new \ReflectionClass(GetWechatMiniProgramPromotionCodeInfoParam::class);

        $properties = ['id'];

        foreach ($properties as $propertyName) {
            $property = $reflection->getProperty($propertyName);
            $this->assertTrue($property->isPublic(), "{$propertyName} should be public");
            $this->assertTrue($property->isReadOnly(), "{$propertyName} should be readonly");
        }
    }

    public function testValidationFailsWhenIdIsNotPositive(): void
    {
        $param = new GetWechatMiniProgramPromotionCodeInfoParam(id: -1);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationPassesWithValidId(): void
    {
        $param = new GetWechatMiniProgramPromotionCodeInfoParam(id: 100);

        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $violations = $validator->validate($param);

        $this->assertCount(0, $violations);
    }

    public function testHasMethodParamAttributes(): void
    {
        $reflection = new \ReflectionClass(GetWechatMiniProgramPromotionCodeInfoParam::class);
        $constructor = $reflection->getConstructor();

        $this->assertNotNull($constructor);

        foreach ($constructor->getParameters() as $parameter) {
            $attrs = $parameter->getAttributes(\Tourze\JsonRPC\Core\Attribute\MethodParam::class);
            $this->assertNotEmpty($attrs, "Parameter {$parameter->getName()} should have MethodParam attribute");
        }
    }
}
