<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollectionTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_grouped.php
     * @magentoAppIsolation enabled
     */
    public function testGetColumnValues()
    {
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');
        $product->load(9);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_product', $product);

        /** @var Magento_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollection $collection */
        $collection = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create(
            'Magento_Catalog_Model_Resource_Product_Type_Grouped_AssociatedProductsCollection'
        );

        $this->assertEquals(array('simple-1', 'virtual-product'), $collection->getColumnValues('sku'));
    }
}
