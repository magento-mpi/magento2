<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CatalogSearch_Model_Engine_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_CatalogSearch_Model_Engine_Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_factory = $objectManagerHelper->getObject('Magento_CatalogSearch_Model_Engine_Factory', array(
            'objectManager' => $this->_objectManagerMock,
        ));
    }

    public function testCreate()
    {
        $className = 'Magento_Search_Engine';

        $engineMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_factory->create($className));
    }

    public function testCreateWithArguments()
    {
        $className = 'Magento_Search_Engine';
        $arguments = array('foo', 'bar');

        $engineMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, $arguments)
            ->will($this->returnValue($engineMock));

        $this->assertEquals($engineMock, $this->_factory->create($className, $arguments));
    }
}
