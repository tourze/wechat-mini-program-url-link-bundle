<?php

namespace WechatMiniProgramUrlLinkBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineIpBundle\DoctrineIpBundle;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\DoctrineTrackBundle\DoctrineTrackBundle;
use Tourze\DoctrineUserBundle\DoctrineUserBundle;
use Tourze\FileStorageBundle\FileStorageBundle;
use Tourze\RoutingAutoLoaderBundle\RoutingAutoLoaderBundle;
use WechatMiniProgramBundle\WechatMiniProgramBundle;
use WechatMiniProgramQrcodeLinkBundle\WechatMiniProgramQrcodeLinkBundle;
use WeuiBundle\WeuiBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatMiniProgramUrlLinkBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            DoctrineIpBundle::class => ['all' => true],
            DoctrineTrackBundle::class => ['all' => true],
            DoctrineUserBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            WeuiBundle::class => ['all' => true],
            WechatMiniProgramBundle::class => ['all' => true],
            WechatMiniProgramQrcodeLinkBundle::class => ['all' => true],
            SecurityBundle::class => ['all' => true],
            RoutingAutoLoaderBundle::class => ['all' => true],
            FileStorageBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
