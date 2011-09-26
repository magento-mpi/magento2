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
class CheckoutOnePage_Existing_WithProductsTest extends Mage_Selenium_TestCase
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
     * <p>Creating Virtual product with required fields only</p>
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
    public function createVirtual()
    {
        //Data
        $productData = $this->loadData('virtual_product_for_order', NULL, array('general_name', 'general_sku'));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $this->productHelper()->createProduct($productData, 'virtual');
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
     * <p>Checkout with required fields filling</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to this address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Select Shipping Method option</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>8. Select Payment Method option</p>
     * <p>9. Click 'Continue' button.</p>
     * <p>Verify information into "Order Review" tab</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends createCustomer
     * @depends createSimple
     * @test
     */
    public function frontCheckoutRequiredFieldsWithSimpleProduct($customerData, $productData)
    {

        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_saved_cc_registered',
                array('general_name' => $productData, 'email_address' => $customerData['email'],
                    'password' => $customerData['password']));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    /**
     * <p>Checkout with required fields filling</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to this address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Select Shipping Method option</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>8. Select Payment Method option</p>
     * <p>9. Click 'Continue' button.</p>
     * <p>Verify information into "Order Review" tab</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends createCustomer
     * @depends createVirtual
     * @test
     */
    public function frontCheckoutRequiredFieldsWithVirtualProduct($customerData, $productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_saved_cc_req_registered_virtual_product',
                array('general_name' => $productData, 'email_address' => $customerData['email'],
                    'password' => $customerData['password']));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }
}
