<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Search\Adapter\Mysql;


use Magento\TestFramework\Helper\ObjectManager;
use Magento\Framework\App\Resource\Config;

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

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);

        $this->request = $this->getMockBuilder('Magento\Framework\Search\RequestInterface')
            ->setMethods([])
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
        $this->resource->expects($this->once())
            ->method('getConnection')
            ->with(Config::DEFAULT_SETUP_CONNECTION)
            ->will($this->returnValue($this->connectionAdapter));

        $this->mapper = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\Mapper')
            ->setMethods(['buildQuery'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->responseFactory = $this->getMockBuilder('\Magento\Framework\Search\Adapter\Mysql\ResponseFactory')
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapter = $this->objectManager->getObject(
            '\Magento\Framework\Search\Adapter\Mysql\Adapter',
            [
                'mapper' => $this->mapper,
                'responseFactory' => $this->responseFactory,
                'resource' => $this->resource,
            ]
        );
    }

    public function testQuery()
    {
        $selectResult = [
            [
                'id' => 1,
                'sku' => 'Product'
            ]
        ];

        $this->connectionAdapter->expects($this->once())
            ->method('fetchAssoc')
            ->will($this->returnValue($selectResult));
        $this->mapper->expects($this->once())
            ->method('buildQuery')
            ->with($this->request)
            ->will($this->returnValue($this->select));
        $this->responseFactory->expects($this->once())
            ->method('create')
            ->with($selectResult)
            ->will($this->returnArgument(0));
        $response = $this->adapter->query($this->request);
        $this->assertEquals($selectResult, $response);
    }
}
