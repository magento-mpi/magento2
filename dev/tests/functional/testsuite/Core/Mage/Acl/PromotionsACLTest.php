<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_PromotionsACLTest extends Mage_Selenium_TestCase
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
     * <p>Check Promotions Full rights</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6021
     */
    public function checkPromotionsFullRights()
    {
        //Preconditions
        //create specific role with full rights to Promotions Menu
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotions menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
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
     *
     * @test
     * @TestlinkId TL-MAGE-1467
     */
    public function checkPromotionsCatalogOnlyRights()
    {
        //Preconditions
        //create specific role with only to Catalog Promotions Menu rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions-catalog_price_rules'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_shopping_cart_price_rules page is permitted");
    }

    /**
     * <p>Check Promotions Shopping Cart Only Rights</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1475
     */
    public function checkPromotionsShoppingCartOnlyRights()
    {
        //Preconditions
        //create specific role with full rights to Promotion Menu
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'marketing-promotions-cart_price_rules'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            "You have some extra menu items");
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $this->assertTrue($this->controlIsPresent('pageelement', 'access_denied'),
            "Access to manage_catalog_price_rules page is permitted");
    }
}
