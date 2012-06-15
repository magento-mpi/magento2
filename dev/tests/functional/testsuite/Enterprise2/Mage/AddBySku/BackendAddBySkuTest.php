<?php

/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Prices Validation on the Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_AddBySku_BackendAddBySkuTest extends Mage_Selenium_TestCase {

    public static $customer = array();


    protected function assertPreConditions() {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest() {
        if (!empty(self::$customer)){
            $this->loginAdminUser();
            $this->navigate('manage_customers');
            $this->customerHelper()->openCustomer(self::$customer);
            $this->addBySkuHelper()->openShoppingCart();
            $this->addBySkuHelper()->clearShoppingCart();
        }
    }

    /**
     * Create Customer for tests
     * @return array
     * @test
     * @group preConditions
     * @skipTearDown
     */
    public function createCustomer() {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');
        $param = $userData['first_name'] . ' ' . $userData['last_name'];
        $this->addParameter('customer_first_last_name', $param);
        self::$customer = array('email' => $userData['email']);
        return self::$customer;
        //return array('email' => $userData['email']);
    }

    /**
     * Create Simple Products for tests
     * @return array $product
     * @test
     * @group preConditions
     * @skipTearDown
     */
    public function createProduct() {
        $simpleProductData = $this->loadDataSet('Product', 'simple_product_visible');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProductData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simpleProductData;
    }

    /**
     * <p>Displaying Add to Shopping Cart by Sku</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Customers>Manage Customers</p>
     * <p>3.Select customer and click "Edit" button</p>
     * <p>4.Click Manage Shopping Cart button</p>
     * <p>Shopping Cart for selected customer should be opened</p>
     * <p>"Add to Shopping Cart by SKU" section is displayed</p>
     * <p>5.Open "Add to Shopping Cart by SKU" section</p>
     * <p>Expected result:</p>
     * <p>The "Add to Shopping Cart by SKU" field contains the following UI elements:
     * <p>SKU and Qty text fields;</p>
     * <p>a button for adding more fields for SKUs and QTYs</p>
     * <p> "File" label</p>
     * <p>Browse button for loading a spreadsheet</p>
     * <p>Reset button for removing selected file.</p>
     * <p>Two notes: "Allowed file type: csv. and File must contain two columns, with "sku" and "qty" in the header row" are displayed under "File" field.</p>
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId	TL-MAGE-4051
     *//*
      public function displayingAddBySku($customer) {
      //Steps
      $this->navigate('manage_customers');
      $this->customerHelper()->openCustomer($customer);
      $this->clickButton('manage_shopping_cart', false);
      $this->waitForPageToLoad($this->_browserTimeoutPeriod);
      $this->addParameter('store', $this->defineParameterFromUrl('store'));
      $this->addParameter('customer', $this->defineParameterFromUrl('customer'));
      $this->validatePage('customer_shopping_cart');
      $this->assertTrue($this->controlIsPresent('link', 'href_add_to_cart'), 'Add to Shopping Cart by SKU section is not found');
      $this->clickControl('link', 'href_add_to_cart', false);
      $this->waitForAjax();
      //Verifying
      $this->addParameter('itemId', 0);
      $this->assertTrue($this->controlIsPresent('field', 'sku'), 'SKU field is not present');
      $this->assertTrue($this->controlIsPresent('field', 'sku_qty'), 'SKU Qty field is not present');
      $this->assertTrue($this->controlIsPresent('button', 'add_sku'), 'Add button is not present');
      $this->assertTrue($this->controlIsPresent('pageelement', 'file'), '"File" label is not present');
      $this->assertTrue($this->controlIsPresent('field', 'sku_file'), 'File field is not present');
      $this->assertTrue($this->controlIsPresent('button', 'reset_file'), '"Reset file" button is not present');
      $this->assertTrue($this->controlIsPresent('pageelement', 'allowed_file'), '"Allowed file type label are not displayed under "File" field');
      } */

    /**
     * <p>Adding to Cart by SKU after entering SKU and QTY</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Customers>Manage Customers</p>
     * <p>3.Select customer and click "Edit" button</p>
     * <p>4.Click Manage Shopping Cart button</p>
     * <p>5.Open Add to Shopping Cart by SKU section</p>
     * <p>6.Enter to SKU fiels sku simple product</p>
     * <p>7.Enter  valid value to QTY field</p>
     * <p>8.Click "Add Selected Product(s) to Shopping Cart</p>
     * <p>Expected result:</p>
     * <p>Simple product is added to the Shopping Cart.</p>
     *
     * @param array $customer
     * @param array $product
     * @depends createCustomer
     * @depends createProduct
     * @test
     * @TestlinkId	TL-MAGE-4052
     */  /*
    public function addSimpleProductBySku($customer, $product) {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_single', array('sku' => $product ['general_sku']));
        //$productToAdd ['product_1']['sku'] = $product ['general_sku'];
        $verifyProductData = $this->loadDataSet('AddBySku', 'data_to_check_test_single', array ('sku' => $product ['general_sku'], 'product' => $product ['general_name']));
        //$verifyProductData ['product_1']['sku'] = $product ['general_sku'];
        //$verifyProductData ['product_1']['product'] = $product ['general_name'];
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductShoppingCart($productToAdd);
        //Verifying
        $actualProductData = $this->addBySkuHelper()->getProductInfoInTable();
        $this->shoppingCartHelper()->compareArrays($actualProductData['product_1'], $verifyProductData['product_1']);
        //$this->addBySkuHelper()->clearShoppingCart();
    }     */

    /**
     * <p>Adding to Cart by SKU after entering values in multiple fields</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Customers>Manage Customers</p>
     * <p>3.Select customer and click "Edit" button</p>
     * <p>4.Click Manage Shopping Cart button</p>
     * <p>5.Open Add to Shopping Cart by SKU section</p>
     * <p>6.Click "Add Row" button several times</p>
     * <p>7.Enter some valid and invalid SKUs and QTYs values</p>
     * <p>8.Click "Add Selected Product(s) to Shopping Cart</p>
     * <p>Expected result:</p>
     * <p>Product with valid SKU is added to shopping cart</p>
     * <p>Product with invalid SKU is added to "requiring attention" grid.</p>
     *
     * @param array $customer
     * @param array $product
     * @depends createCustomer
     * @depends createProduct
     * @test
     * @TestlinkId	TL-MAGE-4136
     *//*
    public function addSimpleProductsWithInvalidData($customer, $product) {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_multiple_invalid',null, array('validSkuProd1' => $product ['general_sku']));
        $verifyProductData = $this->loadDataSet('AddBySku', 'data_to_check_test_multiple_invalid', null, array('validSku' => $product ['general_sku'],
                                                                                                         'validProduct' => $product ['general_name'],
                                                                                                         'invalidSku' => $productToAdd ['product_2'] ['sku']));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductShoppingCart($productToAdd);
        //Verifying//
        $actualProductData = $this->addBySkuHelper()->getProductInfoInTable();
        $errorProductData = $this->addBySkuHelper()->getProductInfoInErrorTable();
        $this->shoppingCartHelper()->compareArrays($actualProductData['product_1'], $verifyProductData['valid_table']['product_1']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyProductData['invalid_table']['product_1']);
    }*/

    /**
     * <p>Adding/Removing all attention products</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Customers>Manage Customers</p>
     * <p>3.Select customer and click "Edit" button</p>
     * <p>4.Click Manage Shopping Cart button</p>
     * <p>5.Open Add to Shopping Cart by SKU section</p>
     * <p>6.Click "Add Row" button several times</p>
     * <p>7.In fields "SKU" enter non-existing SKU of products</p>
     * <p>8.Click "Add Selected Product(s) to Shopping Cart</p>
     * <p>All product should be added to "require attention" grid</p>
     * <p>9.Click "Remove All" button</p>
     * <p>Expected result:</p>
     * <p>All products are deleted</p>
     * <p>Failed grid is disappeared</p>
     *
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId	TL-MAGE-4248
     *//*
    public function removeAllProductFromErrorTable($customer) {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', FALSE);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductShoppingCart($productToAdd);
        $this->addBySkuHelper()->removeAllItemsFromErrorTable();
        //Verifying
        $this->addBySkuHelper()->verifyErrorTableIsEmpty();
    }*/

    /**
     * <p>Adding/Removing each attention product separately</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Customers>Manage Customers</p>
     * <p>3.Select customer and click "Edit" button</p>
     * <p>4.Click Manage Shopping Cart button</p>
     * <p>5.Open Add to Shopping Cart by SKU section</p>
     * <p>6.Click "Add Row" button three times</p>
     * <p>7.In fields "SKU" enter non-existing SKU of products</p>
     * <p>8.Click "Add Selected Product(s) to Shopping Cart</p>
     * <p>Three products should be added to "require attention" grid</p>
     * <p>9.Click "Remove" button on produc</p>
     * <p>One product should be deleted. Two products should be displayed in grid</p>
     * <p>10.Take away all the products one by one</p>
     * <p>Expected result:</p>
     * <p>All products should be deleted one by one. Grid without product should be disappear.</p>
     *
     * @param array $customer
     * @depends createCustomer
     * @test
     * @TestlinkId	TL-MAGE-4250
     */
    public function removeSelectedProductFromErrorTable($customer) {
        //Data
        $productToAdd = $this->loadDataSet('AddBySku', 'data_to_add_shop_cart_test_remove_invalid');
        $verifyProductData = $this->loadDataSet('AddBySku', 'data_to_check_test_remove_invalid', null, array ('product_1' => $productToAdd ['product_1']['sku'],
                                                                                                              'product_2' => $productToAdd ['product_2']['sku'],
                                                                                                              'product_3' => $productToAdd ['product_3']['sku']));
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->openCustomer($customer);
        $this->addBySkuHelper()->openShoppingCart();
        $this->clickControl('link', 'href_add_to_cart', false);
        $this->waitForAjax();
        $this->addBySkuHelper()->addProductShoppingCart($productToAdd);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInErrorTable();
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_3'], $verifyProductData['product_3']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyProductData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyProductData['product_1']);
        $this->addBySkuHelper()->removeSingleItemsFromErrorTable(3);
        $errorProductData = $this->addBySkuHelper()->getProductInfoInErrorTable();
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_2'], $verifyProductData['product_2']);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyProductData['product_1']);
        $this->addBySkuHelper()->removeSingleItemsFromErrorTable(2);
        $this->shoppingCartHelper()->compareArrays($errorProductData['product_1'], $verifyProductData['product_1']);
        $this->addBySkuHelper()->removeSingleItemsFromErrorTable(1);
        $this->assertFalse($this->controlIsVisible('fieldset', 'shopping_cart_error'), 'Products are not deleted from attention grid');

    }
}

