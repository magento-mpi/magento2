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
 * Test creation new customer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_CreateTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Manage Customers
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin());
        $this->assertTrue($this->navigate('manage_customers'));
    }

    /**
     *
     * @param string $userData
     */
    public function createCustomer($userData)
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
    }

    /**
     * Create customer by filling in only required fields
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in reqired fields.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is created.
     *
     * Success Message is displayed
     */
    public function test_WithRequiredFieldsOnly()
    {
        $userData = $this->loadData('generic_customer_account', null, null);
        $this->createCustomer($userData);
        $this->assertFalse($this->errorMessage(), $this->messages);
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_save_customer'),
                'No success message is displayed');
    }

    /**
     * Create customer. Use email that already exist
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in 'Email' field by using code that already exist.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is not created.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithEmailThatAlreadyExists()
    {
        $userData = $this->loadData('generic_customer_account', null, null);
        $this->createCustomer($userData);
        $this->assertTrue($this->errorMessage('customer_email_exist'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Ceate customer with empty reqired field excluding 'email' field.
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in fields exept one required.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is not created.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_EmptyField
     */
    public function test_WithRequiredFieldsEmpty($field)
    {
        $userData = $this->loadData('generic_customer_account', $field, 'email');
        $this->createCustomer($userData);
        foreach ($field as $key => $value) {
            $xpath = '//' . $this->getCurrentLocationUimapPage()->findFieldset('account_info')->findField($key);
//            echo "$xpath";
        }
//        $v = $this->getCurrentLocationUimapPage()->findMessage('empty_reqired_field');
//        echo "$v";
//        sleep(10);
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('first_name' => null)),
            array(array('last_name' => null)),
            array(array('password' => null)),
        );
    }

    /**
     * Ceate customer with empty reqired field 'email'.
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in required fields exept 'email'.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is not created.
     *
     * Error Message is displayed.
     *
     */
    public function test_WithRequiredFieldsEmpty_EmptyEmail()
    {
        $userData = $this->loadData('generic_customer_account', array('email' => NULL), NULL);
        $this->createCustomer($userData);
        $this->assertTrue($this->errorMessage('empty_reqired_field'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer. Fill in all reqired fields by using special characters(except the field "email").
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in reqired fields.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is created.
     *
     * Success Message is displayed
     */
    public function test_WithSpecialCharacters_ExeptEmail()
    {
        $userData = $this->loadData(
                        'generic_customer_account',
                        array(
                            'firs_name' => $this->generate('string', 32, ':punct:'),
                            'last_name' => $this->generate('string', 32, ':punct:'),
                            'password' => $this->generate('string', 32, ':punct:'),
                        ),
                        'email');
        $this->createCustomer($userData);
        $this->assertFalse($this->errorMessage(), $this->messages);
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'),
                'No success message is displayed');
    }

    /**
     * Create Customer. Fill in only reqired fields. Use max long values for fields.
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in required fields by long value alpha-numeric data.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is created. Success Message is displayed.
     *
     * Length of fields are 255 characters.
     */
    public function test_WithLongValues()
    {
        $userData = $this->loadData(
                        'generic_customer_account',
                        array(
                            'firs_name' => $this->generate('string', 255, ':alnum:'),
                            'last_name' => $this->generate('string', 255, ':alnum:'),
                            'password' => $this->generate('string', 255, ':alnum:'),
                            'email' => $this->generate('email', 255, ':alnum:')
                        ),
                        NULL);
        $this->createCustomer($userData);
        $this->assertFalse($this->errorMessage(), $this->messages);
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'),
                'No success message is displayed');
        // @TODO
        //$this->searchAndOpen();
        //$xpathName = $this->_getUimapData('');
        //$this->assertEquals(strlen($this->getValue($xpathName)), 255);
    }

    /**
     * Create customer with invalid value for 'Email' field
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in 'Email' field by wrong value.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is not created.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_InvalidEmail
     */
    public function test_WithInvalidEmail($wrongEmail)
    {
        $userData = $this->loadData('generic_customer_account', $wrongEmail, NULL);
        $this->createCustomer($userData);
        $this->assertTrue($this->errorMessage('customer_invalid_email'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    public function data_InvalidEmail()
    {
        return array(
            array(array('email' => 'invalid')),
            array(array('email' => 'test@invalidDomain')),
            array(array('email' => 'te@st@magento.com'))
        );
    }

    /**
     * Create customer. Use a value for 'Password' field the length of which less than 6 characters
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in 'Password' field by wrong value.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is not created.
     *
     * Error Message is displayed.
     *
     */
    public function test_WithInvalidPassword()
    {
        $userData = $this->loadData('generic_customer_account', array('password' => '12345'), 'email');
        $this->createCustomer($userData);
        $this->assertTrue($this->errorMessage('password_too_short'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with address
     */
    public function test_WithAddress()
    {


        $userData = $this->loadData('all_fields_customer_account', NULL, NULL);
        $adressData = $this->loadData('all_fields_address', NULL, NULL);

        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address', FALSE);
        $this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number' => 1)));
        $this->fillForm($adressData, 'addresses');
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'),
                'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithAddress()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithoutAddress()
    {
        // @TODO
    }

}