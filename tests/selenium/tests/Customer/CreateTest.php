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
 * Test creation customer from Backend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_CreateTest extends Mage_Selenium_TestCase {

    /**
     * Log in to Admin and Navigate to Manage Customers.
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->navigate('manage_customers'));
    }

    /**
     * Create customer by filling in only required fields
     * Expected result:
     * Redirect to manage customers page, sucsess massage appeared
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account', null, null));
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_customers'),
                'After creating customer admin should be redirected to manage customers page');
        $this->assertTrue($this->successMessage('success_save_customer'),
                'No success message is displayed');
    }

    /**
     * Create customer. Use email that alreadi exist
     * Expected result:
     * 'Customer with the same email already exists.' message appeared
     */
    public function test_WithEmailThatAlreadyExists()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account', null, null));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_email_exist'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Ceate customer with empty reqired 'First Name' field
     * Expected result:
     * 'This is a required field' message appeared under 'First Name' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyFirstName()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account',
                        array('first_name' => null), null));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with empty reqired 'Last Name' field
     * Expected result:
     * 'This is a required field' message appeared under 'Last Name' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyLastName()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account',
                        array('last_name' => NULL), 'email'));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with empty reqired 'Email' field
     * Expected result:
     * 'This is a required field' message appeared under 'Email' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyEmail()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account',
                        array('email' => NULL), NULL));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with empty reqired 'Password' field
     * Expected result:
     * 'This is a required field' message appeared under 'Password' field
     */
    public function test_WithRequiredFieldsEmpty_EmptyPassword()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account',
                        array('password' => NULL), 'email'));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer. Fill in all reqired fields by
     * using using special characters(except the field "email").
     */
    public function test_WithSpecialCharacters()
    {
        $this->clickButton('add_new_customer');
        $longValues = array(
            'firs_name' => $this->generate('string', 255, ':punct:'),
            'last_name' => $this->generate('string', 255, ':punct:'),
            'password' => $this->generate('string', 255, ':punct:'),
        );
        $this->fillForm($this->loadData('generic_customer_account', $longValues, NULL));
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_customers'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
    }

    /**
     *  Create Customer. Fill in only reqired fields. Use max long values for fields
     */
    public function test_WithLongValues()
    {
        $this->clickButton('add_new_customer');
        $longValues = array(
            'firs_name' => $this->generate('string', 255, ':alnum:'),
            'last_name' => $this->generate('string', 255, ':alnum:'),
            'password' => $this->generate('string', 255, ':alnum:'),
            'email' => $this->generate('email', 255, ':alnum:'),
        );
        $this->fillForm($this->loadData('generic_customer_account', $longValues, NULL));
        $this->clickButton('save_customer');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->navigated('manage_customers'),
                'After successful creation store should be redirected to Manage Stores page');
        $this->assertTrue($this->successMessage('success_saved_store'),
                'No success message is displayed');
        // @TODO
        //$this->searchAndOpen($longValues);
        //$xpathName = $this->_getUimapData('');
        //$this->assertEquals(strlen($this->getValue($xpathName)), 255);
    }

    /**
     * Create customer with invalid 'Email' field
     * Expected result:
     * '"Email" is not a valid email address.' message appeared under 'Email' field
     */
    public function test_WithInvalidEmail()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('generic_customer_account',
                        array('email' => 'mail@domain'), NULL));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_invalid_email'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with invalid 'Password' field
     * Expected result:
     * 'Please enter 6 or more characters. Leading or trailing spaces will be ignored.'
     * message appeared under 'Password' field
     */
    public function test_WithInvalidPassword()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('create_customer',
                        array('password' => '12345'), 'email'));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_password_too_short'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
    }

    /**
     * Create customer with address
     */
    public function test_WithAddress()
    {
        $this->clickButton('add_new_customer');
        $this->fillForm($this->loadData('all_fields_customer_account', NULL, NULL));
        $this->clickButton('add_new_address');
        $this->fillForm($this->loadData('all_fields_address', NULL, NULL));
        $this->clickButton('save_customer');
        $this->assertTrue($this->errorMessage('error_empty_value'),
                'No error message is displayed');
        $this->assertFalse($this->successMessage(), $this->messages);
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
