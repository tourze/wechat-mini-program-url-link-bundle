<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatMiniProgramUrlLinkBundle\Request\QueryUrlLinkRequest;

class QueryUrlLinkRequestTest extends TestCase
{
    private QueryUrlLinkRequest $request;

    protected function setUp(): void
    {
        $this->request = new QueryUrlLinkRequest();
    }

    /**
     * 测试请求路径是否正确
     */
    public function testGetRequestPath_returnsCorrectPath(): void
    {
        $this->assertEquals('/wxa/query_urllink', $this->request->getRequestPath());
    }

    /**
     * 测试设置和获取 URL Link
     */
    public function testSetAndGetUrlLink_storesAndReturnsValue(): void
    {
        $urlLink = 'https://example.com/test-url-link';
        
        $this->request->setUrlLink($urlLink);
        
        $this->assertEquals($urlLink, $this->request->getUrlLink());
    }

    /**
     * 测试获取请求选项包含正确的 JSON 参数
     */
    public function testGetRequestOptions_returnsCorrectJsonPayload(): void
    {
        $urlLink = 'https://example.com/test-url-link';
        $this->request->setUrlLink($urlLink);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals($urlLink, $options['json']['url_link']);
    }

    /**
     * 测试边界情况：空 URL Link
     */
    public function testGetRequestOptions_withEmptyUrlLink_includesEmptyValue(): void
    {
        $this->request->setUrlLink('');
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals('', $options['json']['url_link']);
    }

    /**
     * 测试边界情况：特殊字符 URL Link
     */
    public function testGetRequestOptions_withSpecialCharacters_includesThemCorrectly(): void
    {
        $urlLink = 'https://example.com/test?param=value&special=!@#$%^&*()';
        $this->request->setUrlLink($urlLink);
        
        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals($urlLink, $options['json']['url_link']);
    }
} 