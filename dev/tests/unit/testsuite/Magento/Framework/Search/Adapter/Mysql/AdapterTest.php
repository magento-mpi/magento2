<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql;

use Magento\Framework\App\Resource;
use Magento\Framework\App\Resource\Config;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container as AggregationContainer;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\TestFramework\Helper\ObjectManager;

class AdapterTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ResponseFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $connectionAdapter;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Mapper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mapper;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Adapter
     */
    private $adapter;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Search\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $request;

    /**
     * @var \Magento\Framework\DB\Select|\PHPUnit_Framework_MockObject_MockObject
     */
    private $select;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $resource;

    /**
     * @var BucketInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucket;

    /**
     * @var \Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Term|\PHPUnit_Framework_MockObject_MockObject
     */
    private $termBuilder;

    /**
     * @var DataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProvider;

    /**
     * @var \Magento\Framework\Search\EntityMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityMetadata;

    /**
     * @var DataProviderContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderContainer;

    /**
     * @var AggregationContainer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregationContainer;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->request = $this->getMockBuilder('Magento\Framework\Search\RequestInterface')
            ->setMethods(['getAggregation'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->resource = $this->getMockBuilder('Magento\Framework\App\Resource')
            ->setMethods(['getConnection'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->select = $this->getMockBuilder('Magento\Framework\DB\Select')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->connectionAdapter = $this->getMockBuilder('Magento\Framework\DB\Adapter\AdapterInterface')
            ->setMethods(['fetchAssoc'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->resource->expects($this->any())
            ->method('getConnection')
            ->with(Resource::DEFAULT_READ_RESOURCE)
            ->will($this->returnValue($this->connectionAdapter));

        $this->mapper = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\Mapper')
            ->setMethods(['buildQuery'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseFactory = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\ResponseFactory')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->bucket = $this->getMockBuilder('Magento\Framework\Search\Request\BucketInterface')
            ->setMethods(['getType', 'getName'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->termBuilder = $this->getMockBuilder('Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Term')
            ->setMethods(['build'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->aggregationContainer = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container'
        )
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->aggregationContainer->expects($this->once())->method('get')->willReturn($this->termBuilder);

        $this->dataProvider = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface'
        )
            ->setMethods(['getDataSet'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->entityMetadata = $this->getMockBuilder('Magento\Framework\Search\EntityMetadata')
            ->setMethods(['getEntityId'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataProviderContainer = $this->getMockBuilder(
            'Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer'
        )
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProviderContainer->expects($this->any())->method('get')->willReturn($this->dataProvider);

        $this->adapter = $this->objectManager->getObject(
            '\Magento\Framework\Search\Adapter\Mysql\Adapter',
            [
                'mapper' => $this->mapper,
                'responseFactory' => $this->responseFactory,
                'resource' => $this->resource,
                'entityMetadata' => $this->entityMetadata,
                'dataProviderContainer' => $this->dataProviderContainer,
                'aggregationContainer' => $this->aggregationContainer
            ]
        );
    }

    public function testQuery()
    {
        $selectResult = [
            'documents' => [
                [
                    'product_id' => 1,
                    'sku' => 'Product'
                ]
            ],
            'aggregations' => [
                'aggregation_name' => [
                    'aggregation1' => [1, 3],
                    'aggregation2' => [2, 4]
                ]
            ]
        ];

        $this->connectionAdapter->expects($this->at(0))
            ->method('fetchAssoc')
            ->will($this->returnValue($selectResult['documents']));
        $this->connectionAdapter->expects($this->at(1))
            ->method('fetchAssoc')
            ->will($this->returnValue($selectResult['aggregations']['aggregation_name']));
        $this->mapper->expects($this->once())
            ->method('buildQuery')
            ->with($this->request)
            ->will($this->returnValue($this->select));
        $this->responseFactory->expects($this->once())
            ->method('create')
            ->with($selectResult)
            ->will($this->returnArgument(0));
        $this->entityMetadata->expects($this->once())->method('getEntityId')->willReturn('product_id');
        $this->termBuilder->expects($this->once())->method('build')->willReturn($this->select);
        $this->dataProvider->expects($this->once())->method('getDataSet')->willReturn($this->select);
        $this->bucket->expects($this->once())->method('getType')->willReturn(BucketInterface::TYPE_TERM);
        $this->bucket->expects($this->once())->method('getName')->willReturn('aggregation_name');
        $this->request->expects($this->once())->method('getAggregation')->willReturn([$this->bucket]);
        $response = $this->adapter->query($this->request);
        $this->assertEquals($selectResult, $response);
    }
}
