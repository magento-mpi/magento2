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
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('new_customer'),
//                'Wrong page is displayed');
        $this->assertTrue($this->controlIsPresent('button', 'back'),
                'There is no "Back" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_customer'),
                'There is no "Save" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'save_and_continue_edit'),
                'There is no "Save and Continue Edit" button on the page');
        $this->assertTrue($this->controlIsPresent('button', 'reset'),
                'There is no "Reset" button on the page');
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
        //Data
        $userData = $this->loadData('generic_customer_account');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
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
    public function test_WithEmailThatAlreadyExists()
    {
        //Data
        $userData = $this->loadData('generic_customer_account');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->errorMessage('customer_email_exist'), $this->messages);
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
    public function test_WithRequiredFieldsEmpty_ExeptEmail($emptyFields)
    {
        //Data
        $userData = $this->loadData('generic_customer_account', $emptyFields, 'email');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        foreach ($emptyFields as $key => $value) {
            $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->getTab('account_information')->findField($key);
            $this->addParameter('fieldXpath', $xpath);
            //$this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
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
        //Data
        $userData = $this->loadData('generic_customer_account', array('email' => NULL));
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->getTab('account_information')->findField('email');
        $this->addParameter('fieldXpath', $xpath);
        //$this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('fieldXpath' => $xpath)));
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
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
        //Data
        $userData = $this->loadData(
                        'generic_customer_account',
                        array(
                            'firs_name' => $this->generate('string', 32, ':punct:'),
                            'last_name' => $this->generate('string', 32, ':punct:'),
                            'password' => $this->generate('string', 32, ':punct:'),
                        ),
                        'email');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
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
        //Data
        $longValues = array(
            'first_name' => $this->generate('string', 255, ':alnum:'),
            'last_name' => $this->generate('string', 255, ':alnum:'),
            'password' => $this->generate('string', 255, ':alnum:'),
            'email' => $this->generate('email', 255, 'valid')
        );
        $userData = $this->loadData(
                        'generic_customer_account', $longValues);
        $searchData = $this->loadData(
                        'search_customer',
                        array(
                            'name' => $userData['first_name'] . ' ' . $userData['last_name'],
                            'email' => $userData['email']
                        )
        );
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
//        @TODO func 'navigated'
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
//        @TODO
//        //Steps
//        $this->searchAndOpen($searchData);
//        $this->clickControl('tab', 'account_information', FALSE);
//        //Verifying
//        foreach ($longValues as $key => $value) {
//            if ($key != 'password') {
//                $xpath = $this->getCurrentLocationUimapPage()->getMainForm()->getTab('account_information')->findField($key);
//                $this->assertEquals(strlen($this->getValue('//' . $xpath)), 255);
//            }
//        }
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
        //Data
        $userData = $this->loadData('generic_customer_account', $wrongEmail);
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->errorMessage('customer_invalid_email'), $this->messages);
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
        //Data
        $userData = $this->loadData('generic_customer_account', array('password' => '12345'), 'email');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickButton('save_customer');
        //Verifying
        $this->assertTrue($this->errorMessage('password_too_short'), $this->messages);
    }

    /**
     * Create customer with address
     */
    public function test_WithAddress()
    {
        //Data
        $userData = $this->loadData('all_fields_customer_account');
        $adressData = $this->loadData('all_fields_address');
        //Steps
        $this->clickButton('add_new_customer');
        $this->fillForm($userData, 'account_information');
        $this->clickControl('tab', 'addresses', FALSE);
        $this->clickButton('add_new_address', FALSE);
        $this->addParameter('address_number', 1);
        //$this->appendParamsDecorator(new Mage_Selenium_Helper_Params(array('address_number' => 1)));
        $this->fillForm($adressData, 'addresses');
        $this->clickButton('save_customer');
        
        //Verifying
//        $this->assertTrue($this->navigated('manage_customers'),
//                'After successful customer creation should be redirected to Manage Customers page');
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
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
