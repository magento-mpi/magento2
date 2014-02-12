<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Config\Data;

class BackendModelPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\App\Config\Data\ProcessorFactory
     */
    protected $_model;

    /**
     * @var \Magento\App\Config\Data\ProcessorInterface
     */
    protected $_processorMock;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\App\Config\Data\ProcessorFactory($this->_objectManager);
        $this->_processorMock = $this->getMockForAbstractClass('Magento\App\Config\Data\ProcessorInterface');
        $this->_processorMock->expects($this->any())
            ->method('processValue')
            ->will($this->returnArgument(0));
    }

    /**
     * @covers \Magento\App\Config\Data\ProcessorFactory::get
     */
    public function testGetModelWithCorrectInterface()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\App\Config\Data\TestBackendModel')
            ->will($this->returnValue($this->_processorMock));

        $this->assertInstanceOf('Magento\App\Config\Data\ProcessorInterface',
            $this->_model->get('Magento\App\Config\Data\TestBackendModel'));
    }

    /**
     * @covers \Magento\App\Config\Data\ProcessorFactory::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetModelWithWrongInterface()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\App\Config\Data\WrongBackendModel')
            ->will($this->returnValue($this->getMock(
                'Magento\App\Config\Data\WrongBackendModel', array(), array(), '', false
            )));

        $this->_model->get('Magento\App\Config\Data\WrongBackendModel');
    }

    /**
     * @covers \Magento\App\Config\Data\ProcessorFactory::get
     */
    public function testGetMemoryCache()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\App\Config\Data\TestBackendModel')
            ->will($this->returnValue($this->_processorMock));

        $this->_model->get('Magento\App\Config\Data\TestBackendModel');
        $this->_model->get('Magento\App\Config\Data\TestBackendModel');
    }
}
