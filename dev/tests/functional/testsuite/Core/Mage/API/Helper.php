<?php
/**
 * Helper class
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Core_Mage_Api_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * <p>Create new SOAP API Role</p>
     *
     * @param array $roleData
     */
    public function createApiSoapRole($roleData)
    {
        $this->navigate('api_soap_roles_management');
        $this->clickButton('add_new_role', true);
        $this->fillField('role_name', $roleData['role_name']);
        $this->openTab('resources');
        $this->fillDropdown('role_access', 'All');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_role');
    }

    /**
     * <p>Create new SOAP API User</p>
     *
     * @param array $userData
     */
    public function createApiSoapUser($userData)
    {
        $this->navigate('api_soap_users');
        $this->clickButton('add_new_api_user', true);
        $this->fillFieldset($userData, 'api_new_user_grid');
    }

    /**
     * <p>Set SOAP API Role</p>
     *
     * @param array $roleData
     * @param array $userData
     * @return array
     */
    public function setApiSoapRole($roleData, $userData)
    {
        $this->openTab('user_role');
        $this->fillField('role_name', $roleData['role_name']);
        $this->clickButton('search', false);
        $this->waitForAjax();
        $this->addParameter('roleName', $roleData['role_name']);
        $this->clickControl('radiobutton', 'select_role', false);
        $elementTitle = $userData['api_user_first_name'] . ' ' . $userData['api_user_last_name'];
        $this->addParameter('elementTitle', $elementTitle);
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_user_saved');
    }
}