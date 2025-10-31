<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Service\PromotionCodeQrcodeService;

/**
 * @internal
 */
#[CoversClass(PromotionCodeQrcodeService::class)]
#[RunTestsInSeparateProcesses]
final class PromotionCodeQrcodeServiceTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // Service test does not need specific setup
    }

    public function testServiceIsAvailable(): void
    {
        $service = self::getContainer()->get(PromotionCodeQrcodeService::class);

        $this->assertInstanceOf(PromotionCodeQrcodeService::class, $service);
    }

    public function testDeleteQrcodeWithNullImageUrl(): void
    {
        $service = self::getContainer()->get(PromotionCodeQrcodeService::class);
        $this->assertInstanceOf(PromotionCodeQrcodeService::class, $service);

        $promotionCode = new PromotionCode();
        $promotionCode->setCode('test-code');
        $promotionCode->setName('Test Code');
        $promotionCode->setEnvVersion(EnvVersion::RELEASE);
        $promotionCode->setImageUrl(null);

        $service->deleteQrcode($promotionCode);

        $this->assertNull($promotionCode->getImageUrl());
    }

    public function testGenerateQrcode(): void
    {
        $service = self::getContainer()->get(PromotionCodeQrcodeService::class);
        $this->assertInstanceOf(PromotionCodeQrcodeService::class, $service);

        $promotionCode = new PromotionCode();
        $promotionCode->setCode('test-generate-code');
        $promotionCode->setName('Test Generate Code');
        $promotionCode->setEnvVersion(EnvVersion::RELEASE);
        $promotionCode->setLinkUrl('https://example.com/test');

        // 测试没有账号的情况 - 应该返回null
        $result = $service->generateQrcode($promotionCode);
        $this->assertNull($result, '没有设置账号时，generateQrcode应该返回null');

        // 由于这是集成测试，并且需要真实的微信API调用，
        // 我们只测试参数验证部分，不实际调用微信API
        // 这样可以确保方法存在且参数验证正确
    }

    public function testDeleteQrcodeWithExistingImageUrl(): void
    {
        $service = self::getContainer()->get(PromotionCodeQrcodeService::class);
        $this->assertInstanceOf(PromotionCodeQrcodeService::class, $service);

        $promotionCode = new PromotionCode();
        $promotionCode->setCode('test-delete-code');
        $promotionCode->setName('Test Delete Code');
        $promotionCode->setEnvVersion(EnvVersion::RELEASE);
        $promotionCode->setImageUrl('promotion-qrcode/2024/01/test-image.png');

        // 调用deleteQrcode方法 - 即使文件不存在也不应抛出异常
        $service->deleteQrcode($promotionCode);

        // 删除操作不会修改实体的imageUrl属性，只是删除文件
        $this->assertEquals('promotion-qrcode/2024/01/test-image.png', $promotionCode->getImageUrl());
    }
}
