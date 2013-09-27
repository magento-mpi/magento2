<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Model\Resource\Item;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\GiftRegistry\Model\Resource\Item\Collection
     */
    protected $_collection = null;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\GiftRegistry\Model\Resource\Item\Collection');
    }

    public function testAddProductFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addProductFilter(0));
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->_collection->addProductFilter(99);
        $where = $select->getPart(\Zend_Db_Select::WHERE);
        $this->assertArrayHasKey(0, $where);
        $this->assertContains('product_id', $where[0]);
        $this->assertContains(99, $where[0]);
    }

    public function testAddItemFilter()
    {
        $select = $this->_collection->getSelect();
        $this->assertSame(array(), $select->getPart(\Zend_Db_Select::WHERE));
        $this->assertSame($this->_collection, $this->_collection->addItemFilter(99));
        $this->_collection->addItemFilter(array(100, 101));
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Sitem_id%S = %S99%S)%SAND%S(%Sitem_id%S IN(%S100%S,%S101%S))%A', (string)$select
        );
    }
}
