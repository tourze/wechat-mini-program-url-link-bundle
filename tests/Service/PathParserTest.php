<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\WebLink\Link;
use WechatMiniProgramBundle\Service\PathParserInterface;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Service\PathParser;

class PathParserTest extends TestCase
{
    private MockObject|PathParserInterface $innerParser;
    private MockObject|PromotionCodeRepository $promotionCodeRepository;
    private PathParser $pathParser;

    protected function setUp(): void
    {
        $this->innerParser = $this->createMock(PathParserInterface::class);
        $this->promotionCodeRepository = $this->createMock(PromotionCodeRepository::class);
        $this->pathParser = new PathParser($this->innerParser, $this->promotionCodeRepository);
    }

    /**
     * 测试普通路径直接传递给内部解析器
     */
    public function testParsePath_withRegularPath_callsInnerParser(): void
    {
        // 测试数据
        $path = 'pages/index/index';
        $query = ['foo' => 'bar'];
        $expectedLink = new Link('preload');

        // 设置内部解析器的行为
        $this->innerParser->expects($this->once())
            ->method('parsePath')
            ->with($path, $query)
            ->willReturn($expectedLink);

        // 设置仓库的行为 - 不应该被调用
        $this->promotionCodeRepository->expects($this->never())
            ->method('findOneBy');

        // 执行测试
        $result = $this->pathParser->parsePath($path, $query);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    /**
     * 测试重定向路径使用场景参数
     */
    public function testParsePath_withRedirectPathAndSceneParam_usesPromotionCode(): void
    {
        // 测试数据
        $path = 'pages/redirect/index';
        $query = ['scene' => '123'];
        $expectedLink = new Link('preload');

        // 创建模拟的促销码
        $promotionCode = $this->createMock(PromotionCode::class);
        $promotionCode->expects($this->once())
            ->method('getLinkUrl')
            ->willReturn('pages/product/detail?id=456&category=food');

        // 设置仓库的行为
        $this->promotionCodeRepository->expects($this->once())
            ->method('findOneBy')
            ->with([
                'id' => '123',
                'valid' => true,
            ])
            ->willReturn($promotionCode);

        // 设置内部解析器的行为 - 期望使用从促销码解析出的路径和查询参数
        $this->innerParser->expects($this->once())
            ->method('parsePath')
            ->with('pages/product/detail', ['id' => '456', 'category' => 'food'])
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->pathParser->parsePath($path, $query);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    /**
     * 测试重定向路径但找不到有效的促销码
     */
    public function testParsePath_withRedirectPathButNoValidCode_usesOriginalPath(): void
    {
        // 测试数据
        $path = 'pages/redirect/index';
        $query = ['scene' => '999'];
        $expectedLink = new Link('preload');

        // 设置仓库的行为 - 找不到有效的促销码
        $this->promotionCodeRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => '999', 'valid' => true])
            ->willReturn(null);

        // 设置内部解析器的行为 - 使用原始路径和查询参数
        $this->innerParser->expects($this->once())
            ->method('parsePath')
            ->with($path, $query)
            ->willReturn($expectedLink);

        // 执行测试
        $result = $this->pathParser->parsePath($path, $query);

        // 验证结果
        $this->assertSame($expectedLink, $result);
    }

    /**
     * 创建模拟的 PromotionCode 对象
     */
    private function createPromotionCodeMock(string $linkUrl): PromotionCode
    {
        $promotionCode = $this->createMock(PromotionCode::class);
        $promotionCode->expects($this->once())
            ->method('getLinkUrl')
            ->willReturn($linkUrl);
        return $promotionCode;
    }
} 