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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page Checkout test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_BufferTest extends Mage_Selenium_TestCase
{
    /**
     *
     * <p>Creating products for testing.</p>
     *
     * <p>Navigate to Sales-Orders page.</p>
     *
     */
    protected function assertPreConditions()
    {
        $this->addParameter('tabName', '');
        $this->addParameter('webSite', '');
        $this->addParameter('storeName', '');
    }

    /**
     * <p>Creating Simple product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function createSimple()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData['general_name'];
    }


    /**
     * Create customer
     *
     * @test
     */
    public function createCustomer()
    {
        //Preconditions
        $userData = $this->loadData('generic_customer_account', null, 'email');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $this->CustomerHelper()->createCustomer($userData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        return $userData;
    }
    /**
     * NOTE: You Need to comment in Helper's pressing 'place order' button to run this test
     *
     * @depends createCustomer
     * @depends createSimple
     * @test
     */
    public function someTest($customerData, $productData)
    {
        $performLogin = $this->loadData('perform_login',
                array('email' => $customerData['email'], 'password' => $customerData['password']));
        $checkoutData = $this->loadData('checkout_data_saved_cc_loggedin',
                array('general_name' => $productData));
        $productData = $this->loadData('checkout_order_review_products_prices',
                array('product_name' => $productData));
        $orderPriceData = $this->loadData('checkout_order_review_total_prices');
        $this->logoutCustomer();
        $this->clickControl('link', 'log_in');
        $this->fillForm($performLogin);
        $this->clickButton('login');
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        $this->checkoutOnePageHelper()->frontVerifyCheckoutData($productData, $orderPriceData);
    }
}
