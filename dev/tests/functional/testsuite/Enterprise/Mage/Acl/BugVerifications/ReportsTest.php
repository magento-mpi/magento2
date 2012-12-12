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
class Enterprise_Mage_Acl_BugVerifications_ReportsTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Bug Cover<p/>
     * <p>MAGETWO-2592: The button "Fetch Updates" is presented on PayPal Settlement Reports page
     * for user which doesn't have permission to Fetch actions</p>
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