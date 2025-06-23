<?php

namespace WechatMiniProgramUrlLinkBundle\Service;

use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;
use WechatMiniProgramUrlLinkBundle\Controller\ShortLinkController;

class AttributeControllerLoader
{
    private AttributeClassLoader $controllerLoader;

    public function __construct(AttributeClassLoader $controllerLoader)
    {
        $this->controllerLoader = $controllerLoader;
    }

    public function autoload(): RouteCollection
    {
        $collection = new RouteCollection();
        
        // 注册所有控制器
        $collection->addCollection($this->controllerLoader->load(ShortLinkController::class));
        
        return $collection;
    }
}