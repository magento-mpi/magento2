<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search;

use Magento\TestFramework\Helper\ObjectManager;

class AdapterFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Search\AdapterFactory |\PHPUnit_Framework_MockObject_MockObject
     */
    private $adapterFactory;

    /**
     * @var \Magento\Framework\ObjectManager |\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    protected function setUp()
    {
        $helper = new ObjectManager($this);

        $scopeConfig = $this->getMockBuilder('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $scopeConfig->expects($this->once())->method('getValue')
            ->with($this->equalTo('some_path'), $this->equalTo('some_scopeType'))
            ->will($this->returnValue('ClassName'));

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->adapterFactory = $helper->getObject(
            '\Magento\Framework\Search\AdapterFactory',
            [
                'objectManager' => $this->objectManager,
                'scopeConfig' => $scopeConfig,
                'path' => 'some_path',
                'scopeType' => 'some_scopeType'
            ]
        );
    }

    public function testCreate()
    {
        $adapter = $this->getMockBuilder('\Magento\Framework\Search\AdapterInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())->method('create')
            ->with($this->equalTo('ClassName'), $this->equalTo(['input']))
            ->will($this->returnValue($adapter));

        $result = $this->adapterFactory->create(['input']);
        $this->assertInstanceOf('\Magento\Framework\Search\AdapterInterface', $result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateExceptionThrown()
    {
        $adapter = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManager->expects($this->once())->method('create')
            ->with($this->equalTo('ClassName'), $this->equalTo(['input']))
            ->will($this->returnValue($adapter));

        $this->adapterFactory->create(['input']);
    }
}
