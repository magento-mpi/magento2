<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\TestFramework\Helper\ObjectManager;
use \Magento\Framework\Search\Request\Query\Bool as RequestBoolQuery;

class BuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;
    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Filter\Builder
     */
    private $builder;

    /**
     * Set up
     */
    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->adapter = $adapter = $this->getMockBuilder('\Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['quote'])
            ->getMockForAbstractClass();
        $this->adapter->expects($this->any())
            ->method('quote')
            ->will($this->returnArgument(0));

        $rangeBuilder = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Range')
            ->setMethods(['buildFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $rangeBuilder->expects($this->any())
            ->method('buildFilter')
            ->will(
                $this->returnCallback(
                    function (\Magento\Framework\Search\Request\FilterInterface $filter, $isNegation) use ($adapter) {
                        /**
                         * @var \Magento\Framework\Search\Request\Filter\Range $filter
                         * @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter
                         */
                        $fromCondition = '';
                        if (!is_null($filter->getFrom())) {
                            $fromCondition = sprintf(
                                '%s %s %s',
                                $filter->getField(),
                                ($isNegation ? '<' : '>='),
                                $adapter->quote($filter->getFrom())
                            );
                        }
                        $toCondition = '';
                        if (!is_null($filter->getTo())) {
                            $toCondition = sprintf(
                                '%s %s %s',
                                $filter->getField(),
                                ($isNegation ? '>=' : '<'),
                                $adapter->quote($filter->getTo())
                            );
                        }
                        $unionOperator = ($fromCondition and $toCondition)
                            ? ' ' . \Zend_Db_Select::SQL_AND . ' '
                            : '';

                        $condition = $fromCondition . $unionOperator . $toCondition;
                        return $condition;
                    }
                )
            );

        $termBuilder = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\Filter\Builder\Term')
            ->setMethods(['buildFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $termBuilder->expects($this->any())
            ->method('buildFilter')
            ->will(
                $this->returnCallback(
                    function (\Magento\Framework\Search\Request\FilterInterface $filter, $isNegation) use ($adapter) {
                        /**
                         * @var \Magento\Framework\Search\Request\Filter\Term $filter
                         * @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter
                         */
                        return sprintf(
                            '%s %s %s',
                            $filter->getField(),
                            ($isNegation ? '!=' : '='),
                            $adapter->quote($filter->getValue())
                        );
                    }
                )
            );

        $this->builder = $objectManager->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Filter\Builder',
            [
                'range' => $rangeBuilder,
                'term' => $termBuilder,
            ]
        );
    }

    /**
     * @param \Magento\Framework\Search\Request\FilterInterface|\PHPUnit_Framework_MockObject_MockObject $filter
     * @param string $conditionType
     * @param string $expectedResult
     * @dataProvider buildFilterDataProvider
     */
    public function testBuildFilter($filter, $conditionType, $expectedResult)
    {
        $actualResult = $this->builder->build($filter, $conditionType);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function buildFilterDataProvider()
    {
        return array_merge(
            $this->buildTermFilterDataProvider(),
            $this->buildRangeFilterDataProvider(),
            $this->buildBoolFilterDataProvider()
        );
    }

    public function buildTermFilterDataProvider()
    {
        return [
            'termFilter' => [
                'filter' => $this->createTermFilter('term1', 123),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '(term1 = 123)',
            ],
            'termFilterNegative' => [
                'filter' => $this->createTermFilter('term1', 123),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_NOT,
                'expectedResult' => '(term1 != 123)',
            ],
        ];
    }

    /**
     * Data provider for BuildFilter
     *
     * @return array
     */
    public function buildRangeFilterDataProvider()
    {
        return [
            'rangeFilter' => [
                'filter' => $this->createRangeFilter('range1', 0, 10),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '(range1 >= 0 AND range1 < 10)',
            ],
            'rangeFilterNegative' => [
                'filter' => $this->createRangeFilter('range1', 0, 10),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_NOT,
                'expectedResult' => '(range1 < 0 AND range1 >= 10)',
            ]

        ];
    }

    public function buildBoolFilterDataProvider()
    {
        return [
            'boolFilterWithMust' => [
                'filter' => $this->createBoolFilter(
                    [ //must
                        $this->createTermFilter('term1', 1),
                        $this->createRangeFilter('range1', 0, 10),
                    ],
                    [], //should
                    [] // mustNot
                ),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '((term1 = 1) AND (range1 >= 0 AND range1 < 10))',
            ],
            'boolFilterWithShould' => [
                'filter' => $this->createBoolFilter(
                    [], //must
                    [ //should
                        $this->createTermFilter('term1', 1),
                        $this->createRangeFilter('range1', 0, 10),
                    ],
                    [] // mustNot
                ),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '(((term1 = 1) OR (range1 >= 0 AND range1 < 10)))',
            ],
            'boolFilterWithMustNot' => [
                'filter' => $this->createBoolFilter(
                    [], //must
                    [], //should
                    [ // mustNot
                        $this->createTermFilter('term1', 1),
                        $this->createRangeFilter('range1', 0, 10),
                    ]
                ),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '(((term1 != 1) AND (range1 < 0 AND range1 >= 10)))',
            ],
            'boolFilterWithAllFields' => [
                'filter' => $this->createBoolFilter(
                    [ //must
                        $this->createTermFilter('term1', 1),
                        $this->createRangeFilter('range1', 0, 10),
                    ],
                    [ //should
                        $this->createTermFilter('term2', 1),
                        $this->createRangeFilter('range2', 0, 10),
                    ],
                    [ // mustNot
                        $this->createTermFilter('term3', 1),
                        $this->createRangeFilter('range3', 0, 10),
                    ]
                ),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '((term1 = 1) AND (range1 >= 0 AND range1 < 10)'
                    . ' AND ((term2 = 1) OR (range2 >= 0 AND range2 < 10))'
                    . ' AND ((term3 != 1) AND (range3 < 0 AND range3 >= 10)))',
            ],
            'boolFilterInBoolFilter' => [
                'filter' => $this->createBoolFilter(
                    [ //must
                        $this->createTermFilter('term1', 1),
                        $this->createRangeFilter('range1', 0, 10),
                    ],
                    [ //should
                        $this->createTermFilter('term2', 1),
                        $this->createRangeFilter('range2', 0, 10),
                    ],
                    [ // mustNot
                        $this->createTermFilter('term3', 1),
                        $this->createRangeFilter('range3', 0, 10),
                        $this->createBoolFilter(
                            [ //must
                                $this->createTermFilter('term4', 1),
                                $this->createRangeFilter('range4', 0, 10),
                            ],
                            [ //should
                                $this->createTermFilter('term5', 1),
                                $this->createRangeFilter('range5', 0, 10),
                            ],
                            [ // mustNot
                                $this->createTermFilter('term6', 1),
                                $this->createRangeFilter('range6', 0, 10),
                            ]
                        ),
                    ]
                ),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '((term1 = 1) AND (range1 >= 0 AND range1 < 10)'
                    . ' AND ((term2 = 1) OR (range2 >= 0 AND range2 < 10))'
                    . ' AND ((term3 != 1) AND (range3 < 0 AND range3 >= 10)'
                    . ' AND ((term4 != 1) AND (range4 < 0 AND range4 >= 10)'
                    . ' AND ((term5 != 1) OR (range5 < 0 AND range5 >= 10))'
                    . ' AND ((term6 = 1) AND (range6 >= 0 AND range6 < 10)))'
                    . '))',

            ],
            'boolEmpty' => [
                'filter' => $this->createBoolFilter([], [], []),
                'conditionType' => RequestBoolQuery::QUERY_CONDITION_MUST,
                'expectedResult' => '',
            ]
        ];
    }

    /**
     * @param $field
     * @param $value
     * @return \Magento\Framework\Search\Request\Filter\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createTermFilter($field, $value)
    {
        $filter = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Term')
            ->setMethods(['getField', 'getValue'])
            ->disableOriginalConstructor()
            ->getMock();

        $filter->expects($this->exactly(1))
            ->method('getField')
            ->will($this->returnValue($field));
        $filter->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($value));
        return $filter;
    }

    /**
     * @param $field
     * @param $from
     * @param $to
     * @return \Magento\Framework\Search\Request\Filter\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createRangeFilter($field, $from, $to)
    {
        $filter = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Range')
            ->setMethods(['getField', 'getFrom', 'getTo'])
            ->disableOriginalConstructor()
            ->getMock();

        $filter->expects($this->exactly(2))
            ->method('getField')
            ->will($this->returnValue($field));
        $filter->expects($this->atLeastOnce())
            ->method('getFrom')
            ->will($this->returnValue($from));
        $filter->expects($this->atLeastOnce())
            ->method('getTo')
            ->will($this->returnValue($to));
        return $filter;
    }

    /**
     * @param array $must
     * @param array $should
     * @param array $mustNot
     * @return \Magento\Framework\Search\Request\Filter\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createBoolFilter(array $must, array $should, array $mustNot)
    {
        $filter = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Bool')
            ->setMethods(['getMust', 'getShould', 'getMustNot'])
            ->disableOriginalConstructor()
            ->getMock();

        $filter->expects($this->once())
            ->method('getMust')
            ->will($this->returnValue($must));
        $filter->expects($this->once())
            ->method('getShould')
            ->will($this->returnValue($should));
        $filter->expects($this->once())
            ->method('getMustNot')
            ->will($this->returnValue($mustNot));
        return $filter;
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown filter type 'unknownType'
     */
    public function testUnknownFilterType()
    {
        /** @var \Magento\Framework\Search\Request\FilterInterface|\PHPUnit_Framework_MockObject_MockObject $filter */
        $filter = $this->getMockBuilder('Magento\Framework\Search\Request\FilterInterface')
            ->setMethods(['getType'])
            ->getMockForAbstractClass();
        $filter->expects($this->exactly(2))
            ->method('getType')
            ->will($this->returnValue('unknownType'));
        $this->builder->build($filter, RequestBoolQuery::QUERY_CONDITION_MUST);
    }
}
