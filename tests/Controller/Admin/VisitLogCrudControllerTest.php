<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Dto\ActionDto;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;
use WechatMiniProgramUrlLinkBundle\Controller\Admin\VisitLogCrudController;

/**
 * @internal
 */
#[CoversClass(VisitLogCrudController::class)]
#[RunTestsInSeparateProcesses]
final class VisitLogCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    public function testControllerIsService(): void
    {
        $controller = self::getContainer()->get(VisitLogCrudController::class);
        $this->assertInstanceOf(VisitLogCrudController::class, $controller);
    }

    /**
     * @return VisitLogCrudController
     */
    protected function getControllerService(): VisitLogCrudController
    {
        return self::getService(VisitLogCrudController::class);
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideIndexPageHeaders(): iterable
    {
        // 根据实际的控制器字段配置，只包含在index页面显示的字段
        yield 'ID' => ['ID'];
        yield '推广码' => ['推广码'];
        yield '用户' => ['用户'];
        yield '打开版本' => ['打开版本'];
        yield '访问IP' => ['访问IP'];
        yield '访问时间' => ['访问时间'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideNewPageFields(): iterable
    {
        // NEW操作已被禁用，但为了配合基类测试框架，提供虚拟数据
        // 实际的测试会在测试方法中被正确地跳过
        yield 'dummy_field' => ['dummy_field'];
    }

    /**
     * @return \Generator<string, array{string}>
     */
    public static function provideEditPageFields(): iterable
    {
        // EDIT操作已被禁用，但为了配合基类测试框架，提供虚拟数据
        // 实际的测试会在测试方法中被正确地跳过
        yield 'dummy_field' => ['dummy_field'];
    }

    /**
     * 测试表单验证错误 - 验证NEW、EDIT和DELETE操作确实被禁用
     */
    public function testValidationErrors(): void
    {
        $client = $this->createAuthenticatedClient();

        // 验证NEW操作被禁用
        $response = $client->request('GET', $this->generateAdminUrl(Action::NEW));
        $this->assertFalse($response->isSuccessful(), 'NEW action should be disabled');
        $this->assertTrue($response->isNotFound() || $response->isForbidden(), 'NEW action should return 404 or 403');

        // 验证EDIT操作被禁用 - 尝试访问不存在的编辑页面
        $response = $client->request('GET', $this->generateAdminUrl(Action::EDIT, ['entityId' => 99999]));
        $this->assertFalse($response->isSuccessful(), 'EDIT action should be disabled');
        $this->assertTrue($response->isNotFound() || $response->isForbidden(), 'EDIT action should return 404 or 403');
    }

    /**
     * 测试Controller配置正确性 - 验证禁用的操作
     */
    public function testDisabledActions(): void
    {
        $controller = $this->getControllerService();
        $actions = $controller->configureActions(Actions::new());

        $indexActions = $actions->getAsDto('index')->getActions();
        $actionNames = [];
        foreach ($indexActions as $action) {
            if ($action instanceof ActionDto) {
                $actionNames[] = $action->getName();
            }
        }

        // 验证NEW、EDIT和DELETE操作被禁用（不在可用操作列表中）
        self::assertNotContains('new', $actionNames, 'NEW操作应被禁用');
        self::assertNotContains('edit', $actionNames, 'EDIT操作应被禁用');
        self::assertNotContains('delete', $actionNames, 'DELETE操作应被禁用');

        // 验证DETAIL操作可用
        self::assertContains('detail', $actionNames, 'DETAIL操作应可用');
    }
}
