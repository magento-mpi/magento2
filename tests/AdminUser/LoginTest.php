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
class AdminUser_LoginTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Login Admin Page</p>
     */
    protected function assertPreConditions()
    {
        $this->setArea('admin');
        $this->navigate('log_in_to_admin');
        $this->assertTrue($this->checkCurrentPage('log_in_to_admin'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }

    /**
     * <p>Steps</p>
     * <p>1. Leave one field empty;</p>
     * <p>2. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This is a required field"</p>
     * 
     * @dataProvider data_EmptyLoginUser
     */
    public function test_loginEmptyOneField($emptyField)
    {
        //Data
        $loginData = $this->loadData('login_data', array($emptyField => '%noValue%'));
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->validationMessage('empty_' . $emptyField), $this->messages);
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function data_EmptyLoginUser()
    {
        return array(
            array('user_name'),
            array('password')
        );
    }

    /**
     * <p>Steps</p>
     * <p>1.Fill in fields with incorrect data;</p>
     * <p>2. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "Invalid username or password."</p>
     * 
     */
    public function test_loginNotExistUser()
    {
        //Data
        $loginData = $this->loadData('login_data');
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->errorMessage('wrong_credentials'), $this->messages);
    }

    /**
     * <p>Steps</p>
     * <p>1.Fill "Username" field with correct data and "Password" with incorrect data;</p>
     * <p>2. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "Invalid username or password."</p>
     * 
     */
    public function test_loginIncorrectPassword()
    {
        //Data
        $loginData = $this->loadData('login_data',
                        array('password' => $this->generate('string', 9, ':punct:')));
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->errorMessage('wrong_credentials'), $this->messages);
    }

    /**
     * <p>Steps</p>
     * <p>Pre-Conditions:</p>
     * <p>Inactive Admin User is created</p>
     * <p>1.Fill in "Username" and "Password" fields with correct data;</p>
     * <p>2. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This account is inactive."</p>
     * 
     */
    public function test_loginInactiveUserAdminAccount()
    {
        //Data
        $userData = $this->loadData('generic_admin_user', array('this_acount_is' => 'Inactive'),
                        array('email', 'user_name'));
        $loginData = array('user_name' => $userData['user_name'],
            'password' => $userData['password']);
        //Pre-Conditions
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->errorMessage('inactive_account'), $this->messages);
    }

    /**
     * <p>Steps</p>
     * <p>Pre-Conditions:</p>
     * <p>Inactive Admin User is created</p>
     * <p>1.Fill in "Username" and "Password" fields with correct data;</p>
     * <p>2. Click "Login" button;</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "This account is inactive."</p>
     * 
     */
    public function test_loginWithoutPermissions()
    {
        //Data
        $userData = $this->loadData('generic_admin_user', NULL, array('email', 'user_name'));
        $loginData = array('user_name' => $userData['user_name'],
            'password' => $userData['password']);
        //Pre-Conditions
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($userData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        //Steps
        $this->logoutAdminUser();
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verifying
        $this->assertTrue($this->errorMessage('access_denied'));
    }

}
