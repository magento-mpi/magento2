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
class Customer_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * Log in to Backend.
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * Preconditions:
     * Navigate to System -> Manage Customers
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * Test navigation.
     *
     * Steps:
     *
     * 1. Verify that 'Add New Customer' button is present and click her.
     *
     * 2. Verify that the create customer page is opened.
     *
     * 3. Verify that 'Back' button is present.
     *
     * 4. Verify that 'Save Customer' button is present.
     *
     * 5. Verify that 'Reset' button is present.
     */
    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('add_new_customer'),
                'There is no "Add New Customer" button on the page');
        $this->assertTrue($this->checkCurrentPage('create_customer'), 'Wrong page is opened');
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_customer'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_and_continue_edit'),
                'There is no "Save and Continue Edit" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
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
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadData('generic_customer_account', NULL, 'email');
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');

        return $userData;
    }

    /**
     * Create customer. Use email that already exist
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in 'Email' field by using email that already exist.
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
    public function test_WithEmailThatAlreadyExists(array $userData)
    {
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('customer_email_exist'), $this->messages);
    }

    /**
     * Ceate customer with one empty reqired field
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
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRequiredFieldsEmpty($emptyField)
    {
        //Data
        if ($emptyField != 'email') {
            $userData = $this->loadData('generic_customer_account',
                            array($emptyField => '%noValue%'), 'email');
        } else {
            $userData = $this->loadData('generic_customer_account',
                            array($emptyField => '%noValue%'));
        }
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $tab = $this->getCurrentLocationUimapPage()->findTab('account_information');
        $xpath = $tab->findField($emptyField);
        $this->addParameter('fieldXpath', $xpath);
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array('first_name'),
            array('last_name'),
            array('password'),
            array('email')
        );
    }

    /**
     * Create customer. Fill in all fields by using special characters(except the field "email").
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in fields on 'Account Information' tab.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is created.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_ExeptEmail()
    {
        //Data
        $userData = $this->loadData('generic_customer_account',
                        array(
                            'prefix'         => $this->generate('string', 32, ':punct:'),
                            'first_name'     => $this->generate('string', 32, ':punct:'),
                            'middle_name'    => $this->generate('string', 32, ':punct:'),
                            'last_name'      => $this->generate('string', 32, ':punct:'),
                            'suffix'         => $this->generate('string', 32, ':punct:'),
                            'tax_vat_number' => $this->generate('string', 32, ':punct:'),
                            'password'       => $this->generate('string', 32, ':punct:')
                        ), 'email'
        );
        $searchData = $this->loadData('search_customer',
                        array('name' => '%noValue%', 'email' => $userData['email']));
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->CustomerHelper()->openCustomer($searchData);
        $this->clickControl('tab', 'account_information', FALSE);
        //Verifying
        $this->assertTrue($this->verifyForm($userData, 'account_information'), $this->messages);
    }

    /**
     * Create Customer. Fill in fields. Use max long values for fields.
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in fields by long value alpha-numeric data on 'Account Information' tab.
     *
     * 3. Click 'Save Customer' button.
     *
     * Expected result:
     *
     * Customer is created. Success Message is displayed.
     *
     * Length of fields are 255 characters.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $longValues = array(
            'prefix'         => $this->generate('string', 255, ':alnum:'),
            'first_name'     => $this->generate('string', 255, ':alnum:'),
            'middle_name'    => $this->generate('string', 255, ':alnum:'),
            'last_name'      => $this->generate('string', 255, ':alnum:'),
            'suffix'         => $this->generate('string', 255, ':alnum:'),
            'email'          => $this->generate('email', 128, 'valid'),
            'tax_vat_number' => $this->generate('string', 255, ':alnum:'),
            'password'       => $this->generate('string', 255, ':alnum:')
        );
        $userData = $this->loadData('generic_customer_account', $longValues);
        $searchData = $this->loadData('search_customer',
                        array('name' => '%noValue%', 'email' => $userData['email']));
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        //Steps
        $this->CustomerHelper()->openCustomer($searchData);
        $this->clickControl('tab', 'account_information', FALSE);
        //Verifying
        $this->assertTrue($this->verifyForm($userData, 'account_information'), $this->messages);
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
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidEmail($wrongEmail)
    {
        //Data
        $userData = $this->loadData('generic_customer_account', array('email' => $wrongEmail));
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('customer_invalid_email'), $this->messages);
    }

    public function data_InvalidEmail()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@magento.com')
        );
    }

    /**
     * Create customer. Use a value for 'Password' field the length of which less than 6 characters.
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
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidPassword()
    {
        //Data
        $userData = $this->loadData('generic_customer_account',
                        array('password' => $this->generate('string', 5, ':alnum:')), 'email');
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('password_too_short'), $this->messages);
    }

    /**
     * Create customer with auto-generated password
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
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithAutoGeneratedPassword()
    {
        //Data
        $userData = $this->loadData('generic_customer_account',
                        array('password' => '%noValue%', 'auto_generated_password' => 'Yes'),
                        'email');
        //Steps
        $this->CustomerHelper()->createCustomer($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
    }

    /**
     * Create customer with one address by filling all fields
     *
     * Steps:
     *
     * 1. Click 'Add New Customer' button.
     *
     * 2. Fill in fields on 'Account Information' tab.
     *
     * 3. Open 'Addresses' tab.
     *
     * 4. Click 'Add New Address' button.
     *
     * 5. Fill in all fields on 'Addresses' tab.
     *
     * 6. Click 'Save Customer' button.
     *
     * Expected result:|
     *
     * Customer with address is created.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithAddress()
    {
        //Data
        $userData = $this->loadData('all_fields_customer_account', NULL, 'email');
        $addressData = $this->loadData('all_fields_address');
        //Steps
        $this->CustomerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithAddress()
    {
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_OnOrderPage_WithoutAddress()
    {
        $this->markTestIncomplete();
    }

}
