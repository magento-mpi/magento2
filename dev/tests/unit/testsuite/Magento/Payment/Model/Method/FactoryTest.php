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
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
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
        $className = 'Magento_Payment_Model_Method_Abstract';
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($methodMock));

        $this->assertInstanceOf($className, $this->_factory->create($className));
    }

    public function testCreateMethodWithArguments()
    {
        $className = 'Magento_Payment_Model_Method_Abstract';
        $data = array('param1', 'param2');
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, $data)
            ->will($this->returnValue($methodMock));

        $this->assertInstanceOf($className, $this->_factory->create($className, $data));
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage WrongClass class doesn't extend Magento_Payment_Model_Method_Abstract
     */
    public function testWrongTypeException()
    {
        $className = "WrongClass";
        $methodMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($methodMock));

        $this->_factory->create($className);
    }
}