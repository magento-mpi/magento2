<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AdminUser
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

/**
 * Creating Admin User
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_CreateAclTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Post conditions:</p>
     * <p>Log out from Backend.</p>
     */
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Preconditions</p>
     *
     * @param $access
     * @param $page
     * @param $menuElementCount
     * @param $searchFieldCount
     *
     * @test
     * @dataProvider roleResourceAccessDataProvider
     * @TestlinkId TL-MAGE-5586, TL-MAGE-5593, TL-MAGE-5595, TL-MAGE-5598, TL-MAGE-5599
     * @TestlinkId TL-MAGE-5600, TL-MAGE-5601, TL-MAGE-5602, TL-MAGE-5603, TL-MAGE-5604, TL-MAGE-5605
     */
    public function roleResourceAccess($access, $page, $menuElementCount, $searchFieldCount)
    {
        //Preconditions
        //create specific role with test roleResource
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
                                          array('resource_1' => $access));
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
                                             array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //Steps
        //login as admin user with specific(test) role
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage($page);
        //Verifying  count of main menu elements
        $this->assertEquals($menuElementCount, $this->getControlCount('pageelement', 'navigation_menu_items'));
        //Verifying that Global Search fieldset is present or not present
        $this->assertEquals($searchFieldCount, $this->getControlCount('field', 'global_record_search'));
    }

    public function roleResourceAccessDataProvider()
    {
        return array(
            array('Sales', 'manage_sales_orders', 1 ,0),
            array('Customers', 'manage_customers', 1 ,0),
            array('Dashboard', 'dashboard', 1 ,0),
            array('Catalog', 'manage_products', 1 ,0),
            array('Mobile', 'manage_apps', 1 ,0),
            array('Newsletter', 'newsletter_templates', 1 ,0),
            array('CMS', 'manage_cms_pages', 1 ,0),
            array('Reports', 'reports_sales_sales', 1 ,0),
            array('System', 'my_account', 1 ,0),
            array('External Page Cache', 'access_denied', 0 ,0),
            array('Global Search', 'access_denied', 0 ,1),
        );
    }
}