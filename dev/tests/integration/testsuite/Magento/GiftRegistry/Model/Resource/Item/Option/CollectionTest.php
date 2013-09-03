<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_GiftRegistry_Model_Resource_Item_Option_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAddProductFilter()
    {
        $collection = Mage::getModel('Magento_GiftRegistry_Model_Resource_Item_Option_Collection');
        $select = $collection->getSelect();
        $this->assertSame(array(), $select->getPart(Zend_Db_Select::WHERE));

        $product = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Catalog_Model_Product');
        $product->setId(4);
        $collection->addProductFilter(1)->addProductFilter(array(2, 3))->addProductFilter($product);
        $this->assertStringMatchesFormat(
            '%AWHERE%S(%Aproduct_id%A = %S1%S)%SAND%S(%Aproduct_id%A IN(%S2%S,%S3%S))%SAND%S(%Aproduct_id%A = %S4%S)%A',
            (string)$select
        );
    }

    public function testAddProductFilterZero()
    {
        $collection = Mage::getModel('Magento_GiftRegistry_Model_Resource_Item_Option_Collection');
        $collection->addProductFilter(0);
        $this->assertSame(array(), $collection->getSelect()->getPart(Zend_Db_Select::WHERE));
        foreach ($collection as $item) {
            $this->fail("Unexpected item in collection: {$item->getId()}");
        }
    }
}
