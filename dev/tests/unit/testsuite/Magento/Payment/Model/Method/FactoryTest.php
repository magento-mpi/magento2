<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Method_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_Payment_Model_Method_Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array('create', 'get', 'configure'),
            array(), '', false);
        $this->_factory = $objectManagerHelper->getObject('Magento_Payment_Model_Method_Factory', array(
            'objectManager' => $this->_objectManagerMock,
        ));
    }

    public function testCreateMethod()
    {
        $methodMock = $this->getMock('Magento_Payment_Model_Method_Purchaseorder', array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($methodMock));

        $this->assertInstanceOf('Magento_Payment_Model_Method_Abstract',
            $this->_factory->create('Magento_Payment_Model_Method_Purchaseorder'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWrongTypeException()
    {
        $methodMock = $this->getMock('Magento_Payment_Model_Config_Source_Allmethods', array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($methodMock));

        $this->_factory->create('Magento_Payment_Model_Config_Source_Allmethods');
    }
}