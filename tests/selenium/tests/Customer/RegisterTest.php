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
 * Customer registration tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Customer_RegisterTest extends Mage_Selenium_TestCase {

    /**
     * Make sure that customer is not logged in, and navigate to homepage
     */
    protected function assertPreConditions()
    {
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
    }

    /**
     * Сustomer registration.  Filling in only required fields
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in reqired fields.
     *
     * 4. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is registered.
     *
     * Success Message is displayed
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadData('customer_account_register',
                        array('email' => $this->generate('email', 20, 'valid')));
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->successMessage('success_registration'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('customer_account'),
                'After succesfull registration customer should be redirected to account dashboard');

        return $userData;
    }

    /**
     * Сustomer registration.  Use email that already exist.
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in 'Email' field by using code that already exist.
     *
     * 4. Fill other required fields by regular data.
     *
     * 5. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is not registered.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithEmailThatAlreadyExists(array $userData)
    {
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->errorMessage('email_exists'), $this->messages);
    }

    /**
     * Сustomer registration. Fill in only reqired fields. Use max long values for fields.
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in reqired fields by long value alpha-numeric data.
     *
     * 4. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is registered. Success Message is displayed.
     *
     * Length of fields are 255 characters.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $password = $this->generate('string', 255, ':alnum:');
        $userData = $this->loadData(
                        'customer_account_register',
                        array(
                            'first_name' => $this->generate('string', 255, ':alnum:'),
                            'last_name' => $this->generate('string', 255, ':alnum:'),
                            'email' => $this->generate('email', 128, 'valid'),
                            'password' => $password,
                            'password_confirmation' => $password,
                        )
        );
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->successMessage('success_registration'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('customer_account'),
                'After succesfull registration customer should be redirected to account dashboard');
        //Steps
        $this->clickControl('tab', 'account_information');
        //Verifying
        $page = $this->getUimapPage('frontend', 'customer_account');
        $tab = $page->findTab('account_information');
        foreach ($userData as $key => $value) {
            if ($key == 'first_name' or $key == 'last_name' or $key == 'email') {
                $xpath = $tab->findField($key);
                $this->assertEquals($value, $this->getValue('//' . $xpath),
                        "The stored value for '$key' field is not equal to specified");
            }
        }
    }

    /**
     * Сustomer registration with empty reqired field.
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in fields exept one required.
     *
     * 4. Click 'Submit' button
     *
     * Expected result:
     *
     * Customer is not registered.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_EmptyField
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRequiredFieldsEmpty($field, $messageCount)
    {
        //Data
        $userData = $this->loadData('customer_account_register', $field);
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $page = $this->getCurrentLocationUimapPage();
        $fieldset = $page->findFieldset('account_info');
        foreach ($field as $key => $value) {
            $xpath = $fieldset->findField($key);
            $this->addParameter('fieldXpath', $xpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->messages);
    }

    public function data_EmptyField()
    {
        return array(
            array(array('first_name' => ''), 1),
            array(array('last_name' => ''), 1),
            array(array('email' => ''), 1),
            array(array('password' => ''), 2),
            array(array('password_confirmation' => ''), 1),
        );
    }

    /**
     * Сustomer registration. Fill in all reqired fields by using special characters(except the field "email").
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in reqired fields.
     *
     * 4. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is registered.
     *
     * Success Message is displayed
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters()
    {
        //Data
        $password = $this->generate('string', 25, ':punct:');
        $userData = $this->loadData(
                        'customer_account_register',
                        array(
                            'first_name' => $this->generate('string', 25, ':punct:'),
                            'last_name' => $this->generate('string', 25, ':punct:'),
                            'email' => $this->generate('email', 20, 'valid'),
                            'password' => $password,
                            'password_confirmation' => $password,
                ));
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->successMessage('success_registration'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('customer_account'),
                'After succesfull registration customer should be redirected to account dashboard');
    }

    /** Сustomer registration. Fill in only reqired fields. Use value that is greater than the allowable.
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in one field by using value that is greater than the allowable.
     *
     * 4. Fill other required fields by regular data.
     *
     * 5. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is not registered.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_LongValues_NotValid
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues_NotValid($longValue)
    {
        //Data
        $userData = $this->loadData('customer_account_register', $longValue);
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        foreach ($longValue as $key => $value) {
            $fieldName = $key;
        }
        $this->assertTrue($this->errorMessage("not_valid_length_$fieldName"), $this->messages);
    }

    public function data_LongValues_NotValid()
    {
        return array(
            array(array('first_name' => $this->generate('string', 256, ':alnum:'))),
            array(array('last_name' => $this->generate('string', 256, ':alnum:'))),
            array(array('email' => $this->generate('email', 256, 'valid'))),
        );
    }

    /**
     * Сustomer registration with invalid value for 'Email' field
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in 'Email' field by wrong value.
     *
     * 4. Fill other required fields by regular data.
     *
     * 5. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is not registered.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_InvalidEmail
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidEmail($invalidEmail)
    {
        //Data
        $userData = $this->loadData('customer_account_register', $invalidEmail);
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->errorMessage('invalid_mail'), $this->messages);
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
     * Сustomer registration with invalid value for 'Password' fields
     *
     * Steps:
     *
     * 1. Navigate to 'Login or Create an Account' page.
     *
     * 2. Click 'Register' button.
     *
     * 3. Fill in 'password' fields by wrong value.
     *
     * 4. Fill other required fields by regular data.
     *
     * 5. Click 'Submit' button.
     *
     * Expected result:
     *
     * Customer is not registered.
     *
     * Error Message is displayed.
     *
     * @dataProvider data_InvalidPassword
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithInvalidPassword($invalidPassword, $errorMessage)
    {
        //Data
        $userData = $this->loadData('customer_account_register', $invalidPassword);
        //Steps
        $this->navigate('customer_login');
        $this->clickButton('create_account');
        $this->fillForm($userData);
        $this->saveForm('submit');
        //Verifying
        $this->assertTrue($this->errorMessage($errorMessage), $this->messages);
    }

    public function data_InvalidPassword()
    {
        return array(
            array(array('password' => 12345, 'password_confirmation' => 12345), 'short_passwords'),
            array(array('password' => 1234567, 'password_confirmation' => 12345678), 'passwords_not_match'),
        );
    }

    /**
     * @TODO
     */
    public function test_FromOnePageCheckoutPage()
    {
        // @TODO
        $this->markTestIncomplete();
    }

    /**
     * @TODO
     */
    public function test_FromMultipleCheckoutPage()
    {
        // @TODO
        $this->markTestIncomplete();
    }

}
