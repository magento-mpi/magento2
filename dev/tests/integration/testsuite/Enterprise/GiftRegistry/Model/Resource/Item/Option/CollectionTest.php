<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_GiftRegistry_Model_Resource_Item_Option_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testAddProductFilter()
    {
        $collection = new Enterprise_GiftRegistry_Model_Resource_Item_Option_Collection;
        $select = $collection->getSelect();
        $this->assertSame(array(), $select->getPart(Zend_Db_Select::WHERE));

        $product = new Mage_Catalog_Model_Product;
        $product->setId(4);
        $collection->addProductFilter(1)->addProductFilter(array(2, 3))->addProductFilter($product);
        $this->assertStringMatchesFormat(
            '%AWHERE%S(product_id = %S1%S)%SAND%S(product_id IN(%S2%S,%S3%S))%SAND%S(product_id = %S4%S)%A',
            (string)$select
        );

        $collection = new Enterprise_GiftRegistry_Model_Resource_Item_Option_Collection;
        $collection->addProductFilter(0);
        $this->assertSame(array(), $collection->getSelect()->getPart(Zend_Db_Select::WHERE));
        foreach ($collection as $item) {
            $this->fail("Unexpected item in collection: {$item->getId()}");
        }
    }
}
