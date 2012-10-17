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

class Enterprise2_Mage_ACL_SystemConfigurationTest extends Mage_Selenium_TestCase
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
     * <p>Steps</p>
     * <p>1. Role "Role1" with permission to configure only one resource from Configuration menu is created</p>
     * <p>2. Admin user "User1" with "Role1" is created.</p>
     * <p>3. Login to backend using newly created "User1" credentials.</p>
     * <p>4. Navigate to System-Configuration.</p>
     * <p>5. Verify that only one tab is presented.</p>
     * <p>6. Click on presented tab.</p>
     * <p>Expected result</p>
     * <p> Only one tab is presented. This tab equal to resource from ACL tree.</p>
     *
     * @param $resourceCheckbox
     * @param $tabName
     *
     * @dataProvider systemConfigurationOneTabDataProvider
     * @test
     * @TestlinkId TL-MAGE-6016
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
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)),
            'Not only "' . $tabName . '" is presented on page.');
        //verify that this tab equal to resource from ACL tree
        foreach ($tabElement[$tabName] as $fieldset=> $fieldsetName) {
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
            array('Wishlist Section', 'customers_wishlist'),
            array('Persistent Shopping Cart', 'customers_persistent_shopping_cart'),
            array('Invitation Section', 'customer_invitations'),
            array('Gift Registry Section', 'customer_gift_registry'),
            array('Gift Cards', 'sales_gift_card'),
        );
    }

    /**
     * <p>Precondition method</p>
     * <p>Admin user with Role System-Configuration</p>
     * <p>Preconditions:</p>
     * <p>1. Create new admin role with "Role Resources":</p>
     * <p>1.1 Resource Access = Custom</p>
     * <p>1.2 Resource checkboxes = 'System-Configuration'</p>
     * <p>2. Create test Admin user with test Role(Full permissions for Configuration menu)</p>
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
     * <p>Preconditions</p>
     * <p>1. Role "Role1" with role resource "System-Configuration: and role scope "Main Website"  is created.</p>
     * <p>2. Admin user  "User1" with "Role1" is created.</p>
     * <p>Steps</p>
     * <p>1. Login to backend using newly created "User1" credentials.</p>
     * <p>2. Navigate to System-Configuration.</p>
     * <p>3. Click to all tab(one after the other) in configuration menu.(On the left column).</p>
     * <p>Expected result</p>
     * <p>All configuration pages(one after other) are successfully opened.</p>
     *
     * @depends createAdminWithTestRole
     *
     * @param $testData
     *
     * @test
     * @TestlinkId TL-MAGE-6006
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

