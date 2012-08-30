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
class Enterprise2_Mage_ACL_BugVerifications_ReportsTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Bug Cover<p/>
     * <p>MAGETWO-2592: The button "Fetch Updates" is presented on PayPal Settlement Reports page for user which doesn't have permission to Fetch actions</p>
     *
     * <p>Steps:</p>
     * <p>1. Create test role:</p>
     * <p>On Role Resource tab:</p>
     * <p>"Resource Access" drop-down = "Custom"</p>
     * <p>"Resources" = Reports/Sales/PayPal Settlement Reports/View</p>
     * <p>2. Create "User1" with test role.(System-Permissions-Users)</p>
     * <p>3. Log in to backend using newly created "User1" credentials.</p>
     * <p>4. Navigate to Reports-Sales-PayPal Settlement Reports.</p>
     * <p>Expected results:</p>
     * <p>The button "Fetch updates" is not presented on PayPal Settlement Reports page.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6074
     */
    public function settlementReportButtons()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Reports/Sales/PayPal Settlement Reports/View'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->navigate('paypal_reports');
        //verify that button "Fetch Updates" is not presented on PayPal Settlement Reports page.
        if ($this->buttonIsPresent('fetch_updates')) {
            $this->fail('This user does not have permission to view "Fetch Update" button');
        }
    }
}