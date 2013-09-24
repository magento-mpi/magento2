<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_ConnectionAdapterFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_ConnectionAdapterFactory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_Core_Model_ConnectionAdapterFactory($this->_objectManagerMock);
    }

    /**
     * @covers Magento_Core_Model_ConnectionAdapterFactory::create
     */
    public function testCreate()
    {
        $adapterInstanceMock = $this->getMock('Magento_Core_Model_Resource_ConnectionAdapterInterface');
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('default_setup', array())
            ->will($this->returnValue($adapterInstanceMock));

        $this->assertInstanceOf('Magento_Core_Model_Resource_ConnectionAdapterInterface',
            $this->_model->create('default_setup'));
    }

    /**
     * @covers Magento_Core_Model_ConnectionAdapterFactory::create
     * @expectedException Exception
     */
    public function testCreateWrongInstance()
    {
        $adapterInstanceMock = $this->getMock('Magento_DB_Adapter_Interface');
        $this->_objectManagerMock->expects($this->once())
            ->method('create')
            ->with('default_setup')
            ->will($this->returnValue('Magento_DB_Adapter_Interface'));

        $this->_model->create('default_setup');
    }
}
