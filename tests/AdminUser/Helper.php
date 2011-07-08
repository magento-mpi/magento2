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
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AdminUser_Helper extends Mage_Selenium_TestCase
{

    /**
     * Define Admin User Id.
     *
     * Preconditions:
     * User is opened.
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

    /**
     * Search Role for Admin User.
     *
     * @param Array $data
     * @return type
     */
    public function searchRole($data)
    {
        if (isset($data['role_name'])) {
            //Data
            $search['role_name'] = $data['role_name'];
            //Steps
            $this->clickButton('reset_filter', FALSE);
            $this->pleaseWait();
            $this->fillForm($search);
            $this->clickButton('search', FALSE);
            $this->pleaseWait();
            $this->addParameter('roleName', $search['role_name']);
            $page = $this->getCurrentUimapPage();
            $page->assignParams($this->_paramsHelper);
            $fieldsSet = $page->findFieldset('permissions_user_roles');
            $xpathField = $fieldsSet->findRadiobutton('select_by_role_name');
            if ($this->isElementPresent($xpathField)) {
                $this->click($xpathField);
                return TRUE;
            }
        }
        return false;
    }

    /**
     * Create Admin User.
     * @param Array $userData
     */
    public function createAdminUser($userData)
    {
        $this->clickButton('add_new_admin_user');
        $this->fillForm($userData, 'user_info');
        if (array_key_exists('role_name', $userData)
                and $userData['role_name'] !== '%noValue%'
                and $userData['role_name'] !== NULL) {
            $role['role_name'] = $userData['role_name'];
            $this->clickControl('tab', 'user_role', FALSE);
            $this->assertTrue($this->searchRole($role), 'Role is not found');
        }
        $this->saveForm('save_admin_user');
        if ($this->checkCurrentPage('edit_admin_user')) {
            $this->defineId();
        }
    }

    /**
     * Login Admin User
     * @param type $loginData
     */
    public function loginAdmin($loginData)
    {
        $this->fillForm($loginData);
        $this->clickButton('login', false);
        $this->waitForElement(array(self::xpathAdminLogo,
                                    self::xpathErrorMessage,
                                    self::xpathValidationMessage));
    }

    /**
     * Forgot Password Admin User
     * @param type $emailData
     */
    public function forgotPassword($emailData)
    {
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->checkCurrentPage('forgot_password'));
        $this->fillForm($emailData);
        $this->clickButton('retrieve_password', false);
        $this->waitForElement(array(self::xpathSuccessMessage,
                                    self::xpathErrorMessage,
                                    self::xpathValidationMessage));
    }

}