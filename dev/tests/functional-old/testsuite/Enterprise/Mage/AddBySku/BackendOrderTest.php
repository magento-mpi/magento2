<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AddBySku
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for backend order using Add By SKU functionality
 */
class Enterprise_Mage_AddBySku_BackendOrderTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    public function tearDownAfterTest()
    {
        $this->navigate('manage_sales_orders');
    }

    /**
     * <p>Displaying Add to Order by SKU</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4183
     */
    public function navigationTest()
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->assertTrue($this->buttonIsPresent('add_products_by_sku'),
            'There is no "Add Products By SKU" button on the page');
        $this->clickButton('add_products_by_sku', false);
        $this->waitForControlVisible('fieldset', 'add_to_cart_by_sku');
        $this->addParameter('number', 1);
        $this->assertTrue($this->controlIsPresent('field', 'sku'), 'SKU field is not present');
        $this->assertTrue($this->controlIsPresent('field', 'qty'), 'SKU Qty field is not present');
        $this->assertTrue($this->buttonIsPresent('add_row'), 'There is no button for adding rows for SKU on the page');
        $this->assertTrue($this->controlIsPresent('field', 'sku_upload'),
            'There is no File field for adding products per *.CSV');
        $this->assertTrue($this->controlIsPresent('pageelement', 'note_csv'), 'There is no note about *.csv file');
        $this->assertTrue($this->buttonIsPresent('reset_file'), 'There is no "Reset" button on the page');
        $this->assertTrue($this->buttonIsPresent('submit_sku_form'), 'There is no "Add To Order" button on the page');
        $this->clickButton('add_row', false);
        $this->addParameter('number', 2);
        $this->assertTrue($this->buttonIsPresent('remove_row'),
            'There is no button for deleting rows for SKU on the page');
        $this->clickButton('remove_row', false);
        $this->assertFalse($this->controlIsVisible('field', 'sku'),
            "SKU field in the 2nd row is present but shouldn't");
        $this->assertFalse($this->controlIsVisible('field', 'qty'),
            "Qty field in the 2nd row is present but shouldn't");
    }

    /**
     * <p>Adding/Removing all attention products</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4237, TL-MAGE-4078
     */
    public function multipleInvalidSkuDeleteAll()
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        $nonExist = array(
            array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 1),
            array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 1)
        );
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($nonExist, true, false);
        $this->addParameter('number', count($nonExist));
        $this->assertMessagePresent('error', 'required_attention_product');
        $errorData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->assertEquals($nonExist[0]['sku'], $errorData['product_1']['product_sku']);
        $this->assertEquals($nonExist[1]['sku'], $errorData['product_2']['product_sku']);
        $this->clickButton('remove_all', false);
        $this->pleaseWait();
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Required attention grid is present on the page');
    }

    /**
     *<p>Adding/Removing each attention product separately</p>
     *
     * @test
     * @TestlinkId TL-MAGE-4239
     */
    public function multipleInvalidSkuDeleteSeparately()
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical');
        $nonExist = array(
            array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 7),
            array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 3),
            array('sku' => $this->generate('string', 10, ':alnum:'), 'qty' => 255)
        );
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        //Verifying
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($nonExist, true, false);
        $this->addParameter('number', count($nonExist));
        $this->assertMessagePresent('error', 'required_attention_product');
        $this->addBySkuHelper()->removeItemsFromAttentionTable(
            array($nonExist[0]['sku'], $nonExist[1]['sku'], $nonExist[2]['sku'])
        );
        $this->assertFalse($this->controlIsPresent('fieldset', 'products_requiring_attention'),
            'Required attention grid is present on the page');
    }
}