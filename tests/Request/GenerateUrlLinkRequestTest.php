<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Request\GenerateUrlLinkRequest;

class GenerateUrlLinkRequestTest extends TestCase
{
    private GenerateUrlLinkRequest $request;

    protected function setUp(): void
    {
        $this->request = new GenerateUrlLinkRequest();
    }

    /**
     * 测试请求路径是否正确
     */
    public function testGetRequestPath_returnsCorrectPath(): void
    {
        $this->assertEquals('/wxa/generate_urllink', $this->request->getRequestPath());
    }

    /**
     * 测试设置和获取 path 参数
     */
    public function testSetAndGetPath_storesAndReturnsValue(): void
    {
        $path = 'pages/index/index';
        
        $this->request->setPath($path);
        
        $this->assertEquals($path, $this->request->getPath());
    }

    /**
     * 测试设置和获取 query 参数
     */
    public function testSetAndGetQuery_storesAndReturnsValue(): void
    {
        $query = 'id=123&type=product';
        
        $this->request->setQuery($query);
        
        $this->assertEquals($query, $this->request->getQuery());
    }

    /**
     * 测试设置和获取 envVersion 参数
     */
    public function testSetAndGetEnvVersion_storesAndReturnsValue(): void
    {
        $envVersion = 'trial';
        
        $this->request->setEnvVersion($envVersion);
        
        $this->assertEquals($envVersion, $this->request->getEnvVersion());
    }

    /**
     * 测试默认 envVersion 值
     */
    public function testGetEnvVersion_byDefault_returnsRelease(): void
    {
        $this->assertEquals('release', $this->request->getEnvVersion());
    }

    /**
     * 测试设置和获取 expireType 参数
     */
    public function testSetAndGetExpireType_storesAndReturnsValue(): void
    {
        $expireType = 1;
        
        $this->request->setExpireType($expireType);
        
        $this->assertEquals($expireType, $this->request->getExpireType());
    }

    /**
     * 测试设置和获取 expireTime 参数
     */
    public function testSetAndGetExpireTime_storesAndReturnsValue(): void
    {
        $expireTime = time() + 86400;
        
        $this->request->setExpireTime($expireTime);
        
        $this->assertEquals($expireTime, $this->request->getExpireTime());
    }

    /**
     * 测试设置和获取 expireInterval 参数
     */
    public function testSetAndGetExpireInterval_storesAndReturnsValue(): void
    {
        $expireInterval = 7;
        
        $this->request->setExpireInterval($expireInterval);
        
        $this->assertEquals($expireInterval, $this->request->getExpireInterval());
    }

    /**
     * 测试获取请求选项 - 使用失效时间模式
     */
    public function testGetRequestOptions_withExpireTime_returnsCorrectOptions(): void
    {
        $path = 'pages/product/detail';
        $query = 'id=456&category=food';
        $envVersion = 'trial';
        $expireType = 0;
        $expireTime = time() + 86400;
        
        $this->request->setPath($path);
        $this->request->setQuery($query);
        $this->request->setEnvVersion($envVersion);
        $this->request->setExpireType($expireType);
        $this->request->setExpireTime($expireTime);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertEquals($path, $json['path']);
        $this->assertEquals($query, $json['query']);
        $this->assertEquals($envVersion, $json['env_version']);
        $this->assertEquals($expireType, $json['expire_type']);
        $this->assertEquals($expireTime, $json['expire_time']);
    }

    /**
     * 测试获取请求选项 - 使用失效间隔模式
     */
    public function testGetRequestOptions_withExpireInterval_returnsCorrectOptions(): void
    {
        $path = 'pages/product/detail';
        $query = 'id=456&category=food';
        $envVersion = 'trial';
        $expireType = 1;
        $expireInterval = 7;
        
        $this->request->setPath($path);
        $this->request->setQuery($query);
        $this->request->setEnvVersion($envVersion);
        $this->request->setExpireType($expireType);
        $this->request->setExpireInterval($expireInterval);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertEquals($path, $json['path']);
        $this->assertEquals($query, $json['query']);
        $this->assertEquals($envVersion, $json['env_version']);
        $this->assertEquals($expireType, $json['expire_type']);
        $this->assertEquals($expireInterval, $json['expire_interval']);
    }

    /**
     * 测试缺少必要参数时抛出异常 - 失效时间模式
     */
    public function testGetRequestOptions_withExpireTypeZeroButNoExpireTime_throwsException(): void
    {
        $this->request->setExpireType(0);
        $this->request->setExpireTime(null);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('expire_type 为 0 时，expire_time 必填');
        
        $this->request->getRequestOptions();
    }

    /**
     * 测试缺少必要参数时抛出异常 - 失效间隔模式
     */
    public function testGetRequestOptions_withExpireTypeOneButNoExpireInterval_throwsException(): void
    {
        $this->request->setExpireType(1);
        $this->request->setExpireInterval(null);
        
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('expire_type 为 1 时，expire_interval 必填');
        
        $this->request->getRequestOptions();
    }

    /**
     * 测试只有必要参数的最小请求选项
     */
    public function testGetRequestOptions_withMinimalRequiredParams_returnsCorrectOptions(): void
    {
        $expireType = 0;
        $expireTime = time() + 86400;
        
        $this->request->setExpireType($expireType);
        $this->request->setExpireTime($expireTime);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $json = $options['json'];
        $this->assertEquals($expireType, $json['expire_type']);
        $this->assertEquals($expireTime, $json['expire_time']);
        $this->assertEquals('release', $json['env_version']);
        $this->assertArrayNotHasKey('path', $json);
        $this->assertArrayNotHasKey('query', $json);
    }

    /**
     * 测试空路径和查询的情况
     */
    public function testGetRequestOptions_withNullPathAndQuery_omitsThem(): void
    {
        $expireType = 0;
        $expireTime = time() + 86400;
        
        $this->request->setPath(null);
        $this->request->setQuery(null);
        $this->request->setExpireType($expireType);
        $this->request->setExpireTime($expireTime);
        
        $options = $this->request->getRequestOptions();
        
        $json = $options['json'];
        $this->assertArrayNotHasKey('path', $json);
        $this->assertArrayNotHasKey('query', $json);
    }
} 