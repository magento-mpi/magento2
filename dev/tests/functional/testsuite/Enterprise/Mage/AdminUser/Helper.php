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
class Enterprise_Mage_AdminUser_Helper extends Core_Mage_AdminUser_Helper
{
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
