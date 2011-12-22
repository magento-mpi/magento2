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
 * Tests for payment methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_LoggedIn_InvalidDataTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Creating Simple product</p>
     *
     * @test
     * @return array $productData
     */
    public function preconditionsCreateProduct()
    {
        //Data
        $productData = $this->loadData('simple_product_for_order');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Create Customer</p>
     *
     * @test
     * @return array $userData
     */
    public function preconditionsCreateCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

################################################################################
#                                                                              #
#                     Select Addresses Page                                    #
#                                                                              #
################################################################################

    /**
     * <p>Empty required fields(Select Addresses page)</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Click "Enter a New Address" on Select Addresses page.</p>
     * <p>5. Fill in fields except one required.</p>
     * <p>6. Click 'Submit' button</p>
     * <p>Expected result:</p>
     * <p>New address is not added.</p>
     * <p>Error Message is displayed.</p>
     *
     * @dataProvider dataSelectAddressesEmptyFields
     * @test
     */
    public function selectAddressesPageEmptyRequiredFields()
    {
    }

    public function dataSelectAddressesEmptyFields()
    {
        return array(
            array(),//First Name
            array(),//Last Name
            array(),//Telephone
            array(),//Street Address
            array(),//City
            array(),//State/Province
            array(),//Zip/Postal Code
            array()//Country 
        );
    }

    /**
     * <p>Fill in all required fields by using special characters</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Click "Enter a New Address" on Select Addresses page.</p>
     * <p>5. Fill in all required fields by using special characters(except the field "email")</p>
     * <p>6. Click 'Submit' button</p>
     * <p>Expected result:</p>
     * <p>New address is added.</p>
     * <p>Success Message is displayed.(The address has been saved.)</p>
     *
     * @test
     */
    public function selectAddressesPageSpecialChars() //Enter New Address page
    {
    }

    /**
     * <p>Fill in only required fields. Use max long values for fields.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Click "Enter a New Address" on Select Addresses page.</p>
     * <p>5. Fill in required fields by long value alpha-numeric data.</p>
     * <p>6. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>New address is added.</p>
     * <p>Success Message is displayed.(The address has been saved.)</p>
     *
     * @test
     */
    public function selectAddressesPageLongValues() //Enter New Address
    {
    }

################################################################################
#                                                                              #
#                     Shipping Information Page                                #
#                                                                              #
################################################################################

    /**
     * <p>Shipping Method is not selected</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Move to the Shipping Information Page</p>
     * <p>5. Leave Shipping Method unselected</p>
     * <p>6. Click 'Continue to Billing Information' button.</p>
     * <p>Expected result:</p>
     * <p>Error Message is displayed.
     * <p>(Please select shipping methods for all addresses)</p>
     *
     * @test
     */
    public function shippingInfPageShippingMethod()
    {
    }

################################################################################
#                                                                              #
#                     Billing Information Page                                 #
#                                                                              #
################################################################################

    /**
     * <p>Payment Method is not selected</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Move to the Billing Information Page</p>
     * <p>5. Leave Payment Method unselected</p>
     * <p>6. Click 'Continue to Review Your Order' button.</p>
     * <p>Expected result:</p>
     * <p>Error Message is displayed.
     * <p>(Payment method is not defined)</p>
     *
     * @test
     */
    public function billingInfPagePaymentMethod() //Not selected Payment Method
    {
    }

    /**
     * <p>Empty Card Info field </p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Move to the Billing Information Page</p>
     * <p>5. Select Credit Card (saved)</p>
     * <p>6. Fill in fields except one required.</p>
     * <p>7. Click 'Continue to Review Your Order' button.</p>
     * <p>Expected result:</p>
     * <p>Error Message is displayed.
     *
     * @test
     */
    public function billingInfPageEmptyCardInfo() //For Credit Card (saved) only
    {
    }

    public function dataEmptyCardField()
    {
        return array(
            array(),//Name on Card
            array(),//Credit Card Type
            array(),//Credit Card Type
            array(),//Credit Card Number
            array(),//Expiration Date (Month)
            array(),//Expiration Date (Year)
            array()//Card Verification Number
        );
    }
}
