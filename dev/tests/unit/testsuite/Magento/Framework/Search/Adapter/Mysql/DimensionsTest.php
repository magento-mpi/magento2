<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\Search\Adapter\Mysql\Dimensions as DimensionsBuilder;
use Magento\TestFramework\Helper\ObjectManager;

class DimensionsTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Magento\TestFramework\Helper\ObjectManager */
    private $objectManager;
    /** @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $adapter;
    /** @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject */
    private $resource;
    /** @var \Magento\Framework\App\ScopeInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $scope;
    /** @var \Magento\Framework\App\ScopeResolverInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $scopeResolver;
    /** @var \Magento\Framework\Search\Request\Dimension|\PHPUnit_Framework_MockObject_MockObject */
    private $dimension;
    /** @var DimensionsBuilder */
    private $builder;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->adapter = $this->getMockBuilder('\Magento\Framework\DB\Adapter\AdapterInterface')
            ->disableOriginalConstructor()
            ->setMethods(['quote', 'quoteIdentifier'])
            ->getMockForAbstractClass();

        $escapeValueCallback = function ($value) {
            return '`' . $value . '`';
        };

        $this->adapter->expects($this->once())
            ->method('quote')
            ->will($this->returnCallback($escapeValueCallback));
        $this->adapter->expects($this->once())
            ->method('quoteIdentifier')
            ->will($this->returnCallback($escapeValueCallback));

        $this->resource = $this->getMockBuilder('\Magento\Framework\App\Resource')
            ->disableOriginalConstructor()
            ->setMethods(['getConnection'])
            ->getMock();
        $this->resource->expects($this->once())
            ->method('getConnection')
            ->with(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE)
            ->will($this->returnValue($this->adapter));

        $this->scope = $this->getMockBuilder('\Magento\Framework\App\ScopeInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getId'])
            ->getMockForAbstractClass();

        $this->scopeResolver = $this->getMockBuilder('\Magento\Framework\App\ScopeResolverInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getScope'])
            ->getMockForAbstractClass();

        $this->dimension = $this->getMockBuilder('\Magento\Framework\Search\Request\Dimension')
            ->disableOriginalConstructor()
            ->setMethods(['getName', 'getValue'])
            ->getMock();

        $this->builder = $this->objectManager->getObject(
            '\Magento\Framework\Search\Adapter\Mysql\Dimensions',
            [
                'resource' => $this->resource,
                'scopeResolver' => $this->scopeResolver
            ]
        );
    }

    public function testBuildDimensionWithCustomScope()
    {
        $name = 'customScopeName';
        $value = 'customScopeId';

        $this->dimension->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->dimension->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($value));

        $this->scope->expects($this->never())
            ->method('getId');

        $this->scopeResolver->expects($this->never())
            ->method('getScope');

        $query = $this->builder->build($this->dimension);
        $this->assertEquals(
            sprintf('`%s` = `%s`', $name, $value),
            $query
        );
    }

    public function testBuildDimensionWithDefaultScope()
    {
        $name = 'scope';
        $value = \Magento\Framework\App\ScopeInterface::SCOPE_DEFAULT;
        $scopeId = -123456;

        $this->dimension->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($name));
        $this->dimension->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($value));

        $this->scope->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($scopeId));

        $this->scopeResolver->expects($this->once())
            ->method('getScope')
            ->with($value)
            ->will($this->returnValue($this->scope));

        $query = $this->builder->build($this->dimension);
        $this->assertEquals(
            sprintf('`%s` = `%s`', \Magento\Framework\Search\Adapter\Mysql\Dimensions::STORE_FIELD_NAME, $scopeId),
            $query
        );
    }
}
