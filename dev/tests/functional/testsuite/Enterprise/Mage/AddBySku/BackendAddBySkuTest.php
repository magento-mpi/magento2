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

class Enterprise_Mage_AddBySku_BackendAddBySkuTest extends Mage_Selenium_TestCase
{
    private static $_customer = array();

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        if (!empty(self::$_customer)) {
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer(self::$_customer);
            $this->addBySkuHelper()->openShoppingCart();
        }
    }

    protected function tearDownAfterTest()
    {
        if (!empty(self::$_customer)) {
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer(self::$_customer);
            $this->addBySkuHelper()->openShoppingCart();
            $this->addBySkuHelper()->removeAllItemsFromShoppingCart();
            $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        }
    }

    /**
     * <p>Creating Simple product and customer</p>
     *
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        self::$_customer = array('email' => $userData['email']);

        return array('sku' => $simple['general_sku'], 'name' => $simple['general_name']);
    }

    /**
     * <p>Displaying Add to Shopping Cart by Sku</p>
     *
     * @test
     * @depends preconditionsForTests
     * @skipTearDown
     * @TestlinkId TL-MAGE-4051
     */
    public function displayingAddBySku()
    {
        //Verifying
        $this->addParameter('number', 1);
        $this->assertTrue($this->controlIsPresent('field', 'sku'), 'SKU field is not present');
        $this->assertTrue($this->controlIsPresent('field', 'qty'), 'SKU Qty field is not present');
        $this->assertTrue($this->controlIsPresent('button', 'add_row'), 'Add button is not present');
        $this->assertTrue($this->controlIsPresent('field', 'sku_upload'), 'File field is not present');
        $this->assertTrue($this->controlIsPresent('button', 'reset_file'), '"Reset file" button is not present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'note_csv'),
            '"Allowed file type label are not displayed under "File" field');
    }

    /**
     * <p>Adding to Cart by SKU after entering SKU and QTY</p>
     *
     * @param array $product
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4052
     */
    public function addSimpleProductBySku($product)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_single',
            array('sku' => $product['sku']));
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_single',
            array('product_sku' => $product['sku'], 'product_name' => $product['name']));
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        //Verifying
        $actualData = $this->addBySkuHelper()->getProductInfoInTable();
        $this->shoppingCartHelper()->compareArrays($actualData['product_1'], $verifyData['product_1']);
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $product
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4136
     */
    public function addSimpleProductsWithInvalidData($product)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_multiple_invalid', null,
            array('validSkuProd1' => $product['sku']));
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_multiple_invalid', null, array(
            'validSku' => $product['sku'], 'validProduct' => $product['name'],
            'invalidSku' => $productToAdd ['product_2']['sku']
        ));
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        //Verifying
        $actualData = $this->addBySkuHelper()->getProductInfoInTable();
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()
            ->compareArrays($actualData['product_1'], $verifyData['valid']['product_1']);
        $this->shoppingCartHelper()
            ->compareArrays($errorProductData['product_1'], $verifyData['invalid']['product_1']);
    }

    /**
     * <p>Adding/Removing all attention products</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4248
     */
    public function removeAllProductFromErrorTable()
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        //Verifying
        $this->assertTrue($this->addBySkuHelper()->isAttentionTableEmpty());
    }

    /**
     * <p>Adding/Removing each attention product separately</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-4250
     */
    public function removeSelectedProductFromErrorTable()
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_remove_invalid', null, array(
            'product_1' => $productToAdd ['product_1']['sku'],
            'product_2' => $productToAdd ['product_2']['sku'],
            'product_3' => $productToAdd ['product_3']['sku']
        ));
        //Steps
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_3'], $verifyData['product_3']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable($verifyData['product_3']['product_sku']);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable($verifyData['product_2']['product_sku']);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable($verifyData['product_1']['product_sku']);
        $this->assertFalse($this->controlIsVisible('fieldset', 'products_requiring_attention'),
            'Products are not deleted from attention grid');
    }
}

