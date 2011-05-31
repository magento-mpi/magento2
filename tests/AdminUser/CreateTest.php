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
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AdminUser_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Permissions -> Users.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->assertTrue($this->checkCurrentPage('manage_admin_users'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * Test navigation.
     *
     * Steps:
     *
     * 1. Verify that 'Add New User' button is present and click her.
     *
     * 2. Verify that the create user page is opened.
     *
     * 3. Verify that 'Back' button is present.
     *
     * 4. Verify that 'Save User' button is present.
     *
     * 5. Verify that 'Reset' button is present.
     */
    public function test_Navigation()
    {
        $this->assertTrue($this->clickButton('add_new_admin_user'),
                'There is no "Add New Customer" button on the page');
        $this->assertTrue($this->checkCurrentPage('new_admin_user'), 'Wrong page is opened');
        $this->assertTrue($this->buttonIsPresent('back'), 'There is no "Back" button on the page');
        $this->assertTrue($this->buttonIsPresent('save_admin_user'),
                'There is no "Save User" button on the page');
        $this->assertTrue($this->buttonIsPresent('reset'), 'There is no "Reset" button on the page');
    }

    /**
     * Create Admin User (all required fields are filled).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields.
     *
     * 4.Press "Save User" button.
     *
     * Expected result:
     *
     * New user successfully saved.
     *
     * Message "The user has been saved." is displayed.
     *
     * @depends test_Navigation
     */
    public function test_WithRequiredFieldsOnly()
    {
        //Data
        $userData = $this->loadData('generic_admin_user', Null, array('email', 'user_name'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('edit_admin_user'),
                'After successful user creation should be redirected to Edit User page');

        return $userData;
    }

    /**
     * Create Admin User. Use user name that already exist
     *
     * Steps:
     *
     * 1. Click 'Add New User' button.
     *
     * 2. Fill in 'user name' field by using data that already exist.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save User' button.
     *
     * Expected result:
     *
     * User is not created. Error Message is displayed.
     * @depends test_WithRequiredFieldsOnly
     * @param array $userData
     */
    public function test_WithUserNameThatAlreadyExists($userData)
    {
        //Data
        $userData['email'] = $this->generate('email', 20, 'valid');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('exist_name_or_email'), $this->messages);
    }

    /**
     * Create Admin User. Use email that already exist
     *
     * Steps:
     *
     * 1. Click 'Add New User' button.
     *
     * 2. Fill in 'email' field by using email that already exist.
     *
     * 3. Fill other required fields by regular data.
     *
     * 4. Click 'Save User' button.
     *
     * Expected result:
     *
     * User is not created. Error Message is displayed.
     * @depends test_WithRequiredFieldsOnly
     * @param array $userData
     */
    public function test_WithUserEmailThatAlreadyExists($userData)
    {
        //Data
        $userData['user_name'] = $this->generate('string', 5, ':lower:');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('exist_email'), $this->messages);
    }

    /**
     * Create Admin User with one empty reqired field.
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill fields exept one required.
     *
     * 4.Press "Save User" button.
     *
     * Expected result:
     *
     * New user is not saved.
     *
     * Message "This is a required field." is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_emptyFields
     */
    public function test_WithRequiredFieldsEmpty($emptyField, $messageCount)
    {
        //Data
        if (array_key_exists('user_name', $emptyField)) {
            $userData = $this->loadData('generic_admin_user', $emptyField, 'email');
        } elseif (array_key_exists('email', $emptyField)) {
            $userData = $this->loadData('generic_admin_user', $emptyField, 'user_name');
        } else {
            $userData = $this->loadData('generic_admin_user', $emptyField,
                            array('email', 'user_name'));
        }
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $page = $this->getUimapPage('admin', 'new_admin_user');
        foreach ($emptyField as $key => $value) {
            $xpath = $page->findField($key);
            $this->addParameter('fieldXpath', $xpath);
        }
        $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
        $this->assertTrue($this->verifyMessagesCount($messageCount), $this->messages);
    }

    public function data_emptyFields()
    {
        return array(
            array(array('user_name' => '%noValue%'), 1),
            array(array('first_name' => '%noValue%'), 1),
            array(array('last_name' => '%noValue%'), 1),
            array(array('email' => '%noValue%'), 1),
            array(array('password' => '%noValue%'), 2),
            array(array('password_confirmation' => '%noValue%'), 1),
        );
    }

    /**
     * Create Admin User (all required fields are filled by special chracters).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill in all required fields by special chracters
     * (exept 'email', 'password' and 'password_confirmation' fields).
     *
     * 4.Fill in 'email', 'password' and 'password_confirmation' fields by valid data.
     *
     * 5.Press "Save User" button.
     *
     * Expected result:
     *
     * New user is saved.
     *
     * Message "The user has been saved." is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithSpecialCharacters_exeptEmail()
    {
        //Data
        $specialCharacters = array(
            'user_name' => $this->generate('string', 32, ':punct:'),
            'first_name' => $this->generate('string', 32, ':punct:'),
            'last_name' => $this->generate('string', 32, ':punct:'),
        );
        $userData = $this->loadData('generic_admin_user', $specialCharacters, 'email');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('edit_admin_user'),
                'After successful user creation should be redirected to Edit User page');
        $this->assertTrue(
                $this->verifyForm(
                        $userData, 'user_info', array('password', 'password_confirmation')
                ), $this->messages);
    }

    /**
     * Create Admin User (all required fields are filled by long value data).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields by long value data (exclude 'email').
     *
     * 4.Press "Save User" button.
     *
     * Expected result:
     *
     * New user is not saved.
     *
     * Message "The user has been saved." is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithLongValues()
    {
        //Data
        $password = $this->generate('string', 255, ':alnum:');
        $longValues = array(
            'user_name' => $this->generate('string', 40, ':alnum:'),
            'first_name' => $this->generate('string', 32, ':alnum:'),
            'last_name' => $this->generate('string', 32, ':alnum:'),
            'email' => $this->generate('email', 128, 'valid'),
            'password' => $password,
            'password_confirmation' => $password
        );
        $userData = $this->loadData('generic_admin_user', $longValues);
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('edit_admin_user'),
                'After successful user creation should be redirected to Edit User page');
        $this->assertTrue(
                $this->verifyForm(
                        $userData, 'user_info', array('password', 'password_confirmation')
                ), $this->messages);
    }

    /**
     * Create Admin User. Use wrong values for 'password' fields.
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields by regular data (exclude 'Password' and 'Password Confirmation').
     *
     * 4.Fill 'Password' and 'Password Confirmation' by wrong values.
     *
     * 5.Press "Save User" button.
     *
     * Expected result:
     *
     * New user is not saved.
     *
     * Error Message is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_invalidPassword
     */
    public function test_WithInvalidPassword($wrongPasswords, $errorMessage)
    {
        //Data
        $userData = $this->loadData('generic_admin_user', $wrongPasswords,
                        array('email', 'user_name'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->errorMessage($errorMessage), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_invalidPassword()
    {
        return array(
            array(array(
                    'password' => '1234567890',
                    'password_confirmation' => '1234567890',
                ), 'invalid_password'),
            array(array(
                    'password' => 'qwertyqw',
                    'password_confirmation' => 'qwertyqw',
                ), 'invalid_password'),
            array(array(
                    'password' => '123qwe',
                    'password_confirmation' => '123qwe',
                ), 'invalid_password'),
            array(array(
                    'password' => '123123qwe',
                    'password_confirmation' => '1231234qwe',
                ), 'password_unmatch')
        );
    }

    /**
     * Create Admin User (with invalid data in the 'email' field).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields by regular data (exclude 'email').
     *
     * 4.Fill 'email' field by invalid data [example: me&you@domain.com / me&you@com / nothing@домен.com].
     *
     * 5.Press "Save User" button.
     *
     * Expected result:
     *
     * New user is not saved.
     *
     * Message "Please enter a valid email." OR "Please enter a valid email address.
     * For example johndoe@domain.com." is displayed.
     *
     * @depends test_WithRequiredFieldsOnly
     * @dataProvider data_InvalidEmail
     */
    public function test_WithInvalidEmail($invalidEmail)
    {
        //Data
        $userData = $this->loadData('generic_admin_user', $invalidEmail, 'user_name');
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->errorMessage('invalid_email'), $this->messages);
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
     * Create Admin User  (as Inactive).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields.
     *
     * 4.Choose in the 'This account is' dropdown - "Inactive".
     *
     * 5.Press "Save User" button.
     *
     * Expected result:
     *
     * New user successfully saved. Message "The user has been saved." is displayed.
     *
     * 6.Log out
     *
     * 7.Log in using created user.
     *
     * Expected result:
     *
     * Error Message "This account is inactive." is displayed.
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_InactiveUser()
    {
        //Data
        $userData = $this->loadData('generic_admin_user',
                        array('this_acount_is' => 'Inactive', 'role_name' => 'Administrators'),
                        array('email', 'user_name')
        );
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        //Steps
        $this->logoutAdminUser();
        $this->fillForm($userData);
        $this->clickButton('login');
        //Verifying
        $this->assertTrue($this->errorMessage('inactive_aacount'), $this->messages);
    }

    /**
     * Create Admin User (with Admin User Role).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields.
     *
     * 4.Choose in the 'User Role' grid - "Administrators" role.
     *
     * 5.Press "Save User" button.
     *
     * Expected result:
     *
     * New user successfully saved. Message "The user has been saved." is displayed
     *
     * 6.Log out
     *
     * 7.Log in using created user.
     *
     * Expected result:
     *
     * Logged in to Admin.
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithRole()
    {
        //Data
        $userData = $this->loadData('generic_admin_user', array('role_name' => 'Administrators'),
                        array('email', 'user_name'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        //Steps
        $this->logoutAdminUser();
        $this->fillForm($userData);
        $this->clickButton('login');
        //Verifying
        $this->assertTrue($this->checkCurrentPage('dashboard'), 'Wrong page is opened');
    }

    /**
     * Create Admin User (with Admin User Role).
     *
     * Steps:
     *
     * 1.Go to System-Permissions-Users.
     *
     * 2.Press "Add New User" button.
     *
     * 3.Fill all required fields.
     *
     * 4.Press "Save User" button.
     *
     * Expected result:
     *
     * New user successfully saved. Message "The user has been saved." is displayed
     *
     * 6.Log out
     *
     * 7.Log in using created user.
     *
     * Expected result:
     *
     * Error Message "Access denied." is displayed.
     * @depends test_WithRequiredFieldsOnly
     */
    public function test_WithoutRole()
    {
        //Data
        $userData = $this->loadData('generic_admin_user', NULL, array('email', 'user_name'));
        //Steps
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        //Steps
        $this->logoutAdminUser();
        $this->fillForm($userData);
        $this->clickButton('login');
        //Verifying
        $this->assertTrue($this->errorMessage('access_denied'), $this->messages);
    }

}
