<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Url;

use Magento\TestFramework\Helper\ObjectManager;

class QueryParamsResolverTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Url\QueryParamsResolver */
    protected $object;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->object = $objectManager->getObject('Magento\Framework\Url\QueryParamsResolver');
    }

    public function testGetQuery()
    {
        $this->object->addQueryParams(['foo' => 'bar', 'true' => 'false']);
        $this->assertEquals('foo=bar&true=false', $this->object->getQuery());
    }

    public function testGetQueryEscaped()
    {
        $this->object->addQueryParams(['foo' => 'bar', 'true' => 'false']);
        $this->assertEquals('foo=bar&amp;true=false', $this->object->getQuery(true));
    }

    public function testSetQuery()
    {
        $this->object->setQuery('foo=bar&true=false');
        $this->assertEquals(['foo' => 'bar', 'true' => 'false'], $this->object->getQueryParams());
    }

    public function testSetQueryIdempotent()
    {
        $this->object->setQuery(null);
        $this->assertEquals([], $this->object->getQueryParams());
    }

    public function testSetQueryParam()
    {
        $this->object->setQueryParam('foo', 'bar');
        $this->object->setQueryParam('true', 'false');
        $this->object->setQueryParam('foo', 'bar');
        $this->assertEquals(['foo' => 'bar', 'true' => 'false'], $this->object->getQueryParams());
    }

    public function testSetQueryParams()
    {
        $this->object->setQueryParams(['foo' => 'bar', 'true' => 'false']);
        $this->assertEquals(['foo' => 'bar', 'true' => 'false'], $this->object->getQueryParams());
    }

    public function testAddQueryParamsIdempotent()
    {
        $this->object->setData('query_params', ['foo' => 'bar', 'true' => 'false']);
        $this->object->addQueryParams(['foo' => 'bar', 'true' => 'false']);
        $this->assertEquals(['foo' => 'bar', 'true' => 'false'], $this->object->getQueryParams());
    }
}
