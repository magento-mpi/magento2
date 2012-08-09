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
class Enterprise2_Mage_ACL_PromotionsAClTest extends Mage_Selenium_TestCase
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
     * <p>Check Promotions Full Rights - Catalog Price Rules, Shopping Cart Price Rules and Automated Email Reminder Rules</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select all Permissions checkboxes</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1. Log In as admin user with full rights to Promotions Menu</p>
     * <p>Expected Results:</p>
     * <p>Only Promotions menu is available</p>
     * <p>User has rights to create Catalog Price Rule</p>
     * <p>User has rights to create Shopping Cart Price Rule</p>
     * <p>User has rights to create Automated Email Reminder Rules</p>
     *
     * @TestlinkId TL-MAGE-6021
     * @test
     */
    public function checkPromotionsFullRights()
    {
        //Preconditions
        //create specific role with full rights to Promotions Menu
        $this->navigate('manage_roles');
        $roleSource =
            $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom', array('resource_1' => 'Promotions'));
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
        $this->priceRulesHelper()->createAndApplyRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_applied_rule');
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify rights to create Automated Email Reminder Rules
        $this->navigate('manage_automated_email_reminder_rules');
        $this->priceRulesHelper()->createEmailReminderRule();
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Check Promotions only Catalog Price Rules Read Rights</p>
     * <p>Preconditions</p>
     * <p> Login to backend as admin</p>
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
     * <p>Admin user has Read access rights to  Catalog Price Rule</p>
     * <p>Admin user has NO rights to create Shopping Cart Price Rule</p>
     * <p>Admin user has NO rights to create  Automated Reminder Rule</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1463
     */
    public function checkPromotionsCatalogPriceRulesReadRights()
    {
        //Preconditions
        //create specific role with only to Catalog Read Promotions  rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Promotions/Catalog Price Rules/Edit Catalog Price Rules'));
        $this->adminUserHelper()->createRestrictedRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with only to Catalog Read Promotions rights
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotions Menu
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify Read access rights to  Catalog Price Rule
        $this->navigate('manage_catalog_price_rules');
        // verify No rights to create Catalog Price Rule
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add New Rule is available,but shouldn't");
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_shopping_cart_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_automated_email_reminder_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }

    /**
     * <p>Check Promotions Shopping Cart price Rules Create Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with  rights only to Shopping Cart Price Rules</p>
     * <p>Expected Results:
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has  Rights to Shopping Cart Price Rule</p>
     * <p>Admin User has  Rights to create Catalog Price Rule</p>
     * <p>Admin User has NO rights to create Automated Reminder Rule</p>
     *
     * @TestlinkId TL-MAGE-1475
     * @test
     */
    public function checkPromotionsShoppingCartRulesCreateRights()
    {
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Create rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Promotions/Shopping Cart Price Rules'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights to Shopping Cart Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with  Create rights to Shopping Cart Price Rule
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify Create rights to Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_automated_email_reminder_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }

    /**
     * <p>Check Promotions Shopping Cart price Rules Read Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with  rights only to Shopping Cart Price Rules</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has rights to Shopping Cart Price Rule</p>
     * <p>Admin User has NO rights to create Catalog Price Rule</p>
     * <p>Admin User hasNO rights to create Automated Reminder Rule</p>
     *
     * @TestlinkId TL-MAGE-1464
     * @test
     */
    public function checkPromotionsShoppingCartRulesReadRights()
    {
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Read rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Promotions/Shopping Cart Price Rules/Edit Shopping Cart Price Rules'));
        $this->adminUserHelper()->createRestrictedRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  Only Read rights to Shopping Cart Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with Read rights to Shopping Cart Price Rule
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify Read rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        // verify No rights to create Shopping Cart Price Rule
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add new rule is available,but shouldn't");
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_automated_email_reminder_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }

    /**
     * <p>Check Automated Email Reminder Rules Create Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with  rights only to Shopping Cart Price Rules</p>
     * <p>Expected Results:</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin user has Create rights  to Automated Email Reminder Rules</p>
     * <p>Admin user hasNO rights to create Catalog Price Rule</p>
     * <p>Admin user has NO rights to create Shopping Cart Price Rules</p>
     *
     * @TestlinkId TL-MAGE-1476
     * @test
     */
    public function checkAutomatedEmailReminderRulesCreateRights()
    {
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Create rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Promotions/Automated Email Reminder Rules'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights Automated Email Reminder Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with  Create rights to Automated Email Reminder Rules
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify Create rights to Automated Email Reminder Rules
        $this->navigate('manage_automated_email_reminder_rules');
        $this->priceRulesHelper()->createEmailReminderRule();
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_shopping_cart_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }

    /**
     * <p>Check Automated Email Reminder Rules  Read Rights</p>
     * <p>Preconditions</p>
     * <p>Login to backend as admin</p>
     * <p>Go to System-Permissions-Role and click "Add New Role" button</p>
     * <p>Fill "Role Name" field</p>
     * <p>Click Role Resource Tab</p>
     * <p>In Role Resources fieldset  select only one test scope checkbox[Sales,Customers,Dashboard,Catalog,Mobile,Newsletter,CMS,Reports,System,External Page Cache,Global Search]</p>
     * <p>Click "Save Role" button for save roleSource</p>
     * <p>Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>Fill all required fields (User Info Tab)</p>
     * <p>Click User Role Tab</p>
     * <p>Select testRole</p>
     * <p>Click "Save User" button for save testAdminUser</p>
     * <p>Log out </p>
     * <p>Steps:</p>
     * <p>1.Log In as admin user with  Read rights to Automated Email Reminder Rules</p>
     * <p>Expected Results</p>
     * <p>Only Promotions menu is available</p>
     * <p>Admin User has Read  rights to Automated Email Reminder Rules</p>
     * <p>Admin User has NO rights to create Catalog Price Rule</p>
     * <p>Admin User has NO rights to create Shopping Cart Price Rule</p>
     *
     * @TestlinkId TL-MAGE-1465
     * @test
     */
    public function checkAutomatedEmailReminderRulesReadRights()
    {
        //Preconditions
        //create specific role with only read rights to Automated Email Reminder Rules
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Promotions/Automated Email Reminder Rules/Edit Automated Email Reminder Rules'));
        $this->adminUserHelper()->createRestrictedRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  Only Read rights to Automated Email Reminder Rules
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with Read rights to Automated Email Reminder Rules
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $this->assertEquals(1, count($this->getElementsByXpath($xpath)), "You have some extra menu items");
        //verify Read Rights to Automated Email Reminder Rules
        $this->navigate('manage_automated_email_reminder_rules');
        // verify No rights to create Automated Email Reminder Rules
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add new Rule is available, but shouldn't");
        //verify NO rights to Create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
        //verify NO rights to Create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_shopping_cart_price_rules');
        $this->assertTrue($this->isElementPresent($this->_getControlXpath('pageelement', 'access_denied', $uimap)),
            "Element isn't present on the page");
    }
}

