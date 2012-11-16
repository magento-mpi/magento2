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
 * @method Community2_Mage_AdminUser_Helper helper(string $className)
 */
class Enterprise_Mage_AdminUser_Helper extends Core_Mage_AdminUser_Helper
{
    /**
     * Create New Role.
     *
     * @param array $roleData
     * @param string $separator
     */
    public function createRole(array $roleData, $separator = '/')
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->createRole($roleData, $separator);
    }

    /**
     * Fill Roles Resources Tab
     *
     * @param array $roleResources
     * @param string $separator
     */
    public function fillRolesResources(array $roleResources, $separator = '/')
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->fillRolesResources($roleResources, $separator);
    }

    /**
     * Fill Roles Scopes Fieldset on Roles Resources Tab
     *
     * @param array $roleWebsites
     */
    public function fillRoleScopes(array $roleWebsites)
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->fillRolesResources($roleWebsites);
    }

    /**
     * Fill Roles Access on Roles Resources Tab
     *
     * @param array $roleAccess
     * @param string $separator
     */
    public function fillRoleAccess(array $roleAccess, $separator = '/')
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->fillRoleAccess($roleAccess, $separator);
    }

    /**
     * Edit Role
     *
     * @param array $roleData
     * @param string $separator
     */
    public function editRole(array $roleData, $separator = '/')
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->editRole($roleData, $separator);
    }

    /**
     * Deletes role
     *
     * @param array $roleData
     */
    public function deleteRole(array $roleData)
    {
        $this->helper('Community2/Mage/AdminUser/Helper')->deleteRole($roleData);
    }

    /**
     *
     * Create New Restricted Role.
     *
     * @param array $roleData
     * @param string $separator
     */
    public function createRestrictedRole(array $roleData, $separator = '/')
    {
        if (empty($roleData)) {
            $this->fail('$roleData parameter is empty');
        }
        $roleInfo = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $this->clickButton('add_new_role');
        $this->fillTab($roleInfo, 'role_info');
        if ($roleResources) {
            $this->fillRestrictedRolesResources($roleResources, $separator);
        }
        $this->saveForm('save_role');
    }

    /**
     *
     * Fill Restricted Roles Resources Tab
     *
     * @param array $roleResources
     * @param string $separator
     */
    public function fillRestrictedRolesResources(array $roleResources, $separator = '/')
    {
        $roleWebsites = (isset($roleResources['role_scopes'])) ? $roleResources['role_scopes'] : array();
        $roleAccess = (isset($roleResources['role_resources'])) ? $roleResources['role_resources'] : array();

        $this->fillRoleScopes($roleWebsites);
        $this->fillAndClearRoleAccess($roleAccess, $separator);
    }

    /**
     * Check current Role Access checkbox on Roles Resources Tab and Clear it to provide restrictions
     *
     * @param array $roleAccess
     * @param string $separator
     *
     */
    public function fillAndClearRoleAccess(array $roleAccess, $separator = '/')
    {
        if (!empty($roleAccess)) {
            if (isset($roleAccess['resource_access'])) {
                $this->fillTab(array('resource_access' => $roleAccess['resource_access']), 'role_resources');
                unset($roleAccess['resource_access']);
            }
            if (!empty($roleAccess)) {
                foreach ($roleAccess as $category) {
                    /* Fill Checkbox*/
                    $this->categoryHelper()->selectCategory($category, 'role_resources', $separator);
                    /*Clear checkbox that restrict Role Scope*/
                    $this->categoryHelper()->selectCategory($category, 'role_resources', $separator);
                }
            }
        }
    }
}
