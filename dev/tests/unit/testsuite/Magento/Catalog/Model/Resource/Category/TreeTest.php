<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Resource_Category_TreeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Catalog_Model_Resource_Category_Tree
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeConfig;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    protected function setUp()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())->method('from')->with('catalog_category_entity');
        $connection = $this->getMock('Magento_DB_Adapter_Interface');
        $connection->expects($this->once())->method('select')->will($this->returnValue($select));
        $this->_resource = $this->getMock('Magento_Core_Model_Resource', array(), array(), '', false);
        $this->_resource
            ->expects($this->once())
            ->method('getConnection')
            ->with('catalog_write')
            ->will($this->returnValue($connection))
        ;
        $this->_resource
            ->expects($this->once())
            ->method('getTableName')
            ->with('catalog_category_entity')
            ->will($this->returnArgument(0))
        ;
        $eventManager = $this->getMock('Magento_Core_Model_Event_Manager', array(), array(), '', false);
        $this->_attributeConfig = $this->getMock('Magento_Catalog_Model_Attribute_Config', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento_Catalog_Model_Resource_Category_Collection_Factory', array(), array(), '', false
        );
        $this->_model = new Magento_Catalog_Model_Resource_Category_Tree(
            $this->_resource, $eventManager, $this->_attributeConfig, $this->_collectionFactory
        );
    }

    public function testGetCollection()
    {
        $attributes = array('attribute_one', 'attribute_two');
        $this->_attributeConfig
            ->expects($this->once())
            ->method('getAttributeNames')
            ->with('catalog_category')
            ->will($this->returnValue($attributes))
        ;
        $collection = $this->getMock(
            'Magento_Catalog_Model_Resource_Category_Collection', array(), array(), '', false
        );
        $collection->expects($this->once())->method('addAttributeToSelect')->with($attributes);
        $this->_collectionFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($collection))
        ;
        $this->assertSame($collection, $this->_model->getCollection());
        // Makes sure the value is calculated only once
        $this->assertSame($collection, $this->_model->getCollection());
    }
}
