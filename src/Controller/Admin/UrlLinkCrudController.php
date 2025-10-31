<?php

namespace WechatMiniProgramUrlLinkBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;

/**
 * @extends AbstractCrudController<UrlLink>
 */
#[AdminCrud(routePath: '/wechat-mini-program-url-link/url-link', routeName: 'wechat_mini_program_url_link_url_link')]
final class UrlLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UrlLink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('URL链接')
            ->setEntityLabelInPlural('URL链接')
            ->setPageTitle('index', 'URL链接列表')
            ->setPageTitle('new', '创建URL链接')
            ->setPageTitle('edit', '编辑URL链接')
            ->setPageTitle('detail', 'URL链接详情')
            ->setHelp('index', '记录微信短链的打开记录')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'urlLink', 'path', 'query', 'visitOpenId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本字段
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        yield TextField::new('urlLink', 'URL链接')
            ->setRequired(true)
            ->setHelp('微信短链URL')
            ->formatValue(function ($value) {
                return sprintf('<code>%s</code>', is_string($value) ? $value : '');
            })
        ;

        yield TextField::new('path', '打开路径')
            ->setHelp('小程序内部路径')
        ;

        yield TextField::new('query', '打开参数')
            ->setHelp('路径参数')
        ;

        yield TextField::new('visitOpenId', '访问者OpenId')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value ? sprintf('<code>%s</code>', is_string($value) ? $value : '') : '-';
            })
        ;

        // 关联字段
        yield AssociationField::new('account', '小程序账号')
            ->setRequired(false)
            ->setHelp('关联的小程序账号')
        ;

        // 配置字段
        yield ChoiceField::new('envVersion', '打开版本')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => EnvVersion::class])
            ->formatValue(function ($value) {
                return $value instanceof EnvVersion ? $value->getLabel() : '';
            })
            ->setHelp('小程序打开的版本环境')
        ;

        yield BooleanField::new('checked', '已检查')
            ->setHelp('是否已经检查过该记录')
        ;

        // JSON字段
        yield TextareaField::new('rawData', '原始数据')
            ->hideOnIndex()
            ->hideOnDetail()
            ->setHelp('保存的原始数据信息')
            ->formatValue(function ($value) {
                return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '';
            })
        ;

        // IP追踪字段
        yield TextField::new('createdFromIp', '创建IP')
            ->hideOnForm()
        ;

        yield TextField::new('updatedFromIp', '更新IP')
            ->hideOnForm()
        ;

        // 时间戳字段
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW) // URL链接通常是系统生成的，不允许手动创建
            ->disable(Action::EDIT) // URL链接不应该编辑
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
            ->add(TextFilter::new('urlLink', 'URL链接'))
            ->add(TextFilter::new('path', '打开路径'))
            ->add(TextFilter::new('visitOpenId', '访问者OpenId'))
            ->add(EntityFilter::new('account', '小程序账号'))
            ->add(ChoiceFilter::new('envVersion', '打开版本')->setChoices($envVersionChoices))
            ->add(BooleanFilter::new('checked', '已检查'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
