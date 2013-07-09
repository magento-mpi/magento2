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
class Core_Mage_AdminUser_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Create Admin User.
     *
     * @param Array $userData
     */
    public function createAdminUser($userData)
    {
        $this->clickButton('add_new_admin_user');
        $this->fillForm($userData, 'user_info');
        $first = (isset($userData['first_name'])) ? $userData['first_name'] : '';
        $last = (isset($userData['last_name'])) ? $userData['last_name'] : '';
        $param = $first . ' ' . $last;
        $this->addParameter('elementTitle', $param);
        if (array_key_exists('role_name', $userData)) {
            $this->openTab('user_role');
            $this->searchAndChoose(array('role_name' => $userData['role_name']), 'permissions_user_roles');
        }
        $this->saveForm('save_admin_user');
    }

    /**
     * Login Admin User
     *
     * @param array $loginData
     */
    public function loginAdmin($loginData)
    {
        $waitCondition = array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('pageelement', 'admin_logo'));
        $this->fillForm($loginData);
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }

    /**
     * Forgot Password Admin User
     *
     * @param array $emailData
     */
    public function forgotPassword($emailData)
    {
        $waitCondition = array($this->_getMessageXpath('general_success'), $this->_getMessageXpath('general_error'),
                               $this->_getMessageXpath('general_validation'));
        $this->clickControl('link', 'forgot_password');
        $this->assertTrue($this->checkCurrentPage('forgot_password'));
        $this->fillForm($emailData);
        $this->clickButton('retrieve_password', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
    }

    /**
     * Create New Role.
     *
     * @param array $roleData
     * @param string $separator
     */
    public function createRole(array $roleData, $separator = '/')
    {
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
        $aclResource = (isset($roleAccess['resource_acl'])) ? $roleAccess['resource_acl'] : array();
        $this->fillRoleScopes($roleWebsites);
        $this->fillRoleAccess($roleAccess, $aclResource, $separator);
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
                    $this->addParameter('websiteName', $website);
                    $this->fillCheckbox('websites', 'yes');
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
    public function fillRoleAccess(array $roleAccess, $aclResource, $separator = '/')
    {
        if (!empty($roleAccess)) {
            if (isset($roleAccess['resource_access'])) {
                $this->fillTab(array('resource_access' => $roleAccess['resource_access']), 'role_resources');
                unset($roleAccess['resource_access']);
            }
            if (!empty($roleAccess)) {
                if (isset($roleAccess['resource_acl'])) {
                    $this->clickControl('checkbox', $aclResource, false);
                    unset($roleAccess['resource_acl']);
                } else {
                    foreach ($roleAccess as $category) {
                        $this->categoryHelper()->selectCategory($category, 'role_resources', $separator);
                    }
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
        $cellId = $this->getColumnIdByName('Role');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $cellId);
        $param = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $param);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /*
     * EditRole identify variables
     * @param array $roleData
     */
    protected function _editRoleVars(array $roleData)
    {
        $result = array();
        $result['searchUserRole'] = (isset($roleData['search_role'])) ? $roleData['search_role'] : array();
        $result['roleInfo'] = (isset($roleData['role_info_tab'])) ? $roleData['role_info_tab'] : array();
        $result['roleResources'] = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $result['roleUsers'] = (isset($roleData['role_users_tab'])) ? $roleData['role_users_tab'] : array();
        return $result;
    }

    /**
     * Edit Role
     *
     * @param array $roleData
     * @param string $separator
     */
    public function editRole(array $roleData, $separator = '/')
    {
        $result = $this->_editRoleVars($roleData);
        if (!empty($result['searchUserRole'])) {
            $this->openRole($result['searchUserRole']);
        }
        $this->fillTab($result['roleInfo'], 'role_info');
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