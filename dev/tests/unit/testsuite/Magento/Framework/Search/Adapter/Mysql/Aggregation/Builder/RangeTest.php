<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder;

use Magento\TestFramework\Helper\ObjectManager;

class RangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Metrics|\PHPUnit_Framework_MockObject_MockObject
     */
    private $metricsBuilder;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapter;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Magento\Framework\Search\Request\BucketInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucket;

    /**
     * @var \Magento\Framework\Search\Request\Aggregation\Range|\PHPUnit_Framework_MockObject_MockObject
     */
    private $range;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Range
     */
    private $builder;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->metricsBuilder = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Metrics'
        )
            ->setMethods(['build'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['fetchAssoc', 'select', 'getCaseSql'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->adapter->expects($this->any())->method('select')->willReturn($this->select);

        $this->resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->disableOriginalConstructor()
            ->getMock();
        $this->resource->expects($this->any())->method('getConnection')->willReturn($this->adapter);

        $this->bucket = $this->getMockBuilder('Magento\Framework\Search\Request\BucketInterface')
            ->setMethods(['getName', 'getRanges'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->range = $this->getMockBuilder('Magento\Framework\Search\Request\Aggregation\Range')
            ->setMethods(['getFrom', 'getTo'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->builder = $helper->getObject(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Range',
            ['metricsBuilder' => $this->metricsBuilder, 'resource' => $this->resource]
        );
    }

    public function testBuild()
    {
        $this->metricsBuilder->expects($this->once())->method('build')->willReturn(['metrics']);
        $this->bucket->expects($this->once())->method('getRanges')->willReturn(
            [$this->range, $this->range, $this->range]
        );
        $this->range->expects($this->at(0))->method('getFrom')->willReturn('');
        $this->range->expects($this->at(1))->method('getTo')->willReturn(50);
        $this->range->expects($this->at(2))->method('getFrom')->willReturn(50);
        $this->range->expects($this->at(3))->method('getTo')->willReturn(100);
        $this->range->expects($this->at(4))->method('getFrom')->willReturn(100);
        $this->range->expects($this->at(5))->method('getTo')->willReturn('');
        $this->adapter->expects($this->once())->method('getCaseSql')->withConsecutive(
            [''],
            [['`value` < 50' => "'*_50'", '`value` BETWEEN 50 AND 100' => "'50_100'", '`value` >= 100' => "'100_*'"]]
        );

        $result = $this->builder->build($this->select, $this->bucket, [1, 2, 3]);
        $this->assertInstanceOf('Magento\Framework\DB\Select', $result);
    }
}
