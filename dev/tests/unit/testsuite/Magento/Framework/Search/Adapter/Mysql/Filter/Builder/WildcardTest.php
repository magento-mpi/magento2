<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\TestFramework\Helper\ObjectManager;

class WildcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;
    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;
    /**
     * @var \Magento\Framework\Search\Request\Filter\Term|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestFilter;
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Wildcard
     */
    private $filter;

    /**
     * Set up
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestFilter = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Term')
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = $this->getMockBuilder('\Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['quote'])
            ->getMockForAbstractClass();

        $this->resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->once())
            ->method('getConnection')
            ->with(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE)
            ->will($this->returnValue($this->adapter));

        $this->filter = $objectManager->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Wildcard',
            [
                'resource' => $this->resource,
            ]
        );
    }

    /**
     * @param string $field
     * @param string $value
     * @param string $expectedResult
     * @dataProvider buildQueryDataProvider
     */
    public function testBuildQuery($field, $value, $expectedResult)
    {
        $this->requestFilter->expects($this->once())
            ->method('getField')
            ->will($this->returnValue($field));
        $this->requestFilter->expects($this->once())->method('getValue')->willReturn($value);
        $this->adapter->expects($this->once())->method('quote')->willReturnArgument(0);

        $actualResult = $this->filter->buildFilter($this->requestFilter);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for BuildQuery
     * @return array
     */
    public function buildQueryDataProvider()
    {
        return [
            [
                'field' => 'testField',
                'value' => 'testValue',
                'expectedResult' => "testField LIKE %testValue%",
            ],
            [
                'field' => 'testField2',
                'value' => 'testValue2',
                'expectedResult' => "testField2 LIKE %testValue2%",
            ],
        ];
    }
}
