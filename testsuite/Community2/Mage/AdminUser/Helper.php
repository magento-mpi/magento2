<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
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
     * @param string $separator
     */
    public function createRole(array $roleData, $separator = '/')
    {
        if (empty($roleData)) {
            $this->fail('$roleData parameter is empty');
        }
        $roleInfo = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $this->clickButton('add_new_role');
        $this->fillTab($roleInfo, 'role_info');
        if ($roleResources) {
            $this->fillRolesResources($roleResources, $separator);
        }
        $this->saveForm('save_role');
    }

    /**
     * Fill Roles Resources Tab
     *
     * @param array $roleResources
     * @param string $separator
     */
    public function fillRolesResources(array $roleResources, $separator = '/')
    {
        $roleWebsites = (isset($roleResources['role_scopes'])) ? $roleResources['role_scopes'] : array();
        $roleAccess = (isset($roleResources['role_resources'])) ? $roleResources['role_resources'] : array();

        $this->fillRoleScopes($roleWebsites);
        $this->fillRoleAccess($roleAccess, $separator);
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
     * @param string $separator
     */
    public function fillRoleAccess(array $roleAccess, $separator = '/')
    {
        if (!empty($roleAccess)) {
            if (isset($roleAccess['resource_access'])) {
                $this->fillTab(array('resource_access' => $roleAccess['resource_access']), 'role_resources');
                unset($roleAccess['resource_access']);
            }
            if (!empty($roleAccess)) {
                foreach ($roleAccess as $category) {
                    $this->categoryHelper()->selectCategory($category, 'role_resources', $separator);
                }
            }
        }
    }

    /**
     * @param array $searchRole
     */
    public function openRole(array $searchRole)
    {
        $productSearch = $this->_prepareDataForSearch($searchRole);
        $xpathTR = $this->search($productSearch, 'role_list');
        $this->assertNotNull($xpathTR, 'Role is not found');
        $cellId = $this->getColumnIdByName('Role Name');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /**
     * Edit Role
     *
     * @param array $roleData
     * @param string $separator
     */
    public function editRole(array $roleData, $separator = '/')
    {
        if (empty($roleData)) {
            $this->fail('$roleData parameter is empty');
        }
        $searchUserRole = (isset($roleData['search_role'])) ? $roleData['search_role'] : array();
        $roleInfo = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $roleUsers = (isset($roleData['role_users_tab'])) ? $roleData['role_users_tab'] : array();

        if (!empty($searchUserRole)) {
            $this->openRole($searchUserRole);
        }
        $this->fillTab($roleInfo, 'role_info');
        if (!empty($roleResources)) {
            $this->fillRolesResources($roleResources, $separator);
        }
        if (!empty($roleUsers)) {
            $this->openTab('role_users');
            foreach ($roleUsers as $user) {
                $this->searchAndChoose($user, 'role_users');
            }
        }
        $this->saveForm('save_role');
    }

    /**
     * Deletes role
     *
     * @param array $roleData
     */
    public function deleteRole(array $roleData)
    {
        $searchUserRole = (isset($roleData['search_role'])) ? $roleData['search_role'] : array();
        if (!empty($searchUserRole)) {
            $this->openRole($searchUserRole);
        }
        $this->clickButtonAndConfirm('delete_role', 'confirmation_for_delete');
    }
}
