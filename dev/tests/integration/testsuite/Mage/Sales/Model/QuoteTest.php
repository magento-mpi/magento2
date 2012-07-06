<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Sales
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Model_QuoteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Catalog/_files/product_virtual.php
     * @magentoDataFixture Mage/Sales/_files/quote.php
     */
    public function testCollectTotals()
    {
        $quote = new Mage_Sales_Model_Quote();
        $quote->load('test01', 'reserved_order_id');

        $product = new Mage_Catalog_Model_Product();
        $product->load(21);
        $quote->addProduct($product);
        $quote->collectTotals();

        $this->assertEquals(2, $quote->getItemsQty());
        $this->assertEquals(1, $quote->getVirtualItemsQty());
        $this->assertEquals(20, $quote->getGrandTotal());
        $this->assertEquals(20, $quote->getBaseGrandTotal());
    }

    /**
     */
    public function test_collectItemsQtys()
    {
//        $product = new Mage_Catalog_Model_Product();
//        $product->load(21);
//        $quote->addProduct($product);
    }
}
