<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ACL
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Enterprise_Mage_Acl_SystemConfigurationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->admin('log_in_to_admin');
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Access available for user with permission to configure only one resource from Configuration menu</p>
     *
     * @param $resourceCheckbox
     * @param $tabName
     *
     * @dataProvider systemConfigurationOneTabDataProvider
     * @test
     * @TestlinkId TL-MAGE-6016
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationOneTab($resourceCheckbox, $tabName)
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => $resourceCheckbox));
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        $loginData = array('user_name' => $testData['user_name'], 'password' => $testData['password']);
        //create user with specific role to verifying ACL permission
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();

        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('system_configuration');
        //verify that only one tab is presented on page
        $this->assertEquals(1, $this->getControlCount('tab', 'all_tabs'),
            'Not only "' . $tabName . '" is presented on page.');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement[$tabName] as $fieldset => $fieldsetName) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage('The fieldset "' . $fieldset . '" does not present on tab "'
                    . $tabName . '"');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function systemConfigurationOneTabDataProvider()
    {
        return array(
            array('stores-settings-configuration-invitation_section', 'customer_invitations'),
            array('stores-settings-configuration-gift_registry_section', 'customer_gift_registry'),
            array('stores-settings-configuration-gift_cards', 'sales_gift_card'),
            array('stores-settings-configuration-reward_points', 'customer_reward_points'),
        );
    }

    /**
     * <p>Precondition method</p>
     * <p>Admin user with Role System-Configuration</p>
     *
     * @test
     * @return array
     */
    public function createAdminWithTestRole()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'stores-settings-configuration'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
    }

    /**
     * <p> Access available for user with Permission System-Configuration and scope for one website </p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6006
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function systemConfigurationForWebsite($testData)
    {
        if ($this->getBrowser() == 'chrome') {
            $this->markTestIncomplete('MAGETWO-11392');
        }
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_website');
        //Steps
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration_website_base');
        //verifying that all necessary tabs and fieldsets are present
        foreach ($tabElement as $tab => $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
            foreach ($tabName as $fieldset => $fieldsetName) {
                if (!$this->controlIsPresent('fieldset', $fieldset)) {
                    $this->addVerificationMessage('The fieldset "' . $fieldset . '" does not present on tab "'
                        . $tab . '"');
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}

