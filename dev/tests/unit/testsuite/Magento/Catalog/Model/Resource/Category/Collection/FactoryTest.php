<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Resource_Category_Collection_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Category_Collection_Factory
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento_ObjectManager');
        $this->_model = new Magento_Catalog_Model_Resource_Category_Collection_Factory($this->_objectManager);
    }

    public function testCreate()
    {
        $objectOne = $this->getMock('Magento_Catalog_Model_Resource_Category_Collection', array(), array(), '', false);
        $objectTwo = $this->getMock('Magento_Catalog_Model_Resource_Category_Collection', array(), array(), '', false);
        $this->_objectManager
            ->expects($this->exactly(2))
            ->method('create')
            ->with('Magento_Catalog_Model_Resource_Category_Collection', array())
            ->will($this->onConsecutiveCalls($objectOne, $objectTwo))
        ;
        $this->assertSame($objectOne, $this->_model->create());
        $this->assertSame($objectTwo, $this->_model->create());
    }
}
