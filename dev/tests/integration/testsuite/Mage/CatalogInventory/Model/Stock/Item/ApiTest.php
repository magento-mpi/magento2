<?php
/**
 * Stock item API test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
 */
class Mage_CatalogInventory_Model_Stock_Item_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test list method.
     */
    public function testList()
    {
        $productsId = array(10, 11, 12);
        /** Retrieve products stock data. */
        $productsStockData = Magento_Test_Helper_Api::call(
            $this,
            'catalogInventoryStockItemList',
            array($productsId)
        );
        /** Assert product stock data retrieving was successful. */
        $this->assertNotEmpty($productsStockData, 'Product stock data retrieving was unsuccessful.');
        /** Assert base fields are present in the response. */
        $stockData = reset($productsStockData);
        $expectedFields = array('product_id', 'sku', 'qty', 'is_in_stock');
        $missingFields = array_diff($expectedFields, array_keys($stockData));
        $this->assertEmpty(
            $missingFields,
            sprintf("The following fields must be present in response: %s.", implode(', ', $missingFields))
        );
    }
}
