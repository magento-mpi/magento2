<?php
/**
 * Test Product CRUD operations
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @method Mage_Catalog_Model_Product_Api_Helper_Simple _getHelper()
 */
class Mage_Catalog_Model_Product_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_special_price.php
     */
    public function testGetSpecialPrice()
    {
        /** Retrieve the product data. */
        $productId = 1;
        $actualProductData = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductGetSpecialPrice',
            array('productId' => $productId)
        );
        /** Assert returned product data. */
        $this->assertNotEmpty($actualProductData, 'Missing special price response data.');

        /** @var Mage_Catalog_Model_Product $expectedProduct */
        $expectedProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $expectedProduct->load($productId);
        $fieldsToCompare = array(
            'entity_id' => 'product_id',
            'sku',
            'attribute_set_id' => 'set',
            'type_id' => 'type',
            'category_ids' => 'categories',
            'special_price',
            'special_from_date',
            'special_to_date'
        );
        /** Assert response product equals to actual product data. */
        /** @var Magento_Test_Helper_Api $helper */
        $helper = Magento_Test_Helper_Factory::getHelper('api');
        $helper->assertEntityFields($this, $expectedProduct->getData(), $actualProductData, $fieldsToCompare);
    }

    /**
     * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
     */
    public function testItems()
    {
        /** Retrieve the list of products. */
        $actualProductsData = Magento_Test_Helper_Api::call(
            $this,
            'catalogProductList'
        );
        /** Assert returned products quantity. */
        $this->assertCount(3, $actualProductsData, 'Returned products quantity are wrong.');

        /** Loading expected product from fixture. */
        $expectedProduct = Mage::getModel('Mage_Catalog_Model_Product');
        $expectedProduct->load(10);
        $fieldsToCompare = array(
            'entity_id' => 'product_id',
            'sku',
            'attribute_set_id' => 'set',
            'type_id' => 'type',
            'category_ids',
        );
        /** @var Magento_Test_Helper_Api $helper */
        $helper = Magento_Test_Helper_Factory::getHelper('api');
        /** Assert first product from response equals to actual product data. */
        $helper->assertEntityFields(
            $this,
            $expectedProduct->getData(),
            reset($actualProductsData),
            $fieldsToCompare
        );
    }
}
