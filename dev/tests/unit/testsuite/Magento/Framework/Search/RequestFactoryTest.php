<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\TestFramework\Helper\ObjectManager;

class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\RequestFactory
     */
    private $factory;

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Search\Request\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $config;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = $this->getMockBuilder('Magento\Framework\Search\Request\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = $helper->getObject(
            'Magento\Framework\Search\RequestFactory',
            [
                'objectManager' => $this->objectManager,
                'config' => $this->config
            ]
        );
    }

    public function testCreate()
    {
        $requestName = 'request';
        $bindValues = [':str' => 'rpl'];
        $configData = [
            'queries' => ':str',
            'filters' => 'f',
            'query' => 'q',
            'index' => 'i',
            'from' => 1,
            'size' => 15
        ];
        $mappedQuery = $configData['query'] . 'Mapped';
        $this->config->expects($this->once())->method('get')->with($this->equalTo($requestName))
            ->will($this->returnValue($configData));

        /** @var \Magento\Framework\Search\Request\Mapper|\PHPUnit_Framework_MockObject_MockObject $mapper */
        $mapper = $this->getMockBuilder('Magento\Framework\Search\Request\Mapper')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Framework\Search\Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMockBuilder('Magento\Framework\Search\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->at(0))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request\Mapper'),
                $this->equalTo(
                    [
                        'objectManager' => $this->objectManager,
                        'queries' => $bindValues[':str'],
                        'filters' => $configData['filters']
                    ]
                )
            )
            ->will($this->returnValue($mapper));
        $this->objectManager->expects($this->at(1))->method('create')
            ->with(
                $this->equalTo('Magento\Framework\Search\Request'),
                $this->equalTo(
                    [
                        'name' => $configData['query'],
                        'indexName' => $configData['index'],
                        'from' => $configData['from'],
                        'size' => $configData['size'],
                        'query' => $mappedQuery,
                        'buckets' => [],
                    ]
                )
            )
            ->will($this->returnValue($request));

        $mapper->expects($this->once())->method('get')->with($this->equalTo($configData['query']))
            ->will($this->returnValue($mappedQuery));

        $this->assertEquals($request, $this->factory->create($requestName, $bindValues));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidArgumentException()
    {
        $requestName = 'rn';
        $this->config->expects($this->once())->method('get')->with($this->equalTo($requestName))
            ->will($this->returnValue(null));

        $this->factory->create($requestName);
    }
}
