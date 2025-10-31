<?php

namespace WechatMiniProgramUrlLinkBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use WechatMiniProgramBundle\Enum\EnvVersion;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Repository\PromotionCodeRepository;
use WechatMiniProgramUrlLinkBundle\Service\PromotionCodeQrcodeService;

/**
 * @extends AbstractCrudController<PromotionCode>
 */
#[AdminCrud(routePath: '/wechat-mini-program-url-link/promotion-code', routeName: 'wechat_mini_program_url_link_promotion_code')]
final class PromotionCodeCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PromotionCodeQrcodeService $qrcodeService,
        private readonly EntityManagerInterface $entityManager,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly PromotionCodeRepository $promotionCodeRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return PromotionCode::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('推广码')
            ->setEntityLabelInPlural('推广码')
            ->setPageTitle('index', '推广码列表')
            ->setPageTitle('new', '创建推广码')
            ->setPageTitle('edit', '编辑推广码')
            ->setPageTitle('detail', '推广码详情')
            ->setHelp('index', '管理小程序推广码，支持生成短链接和二维码')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['id', 'name', 'code', 'linkUrl'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->getBasicFields();
        yield from $this->getAssociationFields();
        yield from $this->getConfigurationFields();
        yield from $this->getTimeFields();
        yield from $this->getImageFields();
        yield from $this->getShortLinkFields();
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

        yield TextField::new('name', '名称')
            ->setRequired(true)
            ->setHelp('推广码的名称，便于识别和管理')
        ;

        yield TextField::new('code', '唯一码')
            ->formatValue(fn ($value) => $this->formatCodeValue($value))
        ;

        yield TextField::new('linkUrl', '推广链接')
            ->setRequired(true)
            ->setHelp('推广的目标链接')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getAssociationFields(): iterable
    {
        yield AssociationField::new('account', '小程序账号')
            ->setRequired(false)
            ->setHelp('关联的小程序账号')
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

        yield BooleanField::new('forceLogin', '强制授权')
            ->setHelp('是否要求用户必须授权登录')
        ;

        yield BooleanField::new('valid', '有效')
            ->setHelp('推广码是否有效')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimeFields(): iterable
    {
        yield DateTimeField::new('startTime', '开始时间')
            ->setHelp('推广活动开始时间')
        ;

        yield DateTimeField::new('endTime', '结束时间')
            ->setHelp('推广活动结束时间')
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getImageFields(): iterable
    {
        yield ImageField::new('imageUrl', '小程序码')
            ->setBasePath('/uploads')
            ->setHelp('推广小程序码图片')
            ->hideOnForm()
            ->formatValue(fn ($value) => $this->formatImageValue($value))
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getShortLinkFields(): iterable
    {
        yield TextField::new('shortLinkTemp', '临时短链')
            ->hideOnForm()
            ->formatValue(fn ($value) => $this->formatShortLinkValue($value))
        ;

        yield TextField::new('shortLinkPermanent', '永久短链')
            ->hideOnForm()
            ->formatValue(fn ($value) => $this->formatShortLinkValue($value))
        ;

        yield DateTimeField::new('shortLinkTempCreateTime', '短链生成时间')
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTrackingFields(): iterable
    {
        yield TextField::new('createdFromIp', '创建IP')
            ->hideOnForm()
        ;

        yield TextField::new('updatedFromIp', '更新IP')
            ->hideOnForm()
        ;
    }

    /**
     * @return iterable<FieldInterface>
     */
    private function getTimestampFields(): iterable
    {
        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
        ;
    }

    private function formatCodeValue(mixed $value): string
    {
        return sprintf('<code>%s</code>', is_string($value) ? $value : '');
    }

    private function formatEnvVersionValue(mixed $value): string
    {
        return $value instanceof EnvVersion ? $value->getLabel() : '';
    }

    private function formatImageValue(mixed $value): string
    {
        if (null === $value || '' === $value) {
            return '<span class="text-muted">未生成</span>';
        }

        return sprintf(
            '<img src="/uploads/%s" alt="小程序码" style="max-width: 100px; max-height: 100px;" />',
            is_string($value) ? $value : ''
        );
    }

    private function formatShortLinkValue(mixed $value): string
    {
        if (null === $value || '' === $value) {
            return '-';
        }

        $safeValue = is_string($value) ? $value : '';

        return sprintf('<a href="%s" target="_blank">%s</a>', $safeValue, $safeValue);
    }

    public function configureActions(Actions $actions): Actions
    {
        $generateQrcode = Action::new('generateQrcode', '生成小程序码')
            ->linkToCrudAction('generateQrcode')
            ->setCssClass('btn btn-info')
            ->setIcon('fa fa-qrcode')
            ->displayIf(static function ($entity) {
                return $entity instanceof PromotionCode && null === $entity->getImageUrl();
            })
        ;

        $regenerateQrcode = Action::new('regenerateQrcode', '重新生成小程序码')
            ->linkToCrudAction('generateQrcode')
            ->setCssClass('btn btn-warning')
            ->setIcon('fa fa-refresh')
            ->displayIf(static function ($entity) {
                return $entity instanceof PromotionCode && null !== $entity->getImageUrl();
            })
        ;

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $generateQrcode)
            ->add(Crud::PAGE_INDEX, $regenerateQrcode)
            ->add(Crud::PAGE_DETAIL, $generateQrcode)
            ->add(Crud::PAGE_DETAIL, $regenerateQrcode)
            ->add(Crud::PAGE_EDIT, $generateQrcode)
            ->add(Crud::PAGE_EDIT, $regenerateQrcode)
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
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('code', '唯一码'))
            ->add(EntityFilter::new('account', '小程序账号'))
            ->add(ChoiceFilter::new('envVersion', '打开版本')->setChoices($envVersionChoices))
            ->add(BooleanFilter::new('forceLogin', '强制授权'))
            ->add(BooleanFilter::new('valid', '有效'))
            ->add(DateTimeFilter::new('startTime', '开始时间'))
            ->add(DateTimeFilter::new('endTime', '结束时间'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
        ;
    }

    #[AdminAction(routeName: 'wechat_mini_program_url_link_promotion_code_generate_qrcode', routePath: '/wechat-mini-program-url-link/promotion-code/generate-qrcode')]
    public function generateQrcode(AdminContext $context): mixed
    {
        // 获取实体ID
        $entityId = $context->getRequest()->query->get('entityId');
        if (null === $entityId) {
            $this->addFlash('danger', '缺少实体ID参数');

            return $this->redirect($this->adminUrlGenerator->setController(self::class)
                ->setAction(Crud::PAGE_INDEX)
                ->generateUrl());
        }

        // 直接从数据库查询实体
        $entity = $this->promotionCodeRepository->find($entityId);
        if (null === $entity) {
            $this->addFlash('danger', '找不到指定的推广码');

            return $this->redirect($this->adminUrlGenerator->setController(self::class)
                ->setAction(Crud::PAGE_INDEX)
                ->generateUrl());
        }

        // 检查是否设置了小程序账号
        if (null === $entity->getAccount()) {
            $this->addFlash('danger', '请先设置小程序账号');

            return $this->redirect($this->adminUrlGenerator->setController(self::class)
                ->setAction(Crud::PAGE_EDIT)
                ->setEntityId($entity->getId())
                ->generateUrl());
        }

        // 生成小程序码
        $imageUrl = $this->qrcodeService->generateQrcode($entity);

        if (null !== $imageUrl) {
            // 更新实体
            $entity->setImageUrl($imageUrl);
            $this->entityManager->flush();

            $this->addFlash('success', '小程序码生成成功');
        } else {
            $this->addFlash('danger', '小程序码生成失败，请查看日志');
        }

        // 重定向到详情页
        return $this->redirect($this->adminUrlGenerator->setController(self::class)
            ->setAction(Crud::PAGE_DETAIL)
            ->setEntityId($entity->getId())
            ->generateUrl());
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);

        // 如果设置了小程序账号，自动生成小程序码
        // PHPStan已确定$entityInstance为PromotionCode类型，无需instanceof检查
        if (null !== $entityInstance->getAccount()) {
            $imageUrl = $this->qrcodeService->generateQrcode($entityInstance);
            if (null !== $imageUrl) {
                $entityInstance->setImageUrl($imageUrl);
                $entityManager->flush();
            }
        }
    }

    public function deleteEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // 删除小程序码图片
        // PHPStan已确定$entityInstance为PromotionCode类型，无需instanceof检查
        $this->qrcodeService->deleteQrcode($entityInstance);

        parent::deleteEntity($entityManager, $entityInstance);
    }
}
