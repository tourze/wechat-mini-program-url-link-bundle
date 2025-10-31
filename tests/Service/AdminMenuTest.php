<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\MockObject\MockObject;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatMiniProgramUrlLinkBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    private AdminMenu $adminMenu;

    private LinkGeneratorInterface&MockObject $linkGenerator;

    protected function onSetUp(): void
    {
        // AdminMenu测试的初始化逻辑
    }

    private function initializeTestObject(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        // 将Mock的LinkGenerator注入到容器中
        self::getContainer()->set(LinkGeneratorInterface::class, $this->linkGenerator);
        // 从容器获取AdminMenu服务实例
        $this->adminMenu = self::getService(AdminMenu::class);
    }

    public function testServiceIsCallable(): void
    {
        $this->initializeTestObject();
        $this->assertInstanceOf(AdminMenu::class, $this->adminMenu);
    }

    public function testServiceCanBeRetrievedFromContainer(): void
    {
        // 验证服务可以从容器获取
        $this->assertTrue(self::getContainer()->has(AdminMenu::class));
        $adminMenuFromContainer = self::getContainer()->get(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenuFromContainer);
    }

    public function testInvokeCreatesAllMenuItems(): void
    {
        $this->initializeTestObject();

        // 创建根菜单项
        $rootItem = $this->createMock(ItemInterface::class);

        // 创建小程序推广子菜单
        $promotionMenuItem = $this->createMock(ItemInterface::class);

        // 设置LinkGenerator行为 - 总共4个菜单项
        $this->linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturn('/test-url')
        ;

        // 首先返回null，表示需要创建菜单，然后返回promotionMenuItem
        $rootItem->expects($this->any())
            ->method('getChild')
            ->with('小程序推广')
            ->willReturnCallback(function () use ($promotionMenuItem) {
                /** @var int $callCount */
                static $callCount = 0;
                ++$callCount;

                return 1 === $callCount ? null : $promotionMenuItem;
            })
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('小程序推广')
            ->willReturn($promotionMenuItem)
        ;

        // 设置promotionMenuItem的行为 - 创建4个子菜单项
        $expectedMenuItems = ['推广码', 'URL链接', '访问记录', '统计报表'];
        $expectedIcons = ['fas fa-qrcode', 'fas fa-link', 'fas fa-eye', 'fas fa-chart-bar'];

        $promotionMenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function ($name) use ($expectedMenuItems, $expectedIcons) {
                static $callCount = 0;
                /** @var int $callCount */
                $index = $callCount;
                ++$callCount;

                $this->assertContains($name, $expectedMenuItems);

                $menuItem = $this->createMock(ItemInterface::class);
                $menuItem->expects($this->once())
                    ->method('setUri')
                    ->with('/test-url')
                    ->willReturn($menuItem)
                ;
                $menuItem->expects($this->once())
                    ->method('setAttribute')
                    ->with('icon', $expectedIcons[$index])
                    ->willReturn($menuItem)
                ;

                return $menuItem;
            })
        ;

        // 执行菜单构建
        $this->assertInstanceOf(ItemInterface::class, $rootItem);
        ($this->adminMenu)($rootItem);
    }

    public function testInvokeWithExistingPromotionMenu(): void
    {
        $this->initializeTestObject();

        // 创建根菜单项
        $rootItem = $this->createMock(ItemInterface::class);

        // 创建小程序推广子菜单
        $promotionMenuItem = $this->createMock(ItemInterface::class);

        // 设置LinkGenerator行为 - 总共4个菜单项
        $this->linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturn('/some-url')
        ;

        // 总是返回现有的菜单项
        $rootItem->expects($this->any())
            ->method('getChild')
            ->with('小程序推广')
            ->willReturn($promotionMenuItem)
        ;

        // 不应该再创建小程序推广菜单
        $rootItem->expects($this->never())
            ->method('addChild')
        ;

        // 验证子菜单添加 - 创建4个子菜单项
        $promotionMenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function () {
                $menuItem = $this->createMock(ItemInterface::class);
                $menuItem->expects($this->once())
                    ->method('setUri')
                    ->willReturn($menuItem)
                ;
                $menuItem->expects($this->once())
                    ->method('setAttribute')
                    ->willReturn($menuItem)
                ;

                return $menuItem;
            })
        ;

        // 执行菜单构建
        $this->assertInstanceOf(ItemInterface::class, $rootItem);
        ($this->adminMenu)($rootItem);
    }

    public function testInvokeCreatesCorrectMenuItems(): void
    {
        $this->initializeTestObject();

        // 创建根菜单项
        $rootItem = $this->createMock(ItemInterface::class);

        // 创建小程序推广子菜单
        $promotionMenuItem = $this->createMock(ItemInterface::class);

        // 设置LinkGenerator行为 - 总共4个菜单项
        $this->linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturn('/test-url')
        ;

        // 首先返回null，表示需要创建菜单，然后返回promotionMenuItem
        $rootItem->expects($this->any())
            ->method('getChild')
            ->with('小程序推广')
            ->willReturnCallback(function () use ($promotionMenuItem) {
                /** @var int $callCount */
                static $callCount = 0;
                ++$callCount;

                return 1 === $callCount ? null : $promotionMenuItem;
            })
        ;

        $rootItem->expects($this->once())
            ->method('addChild')
            ->with('小程序推广')
            ->willReturn($promotionMenuItem)
        ;

        // 验证特定的菜单项创建
        $menuItems = [];
        $expectedIcons = ['fas fa-qrcode', 'fas fa-link', 'fas fa-eye', 'fas fa-chart-bar'];

        $promotionMenuItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnCallback(function ($name) use (&$menuItems, $expectedIcons) {
                static $callCount = 0;
                /** @var int $callCount */
                $index = $callCount;
                ++$callCount;

                $menuItem = $this->createMock(ItemInterface::class);
                $menuItem->expects($this->once())
                    ->method('setUri')
                    ->with('/test-url')
                    ->willReturn($menuItem)
                ;
                $menuItem->expects($this->once())
                    ->method('setAttribute')
                    ->with('icon', $expectedIcons[$index])
                    ->willReturn($menuItem)
                ;

                $menuItems[] = $name;

                return $menuItem;
            })
        ;

        // 执行菜单构建
        $this->assertInstanceOf(ItemInterface::class, $rootItem);
        ($this->adminMenu)($rootItem);

        // 验证菜单项名称
        $this->assertSame(['推广码', 'URL链接', '访问记录', '统计报表'], $menuItems);
    }
}
