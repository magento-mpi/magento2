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
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
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
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        //create user with specific role to verifying ACL permission
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'System/Configuration/' . $resourceCheckbox));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testData = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testData);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testData);
        $this->navigate('system_configuration');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_default');
        $xpath = $this->_getControlXpath('tab', 'all_tabs');
        //verify that only one tab is presented on page
        $this->assertEquals(1, count($this->getElements($xpath)),
            'Not only "' . $tabName . '" is presented on page.');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement[$tabName] as $fieldset => $fieldsetName) {
            if (!$this->controlIsPresent('fieldset', $fieldset)) {
                $this->addVerificationMessage(
                    'The fieldset "' . $fieldset . '" does not present on tab "' . $tabName . '"');
            }
        }
        $this->assertEmptyVerificationErrors();
    }

    public function systemConfigurationOneTabDataProvider()
    {
        return array(
            array('Invitation Section', 'customer_invitations'),
            array('Gift Registry Section', 'customer_gift_registry'),
            array('Gift Cards', 'sales_gift_card'),
            array('Promo Section','customers_promotions'),
            array('Reward Points','customer_reward_points'),
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
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_1' => 'System/Configuration'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $testData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        return $testData;
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
        $this->admin('log_in_to_admin', false);
        $waitCondition = array($this->_getMessageXpath('general_error'), $this->_getMessageXpath('general_validation'),
                               $this->_getControlXpath('pageelement', 'admin_logo'));
        $this->fillFieldset($testData, 'log_in');
        $this->clickButton('login', false);
        $this->waitForElement($waitCondition);
        $this->navigate('system_configuration_website_base');
        $tabElement = $this->loadDataSet('SystemConfigurationMenu', 'configuration_menu_website');
        //verifying that all necessary tabs and fieldsets are present
        foreach ($tabElement as $tab => $tabName) {
            $this->systemConfigurationHelper()->openConfigurationTab($tab);
            foreach ($tabName as $fieldset => $fieldsetName) {
                if (!$this->controlIsPresent('fieldset', $fieldset)) {
                    $this->addVerificationMessage(
                        'The fieldset "' . $fieldset . '" does not present on tab "' . $tab . '"');
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}

