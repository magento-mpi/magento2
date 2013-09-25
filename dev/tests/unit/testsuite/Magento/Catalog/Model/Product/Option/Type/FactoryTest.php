<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Product_Option_Type_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var Magento_Catalog_Model_Product_Option_Type_Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_factory = $objectManagerHelper->getObject('Magento_Catalog_Model_Product_Option_Type_Factory', array(
            'objectManager' => $this->_objectManagerMock,
        ));
    }

    public function testCreate()
    {
        $className = 'Magento_Catalog_Model_Product_Option_Type_Default';

        $filterMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, array())
            ->will($this->returnValue($filterMock));

        $this->assertEquals($filterMock, $this->_factory->create($className));
    }

    public function testCreateWithArguments()
    {
        $className = 'Magento_Catalog_Model_Product_Option_Type_Default';
        $arguments = array('foo', 'bar');

        $filterMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->with($className, $arguments)
            ->will($this->returnValue($filterMock));

        $this->assertEquals($filterMock, $this->_factory->create($className, $arguments));
    }

    /**
     * @expectedException Magento_Core_Exception
     * @expectedExceptionMessage WrongClass doesn't extends Magento_Catalog_Model_Product_Option_Type_Default
     */
    public function testWrongTypeException()
    {
        $className = 'WrongClass';

        $filterMock = $this->getMock($className, array(), array(), '', false);
        $this->_objectManagerMock->expects($this->once())->method('create')->will($this->returnValue($filterMock));

        $this->_factory->create($className);
    }
}
