<?php

/**
 * Admin_User_Site model
 *
 * @author Magento Inc.
 */
class Model_Admin_AdminUser extends Model_Admin {

    /**
     * Loading configuration data for the testCase
     */
    public function loadConfigData()
    {
        parent::loadConfigData();
        $this->userData = array();
    }

    /**
     * Add user to the system from admin
     *
     * @params array $params
     * @return boolean
     */
    public function doCreateAdminUser($params)
    {
        $this->navigate('System/Permissions/Users');

        $this->setUiNamespace('/admin/pages/system/permissions/users/manage_users');
        $this->clickAndWait($this->getUiElement('buttons/add_new_user'));
        $this->setUiNamespace('/admin/pages/system/permissions/users/manage_users/edit_user');
        $this->click($this->getUiElement('tabs/user_info'));
        $this->checkAndFillField($params, 'user_name', NULL);
        $this->checkAndFillField($params, 'user_first_name', NULL);
        $this->checkAndFillField($params, 'user_last_name', NULL);
        $this->checkAndFillField($params, 'user_email', NULL);
        $this->checkAndFillField($params, 'user_password', NULL);
        $this->checkAndFillField($params, 'user_confirmation', NULL);
        $this->checkAndSelectField($params, 'user_active');
        $this->click($this->getUiElement('tabs/user_role'));
        if ($this->isSetValue($params, 'search_admin_role_name') != NULL) {
            $searchWord = '/search_admin_role_/';
            $searchElements = $this->dataPreparation($params, $searchWord);
            $roleSelect = $this->searchAndDoAction('user_role_container', $searchElements, 'mark', NULL);
            if (!$roleSelect) {
                $this->printInfo('Admin user role not selected.');
            }
        }
        $this->saveAndVerifyForErrors();
    }

    /**
     * Delete user from admin
     *
     * @param array $params
     * @return boolean
     */
    public function doDeleteAdminUser($params)
    {
        $this->navigate('System/Permissions/Users');
        $this->setUiNamespace('/admin/pages/system/permissions/users/manage_users');
        $searchWord = '/search_admin_user_/';
        $searchElements = $this->dataPreparation($params, $searchWord);
        $userSearch = $this->searchAndDoAction('user_table_container', $searchElements, 'open', NULL);
        if ($userSearch) {
            $this->doDeleteElement();
        }
    }

}
