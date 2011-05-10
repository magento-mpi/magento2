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
class AdminUser_DeleteTest extends Mage_Selenium_TestCase {

    /**
     * Preconditions:
     *
     * Log in to Backend.
     *
     * Navigate to System -> Permissions -> Users.
     */
    protected function assertPreConditions()
    {
        $this->addParameter('id', 0);
        $this->loginAdminUser();
        $this->assertTrue($this->admin());
        $this->navigate('manage_admin_users');
        $this->assertTrue($this->checkCurrentPage('manage_admin_users'),
                'Wrong page is opened');
    }

    /**
     * Create Admin User (all required fields are filled).
     *
     * Steps:
     * 1.Press "Add New User" button.
     * 2.Fill all required fields.
     * 3.Press "Save User" button.
     * 4.Search and open user.
     * 5.Press "Delete User" button.
     *
     * Expected result:
     * User successfully deleted.
     * Message "The user has been deleted." is displayed.
     */
    public function test_DeleteAdminUser_Deletable()
    {
        //Data
        $userData = $this->loadData('generic_admin_user',
                        array(
                            'email' => $this->generate('email', 20, 'valid'),
                            'user_name' => $this->generate('string', 7, ':alnum:')
                ));
        $searchData = $this->loadData('search_admin_user',
                        array(
                            'email' => $userData['email'],
                            'user_name' => $userData['user_name']
                ));
        //Steps
        $this->clickButton('add_new_admin_user');
        $this->fillForm($userData);
        $this->saveForm('save_admin_user');
        //Verifying
        $this->defineId();
        $this->assertTrue($this->successMessage('success_saved_user'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('edit_admin_user'),
                'After successful user creation should be redirected to Edit User page');
        //Steps
        $this->navigate('manage_admin_users');
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchData), 'Admin User is not found');
        $this->deleteElement('delete_user', 'confirmation_for_delete');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_user'), $this->messages);
    }

    /**
     * Delete logged in as Admin User
     */
    public function test_DeleteAdminUser_Current()
    {
        //Data
        $searchData = $this->loadData('search_admin_user');
        $searchDataCurrentUser = array();
        //Steps
        $this->navigate('my_account');
        $this->assertTrue($this->checkCurrentPage('my_account'),
                'Wrong page is opened');
        $page = $this->getCurrentLocationUimapPage();
        $fieldSet = $page->findFieldset('account_info');
        foreach ($searchData as $key => $value) {
            if ($fieldSet->findField($key)) {
                $v = $this->getValue('//' . $fieldSet->findField($key));
                $searchDataCurrentUser[$key] = $v;
            } else {
                $searchDataCurrentUser[$key] = $value;
            }
        }
        $this->navigate('manage_admin_users');
        $this->clickButton('reset_filter', FALSE);
        $this->pleaseWait();
        $this->assertTrue($this->searchAndOpen($searchDataCurrentUser), 'Admin User is not found');
        //Verifying
        $this->assertFalse($this->controlIsPresent('button', 'success_deleted_user'),
                '"Delete Admin User" button is present on the page');
    }

    /**
     * *********************************************
     * *         HELPER FUNCTIONS                  *
     * *********************************************
     */
    public function defineId()
    {
        // ID definition
        $item_id = 0;
        $title_arr = explode('/', $this->getLocation());
        $title_arr = array_reverse($title_arr);
        foreach ($title_arr as $key => $value) {
            if (preg_match('/id$/', $value) && isset($title_arr[$key - 1])) {
                $item_id = $title_arr[$key - 1];
                break;
            }
        }
        if ($item_id > 0) {
            $this->addParameter('id', $item_id);
        }
    }

}
