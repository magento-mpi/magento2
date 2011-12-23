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
 * Tests for Checkout with Multiple Addresses. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_WithRegistration_InputDataValidation extends Mage_Selenium_TestCase
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

################################################################################
#                                                                              #
#                     Create an Account Page                                    #
#                                                                              #
################################################################################

    /**
     * <p>Customer registration.  Filling in only required fields</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in required fields.</p>
     * <p>7. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is registered.</p>
     * <p>Success Message is displayed</p>
     *
     * @test
     */
    public function withRequiredFieldsOnly()
    {
        return array();
    }

    /**
     * <p>Customer registration.  Use email that already exist.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in 'Email' field by using code that already exist.</p>
     * <p>7. Fill other required fields by regular data.</p>
     * <p>8. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is not registered.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param array $userData
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withEmailThatAlreadyExists(array $userData)
    {

    }

    /**
     * <p>Customer registration. Fill in only required fields. Use max long values for fields.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in required fields by long value alpha-numeric data.</p>
     * <p>7. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is registered. Success Message is displayed.</p>
     * <p>Length of fields are 255 characters.</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withLongValues()
    {

    }

    /**
     * <p>Customer registration with empty required field.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in fields except one required.</p>
     * <p>7. Click 'Submit' button</p>
     * <p>Expected result:</p>
     * <p>Customer is not registered.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $field
     * @dataProvider emptyField
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withRequiredFieldsEmpty($field)
    {

    }
    /**
     * DataProvider for withRequiredFieldsEmpty
     *
     * @return array
     */
    public function emptyField()
    {
        return array(
            array(),//First Name
            array(),//Last Name
            array(),//Email Address
            array(),//Telephone
            array(),//City
            array(),//State/Province
            array(),//Zip/Postal Code
            array(),//Country
            array(),//Password
            array()//Confirm Password
        );
    }

    /**
     * <p>Customer registration. Fill in all required fields by using special characters(except the field "email").</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in all required fields by using special characters(except the field "email").</p>
     * <p>7. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is registered.</p>
     * <p>Success Message is displayed</p>
     *
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withSpecialCharacters()
    {

    }

    /**
     * <p>Customer registration. Fill in only required fields. Use value that is greater than the allowable.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in one field by using value that is greater than the allowable.</p>
     * <p>7. Fill other required fields by regular data.</p>
     * <p>8. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is not registered.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $longValue
     * @dataProvider dataLongValuesNotValid
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withLongValuesNotValid($longValue)
    {

    }

    /**
     * DataProvider for withLongValuesNotValid
     *
     * @return array
     */
    public function dataLongValuesNotValid()
    {
        return array(

            array(),//First Name (array('first_name' => $this->generate('string', 256, ':alnum:')))
            array(),//Last Name
            array(),//Email Address (array('email' => $this->generate('email', 256, 'valid')))
            array(),//Telephone
            array(),//City
            array(),//State/Province
            array(),//Zip/Postal Code
            array(),//Country
            array(),//Password
            array()//Confirm Password
        );
    }

    /**
     * <p>Customer registration with invalid value for 'Email' field</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in 'Email' field by wrong value.</p>
     * <p>7. Fill other required fields by regular data.</p>
     * <p>8. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is not registered.</p>
     * <p>Error Message is displayed.</p>
     *
     * @param $invalidEmail
     * @dataProvider dataInvalidEmail
     * @depends withRequiredFieldsOnly
     * @test
     */

    public function withInvalidEmail($invalidEmail)
    {

    }

    /**
     * DataProvider for withInvalidEmail
     *
     * @return array
     */
    public function dataInvalidEmail()
    {
        return array(
            array(array('email' => 'invalid')),
            array(array('email' => 'test@invalidDomain')),
            array(array('email' => 'te@st@magento.com'))
        );
    }

    /**
     * <p>Customer registration with invalid value for 'Password' fields</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Checkout with Multiple Addresses".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>5. Navigate to 'Create an Account' page.</p>
     * <p>6. Fill in 'password' fields by wrong value.</p>
     * <p>7. Fill other required fields by regular data.</p>
     * <p>8. Click 'Submit' button.</p>
     * <p>Expected result:</p>
     * <p>Customer is not registered.</p>
     * <p>Error Message is displayed.</p>
     *
     * @dataProvider dataInvalidPassword
     * @depends withRequiredFieldsOnly
     * @test
     */
    public function withInvalidPassword($invalidPassword, $errorMessage)
    {

    }

    /**
     * DataProvider for withInvalidPassword
     *
     * @return array
     */
    public function dataInvalidPassword()
    {
        return array(
            array(array('password' => 12345, 'password_confirmation' => 12345), 'short_passwords'),
            array(array('password' => 1234567, 'password_confirmation' => 12345678), 'passwords_not_match'),
        );
    }

}
