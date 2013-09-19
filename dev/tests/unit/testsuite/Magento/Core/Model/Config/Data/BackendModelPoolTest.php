<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Data;

class BackendModelPoolTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Data\BackendModelPoolTest
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\Core\Model\Config\Data\BackendModelPool($this->_objectManager);
    }

    /**
     * @covers \Magento\Core\Model\Config\Data\BackendModelPool::get
     */
    public function testGetModelWithCorrectInterface()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Config\Data\TestBackendModel')
            ->will($this->returnValue(new \Magento\Core\Model\Config\Data\TestBackendModel()));

        $this->assertInstanceOf('Magento\Core\Model\Config\Data\TestBackendModel',
            $this->_model->get('Magento\Core\Model\Config\Data\TestBackendModel'));
    }

    /**
     * @covers \Magento\Core\Model\Config\Data\BackendModelPool::get
     * @expectedException \InvalidArgumentException
     */
    public function testGetModelWithWrongInterface()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Config\Data\WrongBackendModel')
            ->will($this->returnValue(new \Magento\Core\Model\Config\Data\WrongBackendModel()));

        $this->_model->get('Magento\Core\Model\Config\Data\WrongBackendModel');
    }

    /**
     * @covers \Magento\Core\Model\Config\Data\BackendModelPool::get
     */
    public function testGetMemoryCache()
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->with('Magento\Core\Model\Config\Data\TestBackendModel')
            ->will($this->returnValue(new \Magento\Core\Model\Config\Data\TestBackendModel()));

        $this->_model->get('Magento\Core\Model\Config\Data\TestBackendModel');
        $this->_model->get('Magento\Core\Model\Config\Data\TestBackendModel');
    }
}
