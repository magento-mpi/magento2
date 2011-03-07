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
    protected function assertPreConditions()
    {
        $this->assertTrue($this->loginAdminUser());
        $this->assertTrue($this->admin('dashboard'));
    }

    public function testNavigation()
    {
        $this->assertTrue($this->navigate('manage_admin_users'));
        $this->assertTrue($this->clickButton('add_new_admin_user'), 'There is no "Add New User Button" button on the page');
        $this->assertTrue($this->navigated('new_admin_user'), 'Wrong page is displayed');
        $this->assertTrue($this->navigate('new_admin_user'), 'Wrong page is displayed when accessing direct URL');
    }

    /**
     * Create Admin User (all required fields are filled)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Press "Save User" button
     * Expected result: new user successfully saved.Message "The user has been saved." is displayed
     */
    public function test_WithRequiredFieldsOnly()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', null, null));
        $this->clickButton('save_admin_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "User Name" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "User Name" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyUserName()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('user_name' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "First Name" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "First Name" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyFirstName()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('first_name' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "Last Name" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "Last Name" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyLastName()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('last_name' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "Email" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "Email" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyEmail()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('email' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "Password" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "Password" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyPassword()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('password' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (Empty "Password Confirmation" field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Leave "Password Confirmation" field empty
     * 4.Press "Save User" button
     * Expected result: new user is not saved. Message "This is a required field." is displayed
     */
    public function test_WithRequiredFieldsEmpty_EmptyPasswordConfirmation()
    {
        $this->assertTrue(
            $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array('password_confirmation' => '') , null));
        $this->clickButton('save_admin_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (all required fields are filled by special chracters)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by special chracters (exclude 'email')
     * 4.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please enter 7 or more characters. Password should contain
     *                  both numeric and alphabetic characters." is displayed under 'Password' field
     */
    public function test_WithSpecialCharacters()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password = $this->generate('string', 260, ':punct:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'user_name' => $this->generate('string', 260, ':punct:'),
            'first_name' => $this->generate('string', 260, ':punct:'),
            'last_name' => $this->generate('string', 260, ':punct:'),
            'password' => $password, 'password_confirmation' => $password),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

     /**
     * Create Admin User (all required fields are filled by long value data)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by long value data (exclude 'email')
     * 4.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "This is a required field." is displayed
     */
    public function test_WithLongValues()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password = $this->generate('string', 260, ':alnum:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'user_name' => $this->generate('string', 260, ':alnum:'),
            'first_name' => $this->generate('string', 260, ':alnum:'),
            'last_name' => $this->generate('string', 260, ':alnum:'),
            'password' => $password, 'password_confirmation' => $password),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with invalid data in the 'email' field)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by regular data (exclude 'email')
     * 4.Fill 'email' field by invalid data [example: me&you@domain.com / me&you@com / nothing@домен.com]
     * 5.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please enter a valid email." OR "Please enter a valid email address. For
     *                  example johndoe@domain.com." is displayed
     */
    public function test_WithInvalidValues_InvalidEmail()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array(
            'email' => $this->generate('string', 260, ':invalid-email:')),NULL));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with ONLY numeric data in the 'Password' and 'Password Confirmation' fields)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by regular data (exclude 'Password' and 'Password Confirmation')
     * 4.Fill 'Password' and 'Password Confirmation' by similar numeric only data [example: 010101010101]
     * 5.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please enter 7 or more characters. Password should contain
     *                  both numeric and alphabetic characters." is displayed
     */
    public function test_WithInvalidValues_NumericPassword()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password = $this->generate('string', 10, ':digit:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'password' => $password, 'password_confirmation' => $password),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with ONLY alphabetic data in the 'Password' and 'Password Confirmation' fields)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by regular data (exclude 'Password' and 'Password Confirmation')
     * 4.Fill 'Password' and 'Password Confirmation' by similar alphabetic only data [example: abcdefgh]
     * 5.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please enter 7 or more characters. Password should contain
     *                  both numeric and alphabetic characters." is displayed
     */
    public function test_WithInvalidValues_AlphabeticPassword()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password = $this->generate('string', 10, ':alpha:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'password' => $password, 'password_confirmation' => $password),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with regular but shorten then 7 characters data in the 'Password' and 'Password Confirmation' fields)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by regular data (exclude 'Password' and 'Password Confirmation')
     * 4.Fill 'Password' and 'Password Confirmation' by regular data less then 7 characters [example: abcdef]
     * 5.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please enter 7 or more characters. Password should contain
     *                  both numeric and alphabetic characters." is displayed
     */
    public function test_WithInvalidValues_ShortPassword()
    {
       $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password = $this->generate('string', 6, ':alnum:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'password' => $password, 'password_confirmation' => $password),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with not matched data in the 'Password' and 'Password Confirmation' fields)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields by regular data (exclude 'Password' and 'Password Confirmation')
     * 4.Fill 'Password' by regular data [example: q2q2q2q2q2]
     * 5.Fill 'Password Confirmation' by another regular data [example: q1q1q1q1q1]
     * 6.Press "Save User" button
     * Expected result: new user is not saved.
     *                  Message "Please make sure your passwords match." is displayed
     */
    public function test_WithInvalidValues_PasswordsNotMatch()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $password_main = $this->generate('string', 10, ':alnum:');
        $password_conf = $this->generate('string', 10, ':alnum:');
        $this->fillForm($this->loadData('generic_admin_user', array(
            'password' => $password_main, 'password_confirmation' => $password_conf),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User  (as Inactive)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Choose in the 'This account is' dropdown - "Inactive"
     * 5.Press "Save User" button
     *
     * Expected result: new user successfully saved.
     *                  Message "The user has been saved." is displayed
     */
    public function test_InactiveUser()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array(
            'this_acount_is' => 'Inactive'),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User (with Admin User Role)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Choose in the 'User Role' grid - "Administrators" role
     * 5.Press "Save User" button
     *
     * Expected result: new user successfully saved.
     *                  Message "The user has been saved." is displayed
     */
    public function test_WithRole()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', NULL ,'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create Admin User  (without Admin User Role)
     * Steps:
     * 1.Go to System-Permissions-Users
     * 2.Press "Add New User" button
     * 3.Fill all required fields
     * 4.Do not choose role in the 'User Role' grid
     * 5.Press "Save User" button
     *
     * Expected result: new user successfully saved.
     *                  Message "The user has been saved." is displayed
     */
    public function test_WithoutRole()
    {
        $this->assertTrue(
                $this->navigate('manage_admin_users')->clickButton('add_new_admin_user')->navigated('new_admin_user'),
                'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('generic_admin_user', array(
            'select_by_role_name' => ' '),'email'));
        $this->clickButton('save_attribute');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }
}
