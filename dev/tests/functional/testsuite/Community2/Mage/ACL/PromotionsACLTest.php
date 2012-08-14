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
 */

/**
 * Check Promotion Rights
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Community2_Mage_ACL_PromotionsACLTest extends Mage_Selenium_TestCase
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
     * <p>Check Promotions Full rights</p>
     * <p>Preconditions:</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select all Permissions checkboxes</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System-Permissions-Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with full rights to Promotions Menu</p>
     * <p>Expected Results:</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has Rights to create Catalog Price Rule</p>
     * <p>Admin User has Rights to create Shopping Cart Price Rule</p>
     *
     * @TestlinkId TL-MAGE-6021
     * @test
     */
    public function checkPromotionsFullRights()
    {
        //Preconditions
        //create specific role with full rights to Promotions Menu
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'role_promotions_full_rights');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with full rights to Promotions Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotions Menu
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotions menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Check Promotions Catalog Only Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only Catalog Price Rules Checkbox</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with full rights to Promotions Menu</p>
     * <p>Expected Results:</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has Rights to create Catalog Price Rule</p>
     * <p>Admin User has Rights to create Shopping Cart Price Rule</p>
     *
     * @TestlinkId TL-MAGE-1467
     * @test
     */
    public function checkPromotionsCatalogOnlyRights()
    {
        //Preconditions
        //create specific role with only to Catalog Promotions Menu rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'role_promotions_catalog_rights');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotion Menu
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_shopping_cart_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }

    /**
     * <p>Check Promotions Shopping Cart Only Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System>Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p> Log out </p>
     * <p>Steps:<p/>
     * <p>1.Log In as admin user with  rights only to Shopping Cart Price Rules</p>
     * <p>Expected Results</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has rights to create Shopping Cart Price Rule</p>
     * <p>Admin User has NO rights to create Catalog Price Rule</p>
     *
     * @TestlinkId TL-MAGE-1475
     * @test
     */
    public function checkPromotionsShoppingCartOnlyRights()
    {
        //Preconditions
        //create specific role with full rights to Promotion Menu
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'role_promotions_shopping_cart_rights');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with  rights to Shopping Cart Price Rule
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }
}

