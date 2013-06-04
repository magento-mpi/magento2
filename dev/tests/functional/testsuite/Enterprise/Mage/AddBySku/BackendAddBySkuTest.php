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
    public static $customer = array();

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        if (!empty(self::$customer)) {
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer(self::$customer);
            $this->addBySkuHelper()->openShoppingCart();
            $this->addBySkuHelper()->removeAllItemsFromShoppingCart();
            $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        }
    }

    /**
     * Create Customer for tests
     *
     * @return array
     * @test
     * @skipTearDown
     */
    public function createCustomer()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        self::$customer = array('email' => $userData['email']);

        return self::$customer;
    }

    /**
     * Create Simple Products for tests
     *
     * @return array $product
     * @test
     * @skipTearDown
     */
    public function createProduct()
    {
        $simpleProductData = $this->loadDataSet('Product', 'simple_product_visible');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simpleProductData;
    }

    /**
     * <p>Displaying Add to Shopping Cart by Sku</p>
     *
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId TL-MAGE-4051
     */
    public function displayingAddBySku($customer)
    {
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->clickButton('manage_shopping_cart', false);
        $this->waitForPageToLoad();
        $this->addParameter('store', $this->defineParameterFromUrl('store'));
        $this->addParameter('customer', $this->defineParameterFromUrl('customer'));
        $this->validatePage('customer_shopping_cart');
        $this->assertTrue($this->controlIsPresent('link', 'href_add_to_cart'),
            'Add to Shopping Cart by SKU section is not found');
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        //Verifying
        $this->addParameter('itemId', 0);
        $this->assertTrue($this->controlIsPresent('field', 'sku'), 'SKU field is not present');
        $this->assertTrue($this->controlIsPresent('field', 'sku_qty'), 'SKU Qty field is not present');
        $this->assertTrue($this->controlIsPresent('button', 'add_row'), 'Add button is not present');
        $this->assertTrue($this->controlIsPresent('field', 'sku_upload'), 'File field is not present');
        $this->assertTrue($this->controlIsPresent('button', 'reset_file'), '"Reset file" button is not present');
        $this->assertTrue($this->controlIsPresent('pageelement', 'note_csv'),
            '"Allowed file type label are not displayed under "File" field');
    }

    /**
     * <p>Adding to Cart by SKU after entering SKU and QTY</p>
     *
     * @param array $customer
     * @param array $product
     * @depends createCustomer
     * @depends createProduct
     * @test
     * @TestlinkId TL-MAGE-4052
     */
    public function addSimpleProductBySku($customer, $product)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_single',
            array('sku' => $product ['general_sku']));
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_single',
            array('sku' => $product ['general_sku'], 'product' => $product ['general_name']));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        //Verifying
        $actualData = $this->addBySkuHelper()->getProductInfoInTable();
        $this->shoppingCartHelper()->compareArrays($actualData['product_1'], $verifyData['product_1']);
    }

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     *
     * @param array $customer
     * @param array $product
     * @depends createCustomer
     * @depends createProduct
     * @test
     * @TestlinkId TL-MAGE-4136
     */
    public function addSimpleProductsWithInvalidData($customer, $product)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_multiple_invalid', null,
            array('validSkuProd1' => $product ['general_sku']));
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_multiple_invalid', null, array(
            'validSku' => $product ['general_sku'], 'validProduct' => $product ['general_name'],
            'invalidSku' => $productToAdd ['product_2'] ['sku']
        ));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        //Verifying//
        $actualData = $this->addBySkuHelper()->getProductInfoInTable();
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()
            ->compareArrays($actualData['product_1'], $verifyData['valid_table']['product_1']);
        $this->shoppingCartHelper()
            ->compareArrays($errorProductData['product_1'], $verifyData['invalid_table']['product_1']);
    }

    /**
     * <p>Adding/Removing all attention products</p>
     *
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId TL-MAGE-4248
     */
    public function removeAllProductFromErrorTable($customer)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', FALSE);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        $this->addBySkuHelper()->removeAllItemsFromAttentionTable();
        //Verifying
        $this->assertTrue($this->addBySkuHelper()->isAttentionTableEmpty());
    }

    /**
     * <p>Adding/Removing each attention product separately</p>
     *
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId TL-MAGE-4250
     */
    public function removeSelectedProductFromErrorTable($customer)
    {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        $verifyData = $this->loadDataSet('AddBySku', 'data_to_check_test_remove_invalid', null,
            array(
                'product_1' => $productToAdd ['product_1']['sku'],
                'product_2' => $productToAdd ['product_2']['sku'],
                'product_3' => $productToAdd ['product_3']['sku']
            )
        );
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductsBySkuToShoppingCart($productToAdd);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_3'], $verifyData['product_3']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable(array('sku' => $verifyData['product_3']['sku']));
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable(array('sku' => $verifyData['product_2']['sku']));
        $errorProductData = $this->addBySkuHelper()->getProductInfoInTable('error_table_head', 'error_table_line');
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyData['product_1']);
        $this->addBySkuHelper()->removeItemsFromAttentionTable(array('sku' => $verifyData['product_1']['sku']));
        $this->assertFalse($this->controlIsVisible('fieldset', 'shopping_cart_error_table'),
            'Products are not deleted from attention grid');
    }
}

