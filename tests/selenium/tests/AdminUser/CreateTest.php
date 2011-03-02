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
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AdminUser_CreateTest extends Mage_Selenium_TestCase
{

    /**
     * @TODO
     */
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
     * @TODO
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
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyUserName()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('user_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyFirstName()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('first_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyLastName()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('last_name' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyEmail()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('email' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyPassword()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('password' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
    }

    /**
     * @TODO
     */
    public function test_WithRequiredFieldsEmpty_EmptyPasswordConfirmation()
    {
        $this->assertTrue(
            $this->navigate('manage_users')->clickButton('add_new_user')->navigated('new_user'),
            'Wrong page is displayed'
        );
        $this->fillForm($this->loadData('new_user_create', array('password_confirmation' => '') , null));
        $this->clickButton('save_user');
        $this->assertFalse($this->errorMessage(), $this->messages);
        $this->assertTrue($this->successMessage(), 'No success message is displayed');
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
