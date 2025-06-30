<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Integration\Service;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\RouteCollection;
use WechatMiniProgramUrlLinkBundle\Service\AttributeControllerLoader;

class AttributeControllerLoaderTest extends TestCase
{
    public function testServiceCreation(): void
    {
        $controllerLoader = $this->createMock(AttributeClassLoader::class);
        
        $service = new AttributeControllerLoader($controllerLoader);
        
        $this->assertInstanceOf(AttributeControllerLoader::class, $service);
    }

    public function testAutoload(): void
    {
        $controllerLoader = $this->createMock(AttributeClassLoader::class);
        $routeCollection = new RouteCollection();
        
        $controllerLoader->expects($this->once())
            ->method('load')
            ->willReturn($routeCollection);
        
        $service = new AttributeControllerLoader($controllerLoader);
        $result = $service->autoload();
        
        $this->assertInstanceOf(RouteCollection::class, $result);
    }
}