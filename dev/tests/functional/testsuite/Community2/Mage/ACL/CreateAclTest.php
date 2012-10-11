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
class Community2_Mage_ACL_CreateAclTest extends Mage_Selenium_TestCase
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
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>Steps:</p>
     * <p>1. Log in as testAdminUser</p>
     * <p>Expected results:</p>
     * <p>1. For 'Sales' : current page is 'Manage Sales Order'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>2. For 'Customers' : current page is 'Manage Customers'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>3. For 'Dashboard' : current page is 'Dashboard'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>4. For 'Catalog' : current page is 'Manage Products'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>5. For 'Mobile' : current page is 'Manage Apps'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>6. For 'Newsletter' : current page is 'Newsletter templates'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>7. For 'CMS' : current page is 'Manage CMS pages'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>8. For 'Reports' : current page is 'PayPal reports'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>9. For 'System' : current page is 'My Account'; Navigation menu contains only one element; 'Global Search' field is not presented</p>
     * <p>10. For 'External Page Cache' : current page is 'Access Denied'; Navigation menu is not contains any elements; 'Global Search' field is not presented</p>
     * <p>11. For 'Global Search' : current page is 'Access Denied'; Navigation menu is not contains any elements; 'Global Search' field is presented</p>
     *
     * @test
     *
     * @dataProvider roleResourceAccessDataProvider
     * @TestlinkId TL-MAGE-5586, TL-MAGE-5593, TL-MAGE-5595, TL-MAGE-5598, TL-MAGE-5599, TL-MAGE-5600, TL-MAGE-5601, TL-MAGE-5602, TL-MAGE-5603, TL-MAGE-5604, TL-MAGE-5605
     */
    public function roleResourceAccess($access, $page, $menuElementCount, $globalSearchFieldCount)
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
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals($menuElementCount, count($navigationElements));
        //Verifying that Global Search fieldset is present or not present
        $globSearchXpath = $this->_getControlXpath('field', 'global_record_search');
        $globSearchCount = $this->getElementsByXpath($globSearchXpath, 'value');
        $this->assertEquals($globalSearchFieldCount, count($globSearchCount));
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