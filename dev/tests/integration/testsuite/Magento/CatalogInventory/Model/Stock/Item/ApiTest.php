<?php
/**
 * Stock item API tests.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Magento/Catalog/_files/multiple_products.php
 */
class Magento_CatalogInventory_Model_Stock_Item_ApiTest extends PHPUnit_Framework_TestCase
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
        /** Assert retrieved product stock data is correct. */
        $expectedData = array(
            'product_id' => '10',
            'sku' => 'simple1',
            'qty' => 100,
            'is_in_stock' => '1'
        );
        $stockData = reset($productsStockData);
        $this->assertEquals($expectedData, $stockData, 'Product stock data is incorrect.');
    }

    /**
     * Test update method.
     */
    public function testUpdate()
    {
        $newQty = 333;

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogInventoryStockItemUpdate',
            array(10, array('qty' => $newQty))
        );

        $this->assertTrue($result, 'Failed updating stock item.');
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load(10);

        $this->assertEquals(
            $newQty,
            $product->getStockItem()->getQty(),
            'Actual quantity does not match the expected one.'
        );
    }

    /**
     * Test updating multiple stock items at once.
     */
    public function testMultiUpdate()
    {
        $productIds = array(10, 11, 12);

        $productData = array(
            array('qty' => 1010),
            array('qty' => 1111),
            array('qty' => 1212),
        );

        $result = Magento_Test_Helper_Api::call(
            $this,
            'catalogInventoryStockItemMultiUpdate',
            array($productIds, $productData)
        );

        $this->assertTrue($result, 'Failed updating stock items.');
        /** @var $product Magento_Catalog_Model_Product */
        $product = Mage::getModel('Magento_Catalog_Model_Product');

        foreach ($productIds as $index => $productId) {
            $qty = $product->load($productId)->getStockItem()->getQty();
            $this->assertEquals($productData[$index]['qty'], $qty, 'Actual quantity does not match the expected one.');
        }
    }
}
