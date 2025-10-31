<?php

namespace WechatMiniProgramUrlLinkBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;

/**
 * @extends AbstractCrudController<VisitLog>
 */
#[AdminCrud(routePath: '/wechat-mini-program-url-link/visit-log', routeName: 'wechat_mini_program_url_link_visit_log')]
final class VisitLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisitLog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('访问记录')
            ->setEntityLabelInPlural('访问记录')
            ->setPageTitle('index', '访问记录列表')
            ->setPageTitle('new', '创建访问记录')
            ->setPageTitle('edit', '编辑访问记录')
            ->setPageTitle('detail', '访问记录详情')
            ->setHelp('index', '推广码访问记录，记录每次用户通过推广码访问的详细信息')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields();
        yield from $this->getAssociationFields();
        yield from $this->getConfigurationFields();
        yield from $this->getOptionsFields();
        yield from $this->getDataFields();
        yield from $this->getTrackingFields();
        yield from $this->getTimestampFields();
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getBasicFields(): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(): iterable
    {
        yield AssociationField::new('code', '推广码')
            ->setRequired(true)
            ->setHelp('关联的推广码')
            ->formatValue(fn ($value, $entity) => $this->formatCodeValue($entity))
        ;

        yield AssociationField::new('user', '用户')
            ->setHelp('访问的用户')
            ->formatValue(fn ($value, $entity) => $this->formatUserValue($entity))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getConfigurationFields(): iterable
    {
        yield ChoiceField::new('envVersion', '打开版本')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => EnvVersion::class])
            ->formatValue(fn ($value) => $this->formatEnvVersionValue($value))
            ->setHelp('小程序打开的版本环境')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getOptionsFields(): iterable
    {
        yield TextField::new('launchOptions', '冷启动参数')
            ->hideOnForm()
            ->hideOnIndex()
            ->hideOnDetail()
            ->formatValue(fn ($value) => $this->formatJsonValue($value))
        ;

        yield TextField::new('enterOptions', '热启动参数')
            ->hideOnForm()
            ->hideOnIndex()
            ->hideOnDetail()
            ->formatValue(fn ($value) => $this->formatJsonValue($value))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getDataFields(): iterable
    {
        yield TextField::new('response', '响应数据')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setHelp('访问时的响应数据')
            ->formatValue(fn ($value) => $this->formatResponseValue($value))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTrackingFields(): iterable
    {
        yield TextField::new('createdFromIp', '访问IP')
            ->hideOnForm()
            ->formatValue(fn ($value) => $this->formatIpValue($value))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields(): iterable
    {
        yield DateTimeField::new('createTime', '访问时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
        ;
    }

    private function formatCodeValue(mixed $entity): string
    {
        if (!($entity instanceof VisitLog) || null === $entity->getCode()) {
            return '-';
        }

        return (string) $entity->getCode()->getName();
    }

    private function formatUserValue(mixed $entity): string
    {
        if (!($entity instanceof VisitLog) || null === $entity->getUser()) {
            return '未登录';
        }

        $user = $entity->getUser();
        if (!method_exists($user, 'getId')) {
            return '已登录用户';
        }

        $id = $user->getId();

        return sprintf('ID: %s', is_scalar($id) ? (string) $id : 'unknown');
    }

    private function formatEnvVersionValue(mixed $value): string
    {
        return $value instanceof EnvVersion ? $value->getLabel() : '';
    }

    private function formatJsonValue(mixed $value): string
    {
        if (null === $value || [] === $value || '' === $value) {
            return '-';
        }

        $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return false !== $encoded ? $encoded : '-';
    }

    private function formatResponseValue(mixed $value): string
    {
        if (null === $value || [] === $value || '' === $value) {
            return '';
        }

        $encoded = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return false !== $encoded ? $encoded : '';
    }

    private function formatIpValue(mixed $value): string
    {
        if (null === $value) {
            return '未知';
        }

        return is_scalar($value) ? (string) $value : '未知';
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW)  // 访问记录通常是系统自动生成的
            ->disable(Action::EDIT) // 访问记录不应该被编辑
            ->disable(Action::DELETE) // 访问记录不应该被删除
            ->reorder(Crud::PAGE_INDEX, [Action::DETAIL])
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        // 构建环境版本选项
        $envVersionChoices = [];
        foreach (EnvVersion::cases() as $case) {
            $envVersionChoices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('code', '推广码'))
            ->add(EntityFilter::new('user', '用户'))
            ->add(ChoiceFilter::new('envVersion', '打开版本')->setChoices($envVersionChoices))
            ->add(DateTimeFilter::new('createTime', '访问时间'))
        ;
    }
}
