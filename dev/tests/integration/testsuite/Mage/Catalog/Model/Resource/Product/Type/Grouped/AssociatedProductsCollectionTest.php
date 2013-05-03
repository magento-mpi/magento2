<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollectionTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_grouped.php
     * @magentoAppIsolation enabled
     */
    public function testGetColumnValues()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $product->load(9);
        Mage::register('current_product', $product);

        /** @var Mage_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollection $collection */
        $collection = Mage::getResourceModel(
            'Mage_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollection'
        );

        $this->assertEquals(array('simple-1', 'virtual-product'), $collection->getColumnValues('sku'));
    }
}
