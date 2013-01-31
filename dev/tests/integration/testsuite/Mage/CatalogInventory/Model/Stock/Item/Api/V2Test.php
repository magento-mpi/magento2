<?php
/**
 * Stock item API Version 2 test.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @magentoDataFixture Mage/Catalog/_files/multiple_products.php
 */
class Mage_CatalogInventory_Model_Stock_Item_Api_V2Test extends PHPUnit_Framework_TestCase
{
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
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load(10);
        $this->assertEquals($newQty, $product->getStockItem()->getQty(), 'Actual quantity does not match the expected one.');
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
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');

        foreach ($productIds as $index => $productId) {
            $qty = $product->load($productId)->getStockItem()->getQty();
            $this->assertEquals($productData[$index]['qty'], $qty, 'Actual quantity does not match the expected one.');
        }
    }
}
