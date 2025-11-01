<?php

namespace WechatMiniProgramUrlLinkBundle\Tests\Request;

use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use WechatMiniProgramUrlLinkBundle\Request\QueryUrlLinkRequest;

/**
 * @internal
 */
#[CoversClass(QueryUrlLinkRequest::class)]
final class QueryUrlLinkRequestTest extends RequestTestCase
{
    private QueryUrlLinkRequest $request;

    private function initializeTestObject(): void
    {
        $this->request = new QueryUrlLinkRequest();
    }

    /**
     * 测试请求路径是否正确
     */
    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $this->initializeTestObject();

        $this->assertEquals('/wxa/query_urllink', $this->request->getRequestPath());
    }

    /**
     * 测试设置和获取 URL Link
     */
    public function testSetAndGetUrlLinkStoresAndReturnsValue(): void
    {
        $this->initializeTestObject();

        $urlLink = 'https://example.com/test-url-link';

        $this->request->setUrlLink($urlLink);

        $this->assertEquals($urlLink, $this->request->getUrlLink());
    }

    /**
     * 测试获取请求选项包含正确的 JSON 参数
     */
    public function testGetRequestOptionsReturnsCorrectJsonPayload(): void
    {
        $this->initializeTestObject();

        $urlLink = 'https://example.com/test-url-link';
        $this->request->setUrlLink($urlLink);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals($urlLink, $options['json']['url_link']);
    }

    /**
     * 测试边界情况：空 URL Link
     */
    public function testGetRequestOptionsWithEmptyUrlLinkIncludesEmptyValue(): void
    {
        $this->initializeTestObject();

        $this->request->setUrlLink('');

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals('', $options['json']['url_link']);
    }

    /**
     * 测试边界情况：特殊字符 URL Link
     */
    public function testGetRequestOptionsWithSpecialCharactersIncludesThemCorrectly(): void
    {
        $this->initializeTestObject();

        $urlLink = 'https://example.com/test?param=value&special=!@#$%^&*()';
        $this->request->setUrlLink($urlLink);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('json', $options);
        $this->assertIsArray($options['json']);
        $this->assertArrayHasKey('url_link', $options['json']);
        $this->assertEquals($urlLink, $options['json']['url_link']);
    }
}
