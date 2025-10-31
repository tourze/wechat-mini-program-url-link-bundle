<?php

namespace WechatMiniProgramUrlLinkBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;

/**
 * @extends AbstractCrudController<DailyStatus>
 */
#[AdminCrud(routePath: '/wechat-mini-program-url-link/daily-status', routeName: 'wechat_mini_program_url_link_daily_status')]
final class DailyStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DailyStatus::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('日统计')
            ->setEntityLabelInPlural('日统计')
            ->setPageTitle('index', '访问日统计列表')
            ->setPageTitle('new', '创建日统计')
            ->setPageTitle('edit', '编辑日统计')
            ->setPageTitle('detail', '日统计详情')
            ->setHelp('index', '推广码访问记录的每日统计数据')
            ->setDefaultSort(['date' => 'DESC', 'id' => 'DESC'])
            ->setSearchFields(['id'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        // 基本字段
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->hideOnForm()
        ;

        // 关联字段
        yield AssociationField::new('code', '推广码')
            ->setRequired(true)
            ->setHelp('关联的推广码')
            ->formatValue(function ($value, $entity) {
                if (!$entity instanceof DailyStatus) {
                    return '-';
                }
                $code = $entity->getCode();

                return null !== $code ? $code->getName() : '-';
            })
        ;

        // 统计字段
        yield DateField::new('date', '统计日期')
            ->setRequired(true)
            ->setHelp('统计的日期')
            ->formatValue(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : '-';
            })
        ;

        yield NumberField::new('total', '访问次数')
            ->setRequired(true)
            ->setHelp('当日的访问总次数')
            ->formatValue(function ($value) {
                return number_format(is_numeric($value) ? (float) $value : 0);
            })
        ;

        // 时间戳字段
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->hideOnIndex()
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW) // 日统计通常是系统自动生成的
            ->disable(Action::EDIT) // 日统计数据不应该编辑
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('code', '推广码'))
            ->add(DateTimeFilter::new('date', '统计日期'))
            ->add(NumericFilter::new('total', '访问次数'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }
}
