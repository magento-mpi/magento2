<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftRegistry_Model_Resource_Item_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    protected $_collection = null;

    protected function setUp()
    {
        $this->_collection = new Enterprise_GiftRegistry_Model_Resource_Item_Collection;
    }

    protected function tearDown()
    {
        unset($this->_collection);
    }

    public function testAddProductFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addProductFilter(0));
        $this->assertSame(array(), $select->getPart(Zend_Db_Select::WHERE));
        $this->_collection->addProductFilter(99);
        $where = $select->getPart(Zend_Db_Select::WHERE);
        $this->assertArrayHasKey(0, $where);
        $this->assertContains('product_id', $where[0]);
        $this->assertContains(99, $where[0]);
    }

    public function testAddItemFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addItemFilter(99));
        $this->_collection->addItemFilter(array(100, 101));
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Sitem_id%S = %S99%S)%SAND%S(%Sitem_id%S IN(%S100%S,%S101%S))%A', (string)$select
        );
    }
}