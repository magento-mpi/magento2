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
class Community2_Mage_AdminUser_Helper extends Core_Mage_AdminUser_Helper
{
    /**
     * Create New Role.
     *
     * @param array $roleData
     */
    public function createRole(array $roleData)
    {
        if (empty($roleData)) {
            $this->fail('$roleData parameter is empty');
        }
        $roleInfo = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $this->clickButton('add_new_role');
        $this->fillTab($roleInfo, 'role_info');
        if ($roleResources) {
            $this->fillRolesResources($roleResources);
        }
        $this->saveForm('save_role');
    }

    /**
     * Fill Roles Resources Tab
     *
     * @param array $roleResources
     */
    public function fillRolesResources(array $roleResources)
    {
        $roleWebsites = (isset($roleResources['role_scopes'])) ? $roleResources['role_scopes'] : array();
        $roleAccess = (isset($roleResources['role_resources'])) ? $roleResources['role_resources'] : array();

        $this->fillRoleScopes($roleWebsites);
        $this->fillRoleAccess($roleAccess);
    }

    /**
     * Fill Roles Scopes Fieldset on Roles Resources Tab
     *
     * @param array $roleWebsites
     */
    public function fillRoleScopes(array $roleWebsites)
    {
        if (!empty($roleWebsites)) {
            if (isset($roleWebsites['scopes'])) {
                $this->fillTab(array('role_scopes' => $roleWebsites['scopes']), 'role_resources');
                unset($roleWebsites['scopes']);
            }
            if (!empty($roleWebsites)) {
                foreach ($roleWebsites as $website) {
                    $this->productHelper()->selectWebsite($website);
                }
            }
        }
    }

    /**
     * Fill Roles Access on Roles Resources Tab
     *
     * @param array $roleAccess
     */
    public function fillRoleAccess(array $roleAccess)
    {
        if (!empty($roleAccess)) {
            if (isset($roleAccess['resource_access'])) {
                $this->fillTab(array('resource_access' => $roleAccess['resource_access']), 'role_resources');
                unset($roleAccess['resource_access']);
            }
            if (!empty($roleAccess)) {
                foreach ($roleAccess as $category) {
                    $this->categoryHelper()->selectCategory($category, 'role_resources');
                }
            }
        }
    }

    /**
     * Edit Role
     *
     * @param array $roleData
     */
    public function editRole(array $roleData)
    {
        if (empty($roleData)) {
            $this->fail('$roleData parameter is empty');
        }
        $searchUserRole = (isset($roleData['search_role'])) ? $roleData['search_role'] : array();
        $roleInfo = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $roleUsers = (isset($roleData['role_users_tab'])) ? $roleData['role_users_tab'] : array();

        if (!empty($searchUserRole)) {
            $this->searchAndOpen($searchUserRole);
            $this->addParameter('id', $this->defineIdFromUrl());
        }
        $this->fillTab($roleInfo, 'role_info');
        if (!empty($roleResources)) {
            $this->fillRolesResources($roleResources);
        }
        if (!empty($roleUsers)) {
            $this->openTab('role_users');
            foreach ($roleUsers as $user) {

                $this->searchAndChoose($user, 'role_users');
            }
        }
        $this->saveForm('save_role');
    }
}