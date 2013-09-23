<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Persistent_Model_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_Persistent_Model_Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);

        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager');
        $this->_factory = $helper->getObject('Magento_Persistent_Model_Factory', array(
            'objectManager' => $this->_objectManagerMock,
        ));
    }

    public function testCreate()
    {
        $className = 'SomeModel';

        $classMock = $this->getMock('SomeModel');
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($classMock));

        $this->assertEquals($classMock, $this->_factory->create($className));
    }

    public function testCreateWithArguments()
    {
        $className = 'SomeModel';
        $data = array('param1', 'param2');

        $classMock = $this->getMock('SomeModel');
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, $data)
            ->will($this->returnValue($classMock));

        $this->assertEquals($classMock, $this->_factory->create($className, $data));
    }
}
