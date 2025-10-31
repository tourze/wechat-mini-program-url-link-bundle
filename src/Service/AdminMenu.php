<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatMiniProgramUrlLinkBundle\Entity\DailyStatus;
use WechatMiniProgramUrlLinkBundle\Entity\PromotionCode;
use WechatMiniProgramUrlLinkBundle\Entity\UrlLink;
use WechatMiniProgramUrlLinkBundle\Entity\VisitLog;

/**
 * 微信小程序 URL Link 菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('小程序推广')) {
            $item->addChild('小程序推广');
        }

        $promotionMenu = $item->getChild('小程序推广');
        if (null === $promotionMenu) {
            return;
        }

        // 推广码菜单
        $promotionMenu->addChild('推广码')
            ->setUri($this->linkGenerator->getCurdListPage(PromotionCode::class))
            ->setAttribute('icon', 'fas fa-qrcode')
        ;

        // URL Link菜单
        $promotionMenu->addChild('URL链接')
            ->setUri($this->linkGenerator->getCurdListPage(UrlLink::class))
            ->setAttribute('icon', 'fas fa-link')
        ;

        // 访问记录菜单
        $promotionMenu->addChild('访问记录')
            ->setUri($this->linkGenerator->getCurdListPage(VisitLog::class))
            ->setAttribute('icon', 'fas fa-eye')
        ;

        // 统计报表菜单
        $promotionMenu->addChild('统计报表')
            ->setUri($this->linkGenerator->getCurdListPage(DailyStatus::class))
            ->setAttribute('icon', 'fas fa-chart-bar')
        ;
    }
}
