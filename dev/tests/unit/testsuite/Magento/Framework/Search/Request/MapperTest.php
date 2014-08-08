<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Request;

use Magento\TestFramework\Helper\ObjectManager;

class MapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $helper;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Search\Request\Query\Match|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryMatch;

    /**
     * @var \Magento\Framework\Search\Request\Query\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryBool;

    /**
     * @var \Magento\Framework\Search\Request\Query\Filter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $queryFilter;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Term|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterTerm;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Range|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRange;

    /**
     * @var \Magento\Framework\Search\Request\Filter\Bool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterBool;

    protected function setUp()
    {
        $this->helper = new ObjectManager($this);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryMatch = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Match')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryBool = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Bool')
            ->disableOriginalConstructor()
            ->getMock();

        $this->queryFilter = $this->getMockBuilder('Magento\Framework\Search\Request\Query\Filter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterTerm = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Term')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRange = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Range')
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterBool = $this->getMockBuilder('Magento\Framework\Search\Request\Filter\Bool')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testGetBucketsTermBucket()
    {
        $bucket = [
            "name" => "category_bucket",
            "field" => "category",
            "metric" => [
                ["type" => "sum"],
                ["type" => "count"],
                ["type" => "min"],
                ["type" => "max"]
            ],
            "type" => "termBucket",
        ];
        $metricClass = 'Magento\Framework\Search\Request\Aggregation\Metric';
        $bucketClass = 'Magento\Framework\Search\Request\Aggregation\TermBucket';
        $arguments = [
            'name' => $bucket['name'],
            'field' => $bucket['field'],
            'metrics' => [null, null, null, null],
        ];
        $this->objectManager->expects($this->any())->method('create')
            ->withConsecutive(
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][0]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][1]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][2]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][3]['type']])],
                [$this->equalTo($bucketClass), $this->equalTo($arguments)]
            )
            ->will($this->returnValue(null));

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [],
                'aggregation' => [$bucket]
            ]
        );
        $mapper->getBuckets();
    }

    public function testGetBucketsRangeBucket()
    {
        $bucket = [
            "name" => "price_bucket",
            "field" => "price",
            "metric" => [
                ["type" => "sum"],
                ["type" => "count"],
                ["type" => "min"],
                ["type" => "max"]
            ],
            "range" => [
                ["from" => "", "to" => "50"],
                ["from" => "50", "to" => "100"],
                ["from" => "100", "to" => ""],
            ],
            "type" => "rangeBucket",
        ];
        $metricClass = 'Magento\Framework\Search\Request\Aggregation\Metric';
        $bucketClass = 'Magento\Framework\Search\Request\Aggregation\RangeBucket';
        $rangeClass = 'Magento\Framework\Search\Request\Aggregation\Range';
        $arguments = [
            'name' => $bucket['name'],
            'field' => $bucket['field'],
            'metrics' => [null, null, null, null],
            'ranges' => [null, null, null],
        ];
        $this->objectManager->expects($this->any())->method('create')
            ->withConsecutive(
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][0]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][1]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][2]['type']])],
                [$this->equalTo($metricClass), $this->equalTo(['type' => $bucket['metric'][3]['type']])],
                [
                    $this->equalTo($rangeClass),
                    $this->equalTo(['from' => $bucket['range'][0]['from'], 'to' => $bucket['range'][0]['to']])
                ],
                [
                    $this->equalTo($rangeClass),
                    $this->equalTo(['from' => $bucket['range'][1]['from'], 'to' => $bucket['range'][1]['to']])
                ],
                [
                    $this->equalTo($rangeClass),
                    $this->equalTo(['from' => $bucket['range'][2]['from'], 'to' => $bucket['range'][2]['to']])
                ],
                [
                    $this->equalTo($bucketClass),
                    $this->equalTo($arguments)
                ]
            )
            ->will($this->returnValue(null));

        /** @var \Magento\Framework\Search\Request\Mapper $mapper */
        $mapper = $this->helper->getObject(
            'Magento\Framework\Search\Request\Mapper',
            [
                'objectManager' => $this->objectManager,
                'queries' => [],
                'aggregation' => [$bucket]
            ]
        );
        $mapper->getBuckets();
    }
}
