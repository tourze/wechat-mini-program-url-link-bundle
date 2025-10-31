<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;
use Tourze\RoutingAutoLoaderBundle\Service\RoutingAutoLoaderInterface;
use WechatMiniProgramUrlLinkBundle\Controller\ShortLinkController;

#[AutoconfigureTag(name: 'routing.loader')]
class AttributeControllerLoader extends Loader implements RoutingAutoLoaderInterface
{
    public function __construct(
        #[Autowire(service: 'routing.loader.attribute')]
        private readonly AttributeClassLoader $controllerLoader,
    ) {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        return $this->autoload();
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return false;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();

        // 注册所有控制器
        $collection->addCollection($this->controllerLoader->load(ShortLinkController::class));

        return $collection;
    }
}
