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
        if (array_key_exists('role_name', $userData)) {
            $this->openTab('user_role');
            $this->searchAndChoose(array('role_name' => $userData['role_name']), 'permissions_user_roles');
        }
        $this->saveForm('save_admin_user');
    }

    /**
     * @param array $searchData
     */
    public function openAdminUser(array $searchData)
    {
        //Search Admin User
        $searchData = $this->_prepareDataForSearch($searchData);
        $userLocator = $this->search($searchData, 'permissionsUserGrid');
        $this->assertNotNull($userLocator, 'Admin User is not found with data: ' . print_r($searchData, true));
        $userRowElement = $this->getElement($userLocator);
        $userUrl = $userRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId1 = $this->getColumnIdByName('First Name');
        $cellId2 = $this->getColumnIdByName('Last Name');
        $cellElement1 = trim($this->getChildElement($userRowElement, 'td[' . $cellId1 . ']')->text());
        $cellElement2 = trim($this->getChildElement($userRowElement, 'td[' . $cellId2 . ']')->text());
        $this->addParameter('elementTitle', $cellElement1 . ' ' . $cellElement2);
        $this->addParameter('id', $this->defineIdFromUrl($userUrl));
        //Open Admin User
        $this->url($userUrl);
        $this->validatePage('edit_admin_user');
    }

    /**
     * Login Admin User
     *
     * @param array $loginData
     */
    public function loginAdmin($loginData)
    {
        $waitCondition = array(
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation'),
            $this->_getControlXpath('pageelement', 'admin_logo')
        );
        $this->fillFieldset($loginData, 'log_in');
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->validatePage();
        if ($this->controlIsVisible('button', 'close_notification')) {
            $this->clickControl('button', 'close_notification', false);
        }
    }

    /**
     * Go to My Account link from Account Settings area
     */
    public function goToMyAccount()
    {
        $adminUserLocator = $this->_getControlXpath('link', 'account_avatar');
        $availableElement = $this->elementIsPresent($adminUserLocator);
        if ($availableElement) {
            $this->focusOnElement($availableElement);
            $this->clickControl('link', 'account_avatar', false);
            $this->clickControl('link', 'account_settings');
        }
        $this->assertTrue($this->checkCurrentPage('my_account'), $this->getParsedMessages('verification'));
    }

    /**
     * Forgot Password Admin User
     *
     * @param array $emailData
     */
    public function forgotPassword($emailData)
    {
        $waitCondition = array(
            $this->_getMessageXpath('general_success'),
            $this->_getMessageXpath('general_error'),
            $this->_getMessageXpath('general_validation')
        );
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
     */
    public function createRole(array $roleData)
    {
        $roleName = (isset($roleData['role_info_tab']['role_name'])) ? $roleData['role_info_tab']['role_name'] : '';
        $roleResources = (isset($roleData['role_resources_tab'])) ? $roleData['role_resources_tab'] : array();
        $this->clickButton('add_new_role');
        $this->fillField('role_name', $roleName);
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
        $this->openTab('role_resources');
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
        if (isset($roleWebsites['scopes'])) {
            $this->fillDropdown('role_scopes', $roleWebsites['scopes']);
            unset($roleWebsites['scopes']);
        }
        foreach ($roleWebsites as $website) {
            $this->addParameter('websiteName', $website);
            $this->fillCheckbox('websites', 'yes');
        }
    }

    /**
     * Fill Roles Access on Roles Resources Tab
     *
     * @param array $roleAccess
     */
    public function fillRoleAccess(array $roleAccess)
    {
        if (isset($roleAccess['resource_access'])) {
            $this->fillDropdown('resource_access', $roleAccess['resource_access']);
        }
        if (isset($roleAccess['resource_acl'])) {
            $access = is_string($roleAccess['resource_acl'])
                ? explode(',', $roleAccess['resource_acl'])
                : $roleAccess['resource_acl'];
            foreach ($access as $path) {
                $this->fillCheckbox(trim($path), 'Yes');
            }
        }
        if (isset($roleAccess['resource_acl_skip'])) {
            $access = is_string($roleAccess['resource_acl_skip'])
                ? explode(',', $roleAccess['resource_acl_skip'])
                : $roleAccess['resource_acl_skip'];
            foreach ($access as $path) {
                $this->fillCheckbox(trim($path), 'No');
            }
        }
    }

    /**
     * @param array $searchData
     */
    public function openRole(array $searchData)
    {
        //Search Role
        $searchData = $this->_prepareDataForSearch($searchData);
        $roleLocator = $this->search($searchData, 'role_list');
        $this->assertNotNull($roleLocator, 'Role is not found with data: ' . print_r($searchData, true));
        $roleRowElement = $this->getElement($roleLocator);
        $roleUrl = $roleRowElement->attribute('title');
        //Define and add parameters for new page
        $cellId = $this->getColumnIdByName('Name');
        $cellElement = $this->getChildElement($roleRowElement, 'td[' . $cellId . ']');
        $this->addParameter('elementTitle', trim($cellElement->text()));
        $this->addParameter('id', $this->defineIdFromUrl($roleUrl));
        //Open Role
        $this->url($roleUrl);
        $this->validatePage('admin_edit_role');
    }

    /**
     * Edit Role
     *
     * @param array $roleData
     */
    public function editRole(array $roleData)
    {
        if (isset($roleData['search_role'])) {
            $this->openRole($roleData['search_role']);
        }
        if (isset($roleData['role_info_tab'])) {
            $this->fillTab($roleData['role_info_tab'], 'role_info');
        }
        if (isset($roleData['role_resources_tab'])) {
            $this->fillRolesResources($roleData['role_resources_tab']);
        }
        if (isset($roleData['role_users_tab'])) {
            $this->openTab('role_users');
            foreach ($roleData['role_users_tab'] as $user) {
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
        if (isset($roleData['search_role'])) {
            $this->openRole($roleData['search_role']);
        }
        $this->clickButtonAndConfirm('delete_role', 'confirmation_for_delete');
    }
}