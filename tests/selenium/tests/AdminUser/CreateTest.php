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
        $this->assertTrue($this->navigate('manage_users'));
        $this->assertTrue($this->clickButton('add_new_user'), 'There is no "Add New User Button" button on the page');
        $this->assertTrue($this->navigated('new_user'), 'Wrong page is displayed');
        $this->assertTrue($this->navigate('new_user'), 'Wrong page is displayed when accessing direct URL');
    }

    /**
     * Create User(all required fields are filled)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', null, null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "User Name" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('user_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "First Name" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('first_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "Last Name" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('last_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "Email" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('email' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "Password" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('password' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * Create User (Empty "Password Confirmation" field)
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
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('password_confirmation' => '') , null));
        $this->clickButton('save_user');
        $this->assertTrue($this->errorMessage(), $this->messages);
        $this->assertFalse($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithSpecialCharacters()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithLongValues()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValues_InvalidEmail()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValues_NumericPassword()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValues_AlphabeticPassword()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValues_ShortPassword()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithInvalidValues_PasswordsNotMatch()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_InactiveUser()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithRole()
    {
        // @TODO
    }

    /**
     * @TODO
     */
    public function test_WithoutRole()
    {
        // @TODO
    }
}
