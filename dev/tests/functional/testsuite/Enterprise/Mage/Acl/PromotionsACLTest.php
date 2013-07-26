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
class Enterprise_Mage_Acl_PromotionsAclTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * <p>Check Promotions Full Rights - Catalog Price Rules, Shopping Cart Price Rules
     *    and Automated Email Reminder Rules</p>
     *
     * @TestlinkId TL-MAGE-6021
     * @test
     */
    public function checkPromotionsFullRights()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => array(
                'marketing-promotions-catalog_price_rules-edit',
                'marketing-promotions-cart_price_rules-edit',
                'marketing-communications-email_reminders-edit'
            ))
        );
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $emailRule = $this->loadDataSet('AutomatedEmailRule', 'create_automated_email_rule');
        //Preconditions
        //create specific role with full rights to Promotions Menu
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with full rights to Promotions Menu
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotions Menu
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_catalog_price_rules'), $this->getParsedMessages());
        //Verify that only Promotions menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 3 child elements
        $this->assertEquals(3, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of Top Navigation Menu elements not equal 3, should be equal');
        //verify rights to create Catalog Price Rule
        $this->priceRulesHelper()->createAndApplyRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_applied_rule');
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify rights to create Automated Email Reminder Rules
        $this->navigate('manage_automated_email_reminder_rules');
        $this->priceRulesHelper()->createEmailReminderRule($emailRule);
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Check Promotions only Catalog Price Rules Read Rights</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1463
     */
    public function checkPromotionsCatalogPriceRulesReadRights()
    {
        $this->markTestIncomplete('MAGETWO-3687');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl', array(
            'resource_acl' => 'marketing-promotions-catalog_price_rules',
            'resource_acl_skip' => 'marketing-promotions-catalog_price_rules-edit'
        ));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with only to Catalog Read Promotions  rights
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with only to Catalog Read Promotions rights
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotions Menu
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify Read access rights to  Catalog Price Rule
        $this->assertTrue($this->checkCurrentPage('manage_catalog_price_rules'), $this->getParsedMessages());
        // verify No rights to create Catalog Price Rule
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add New Rule is available,but shouldn't");
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_shopping_cart_price_rules page is permitted");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_automated_email_reminder_rules page is permitted");
    }

    /**
     * <p>Check Promotions Shopping Cart price Rules Create Rights</p>
     *
     * @TestlinkId TL-MAGE-1475
     * @test
     */
    public function checkPromotionsShoppingCartRulesCreateRights()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions-cart_price_rules-edit'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Create rights
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights to Shopping Cart Price Rules Menu
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with  Create rights to Shopping Cart Price Rule
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify Create rights to Shopping Cart Price Rule
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->getParsedMessages());
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_catalog_price_rules page is permitted");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_automated_email_reminder_rules page is permitted");
    }

    /**
     * <p>Check Promotions Shopping Cart price Rules Read Rights</p>
     *
     * @TestlinkId TL-MAGE-1464
     * @test
     */
    public function checkPromotionsShoppingCartRulesReadRights()
    {
        $this->markTestIncomplete('MAGETWO-3687');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl', array(
            'resource_acl' => 'marketing-promotions-cart_price_rules',
            'resource_acl_skip' => 'marketing-promotions-cart_price_rules-edit'
        ));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Read rights
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  Only Read rights to Shopping Cart Price Rules Menu
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with Read rights to Shopping Cart Price Rule
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify Read rights to create Shopping Cart Price Rule
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->getParsedMessages());
        // verify No rights to create Shopping Cart Price Rule
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add new rule is available,but shouldn't");
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_catalog_price_rules page is permitted");
        //verify NO rights to create Automated Reminder Rule
        $this->navigate('manage_automated_email_reminder_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_automated_email_reminder_rules page is permitted");
    }

    /**
     * <p>Check Automated Email Reminder Rules Create Rights</p>
     *
     * @TestlinkId TL-MAGE-1476
     * @test
     */
    public function checkAutomatedEmailReminderRulesCreateRights()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications-email_reminders-edit'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $emailRule = $this->loadDataSet('AutomatedEmailRule', 'create_automated_email_rule');
        //Preconditions
        //create specific role with only Shopping Cart Price Rule Create rights
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights Automated Email Reminder Rules Menu
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with  Create rights to Automated Email Reminder Rules
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify Create rights to Automated Email Reminder Rules
        $this->assertTrue($this->checkCurrentPage('manage_automated_email_reminder_rules'), $this->getParsedMessages());
        $this->priceRulesHelper()->createEmailReminderRule($emailRule);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_catalog_price_rules page is permitted");
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_shopping_cart_price_rules page is permitted");
    }

    /**
     * <p>Check Automated Email Reminder Rules  Read Rights</p>
     *
     * @TestlinkId TL-MAGE-1465
     * @test
     */
    public function checkAutomatedEmailReminderRulesReadRights()
    {
        $this->markTestIncomplete('MAGETWO-8413');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl', array(
            'resource_acl' => 'marketing-communications-email_reminders',
            'resource_acl_skip' => 'marketing-communications-email_reminders-edit'
        ));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        //Preconditions
        //create specific role with only read rights to Automated Email Reminder Rules
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  Only Read rights to Automated Email Reminder Rules
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with Read rights to Automated Email Reminder Rules
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify Read Rights to Automated Email Reminder Rules
        $this->assertTrue($this->checkCurrentPage('manage_automated_email_reminder_rules'), $this->getParsedMessages());
        // verify No rights to create Automated Email Reminder Rules
        $this->assertFalse($this->buttonIsPresent('add_new_rule'), "Button Add new Rule is available, but shouldn't");
        //verify NO rights to Create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_catalog_price_rules page is permitted");
        //verify NO rights to Create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_shopping_cart_price_rules page is permitted");
    }

    /**
     * <p>Bug MAGETWO-2588</p>
     * <p>Create Shopping cart price rule with custom Role Scopes Permissions using "Save and Continue Edit" button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-2283
     */
    public function createShoppingCartPriceRulesWithCustomRoleScopes()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions-cart_price_rules'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        //Preconditions
        //create specific role with create rights to Shopping Cart Price
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights to Shopping Cart Price Rule
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with create rights to Shopping Cart Price Rule
        $this->adminUserHelper()->loginAdmin($loginData);
        //verify Create rights to Shopping Cart Price Rule
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->getParsedMessages());
        $this->priceRulesHelper()->createRuleAndContinueEdit($ruleData, 'edit_shopping_cart_price_rule');
        $this->assertMessagePresent('success', 'success_saved_rule');
    }

    /**
     * <p>Bug MAGETWO-2589</p>
     * <p>Create Automated Reminder Rule with custom Role Scopes Permissions using "Save and Continue Edit" button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6083
     */
    public function createAutomatedReminderRulesWithCustomRoleScopes()
    {
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-communications-email_reminders'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $emailRule = $this->loadDataSet('AutomatedEmailRule', 'create_automated_email_rule');
        //Preconditions
        //create specific role with create rights to Create Automated Reminder Rule
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with Create rights to Shopping Cart Price Rule
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //login as admin user with create rights to Automated Reminder Rule
        $this->adminUserHelper()->loginAdmin($loginData);
        //verify Create rights to Automated Reminder Rule
        $this->assertTrue($this->checkCurrentPage('manage_automated_email_reminder_rules'), $this->getParsedMessages());
        $this->priceRulesHelper()->createRuleAndContinueEdit($emailRule, 'edit_automated_email_reminder_rules');
        $this->assertMessagePresent('success', 'success_saved_rule');
    }
}