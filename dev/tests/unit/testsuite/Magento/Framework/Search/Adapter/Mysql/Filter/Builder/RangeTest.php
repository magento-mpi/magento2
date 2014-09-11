<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\TestFramework\Helper\ObjectManager;

class RangeTest extends \PHPUnit_Framework_TestCase
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
     * @var \Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Range
     */
    private $filter;

    /**
     * Set Up
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);
        $this->requestFilter = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Range')
            ->setMethods(['getField', 'getFrom', 'getTo'])
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
            'Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Range',
            [
                'resource' => $this->resource,
            ]
        );
    }

    /**
     * @param string $field
     * @param string $from
     * @param string $to
     * @param bool $isNegation
     * @param string $expectedResult
     * @dataProvider buildQueryDataProvider
     */
    public function testBuildQuery($field, $from, $to, $isNegation, $expectedResult)
    {
        $this->requestFilter->expects($this->any())
            ->method('getField')
            ->will($this->returnValue($field));
        $this->requestFilter->expects($this->atLeastOnce())
            ->method('getFrom')
            ->will($this->returnValue($from));
        $this->requestFilter->expects($this->atLeastOnce())
            ->method('getTo')
            ->will($this->returnValue($to));
        $this->adapter->expects($this->any())
            ->method('quote')
            ->will(
                $this->returnCallback(
                    function ($value) {
                        return '\'' . $value . '\'';
                    }
                )
            );

        $actualResult = $this->filter->buildFilter($this->requestFilter, $isNegation);
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * Data provider for BuildQuery
     *
     * @return array
     */
    public function buildQueryDataProvider()
    {
        return [
            'rangeWithStrings' => [
                'field' => 'testField',
                'from' => '0',
                'to' => '10',
                'isNegation' => false,
                'expectedResult' => 'testField >= \'0\' AND testField < \'10\'',
            ],
            'rangeWithIntegers' => [
                'field' => 'testField',
                'from' => 50,
                'to' => 50,
                'isNegation' => false,
                'expectedResult' => 'testField >= \'50\' AND testField < \'50\'',
            ],
            'rangeWithFloats' => [
                'field' => 'testField',
                'from' => 50.5,
                'to' => 55.5,
                'isNegation' => false,
                'expectedResult' => 'testField >= \'50.5\' AND testField < \'55.5\'',
            ],
            'rangeWithStringsNegative' => [
                'field' => 'testField',
                'from' => '0',
                'to' => '10',
                'isNegation' => true,
                'expectedResult' => 'testField < \'0\' AND testField >= \'10\'',
            ],
            'rangeWithoutFromValue' => [
                'field' => 'testField',
                'from' => null,
                'to' => 50,
                'isNegation' => false,
                'expectedResult' => 'testField < \'50\'',
            ],
            'rangeWithoutFromValueNegative' => [
                'field' => 'testField',
                'from' => null,
                'to' => 50,
                'isNegation' => true,
                'expectedResult' => 'testField >= \'50\'',
            ],
            'rangeWithoutToValue' => [
                'field' => 'testField',
                'from' => 50,
                'to' => null,
                'isNegation' => false,
                'expectedResult' => 'testField >= \'50\'',
            ],
            'rangeWithoutToValueNegative' => [
                'field' => 'testField',
                'from' => 50,
                'to' => null,
                'isNegation' => true,
                'expectedResult' => 'testField < \'50\'',
            ],
            'rangeWithEmptyValues' => [
                'field' => 'testField',
                'from' => null,
                'to' => null,
                'isNegation' => false,
                'expectedResult' => '',
            ],
            'rangeWithEmptyValuesNegative' => [
                'field' => 'testField',
                'from' => null,
                'to' => null,
                'isNegation' => true,
                'expectedResult' => '',
            ],
        ];
    }
}
