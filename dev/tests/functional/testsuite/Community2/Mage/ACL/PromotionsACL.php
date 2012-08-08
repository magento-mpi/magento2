<?php
    /**
     * Magento
     *
     * NOTICE OF LICENSE
     *
     * This source file is subject to the Open Software License (OSL 3.0)
     * that is bundled with this package in the file LICENSE.txt.
     * It is also available through the world-wide-web at this URL:
     * http://opensource.org/licenses/osl-3.0.php
     * If you did not receive a copy of the license and are unable to
     * obtain it through the world-wide-web, please send an email
     * to license@magentocommerce.com so we can send you a copy immediately.
     *
     * DISCLAIMER
     *
     * Do not edit or add to this file if you wish to upgrade Magento to newer
     * versions in the future. If you wish to customize Magento for your
     * needs please refer to http://www.magentocommerce.com for more information.
     *
     * @category    tests
     * @package     selenium
     * @subpackage  tests
     * @author      Magento Core Team <core@magentocommerce.com>
     * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */

    /**
     * Check Promotion Rights
     *
     * @package     selenium
     * @subpackage  tests
     * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
     */
class Community2_Mage_AdminUser_CreateAclTest extends Mage_Selenium_TestCase
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
     * check Promotions Full rights
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select all Permissions checkboxes</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>13. Log In as admin user with full rights to Promotions Menu
     * <p>14. Verify that only Promotions menu is available
     * <p>15. Verify rights to create Catalog Price Rule
     * <p>16. Verify rights to create Shopping Cart Price Rule
     * @TestlinkId TL-MAGE-6021
     * @test
     *
     */
    public function checkPromotionsFullRights()
    {
        //Preconditions
        //create specific role with full rights to Promotions Menu
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'role_promotions_full_rights');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with full rights to Promotions Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotions Menu
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotions menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals(1, count($navigationElements));
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
     * check Promotions Catalog Only Rights
     * <p>Preconditions</p>
     * <p>1. Login to backend as admin</p>
     * <p>2. Go to System>Permissions>Role and click "Add New Role" button</p>
     * <p>3. Fill "Role Name" field</p>
     * <p>4. Click Role Resource Tab</p>
     * <p>5. In Role Resources fieldset  select only Catalog Price Rules Checkbox</p>
     * <p>6. Click "Save Role" button for save roleSource</p>
     * <p>7. Go to System>Permissions>Users and click "Add New User" button</p>
     * <p>8. Fill all required fields (User Info Tab)</p>
     * <p>9. Click User Role Tab</p>
     * <p>10. Select testRole</p>
     * <p>11. Click "Save User" button for save testAdminUser</p>
     * <p>12. Log out </p>
     * <p>13. Log In as admin user with full rights to Promotions Menu
     * <p>14. Verify that only Promotions menu is available
     * <p>15. Verify rights to create Catalog Price Rule
     * <p>16. Verify NO rights to create Shopping Cart Price Rule
     * @TestlinkId TL-MAGE-1467
     * @test
     *
     */
    public function checkPromotionsCatalogOnlyRights()
    {
        //Preconditions
        //create specific role with only to Catalog Promotions Menu rights
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'role_promotions_catalog_rights');
        $this->adminUserHelper()->createRole($roleSource);
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //login as admin user with full rights to Promotion Menu
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals(1, count($navigationElements));
        //verify rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $this->priceRulesHelper()->createRule($priceRuleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->assertMessagePresent('success', 'notification_message');
        //verify NO rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules',false);
        $uimap = $this->getUimapPage('admin', 'manage_shopping_cart_price_rules');
        $xpath = $this->_getControlXpath('pageelement', 'access_denied', $uimap);
        $this->assertTrue($this->isElementPresent($xpath));
    }
    /**
     * check Promotions Shopping Cart Only Rights
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
     * <p>13. Log In as admin user with  rights only to Shopping Cart Price Rules
     * <p>14. Verify that only Promotions menu is available
     * <p>15. Verify rights to create Shopping Cart Price Rule
     * <p>16. Verify NO rights to create Catalog Price Rule
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
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->logoutAdminUser();
        //login as admin user with  rights to Shopping Cart Price Rule
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        //Verify that only Promotion menu is available
        $xpath = $this->_getControlXpath('pageelement', 'navigation_menu_items');
        $navigationElements = $this->getElementsByXpath($xpath);
        $this->assertEquals(1, count($navigationElements));
        //verify rights to create Shopping Cart Price Rule
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadDataSet('ShoppingCartPriceRule', 'scpr_required_fields');
        $this->priceRulesHelper()->createRule($ruleData);
        $this->assertMessagePresent('success', 'success_saved_rule');
        //verify NO rights to create Catalog Price Rule
        $this->navigate('manage_catalog_price_rules', false);
        $uimap = $this->getUimapPage('admin', 'manage_catalog_price_rules');
        $xpath = $this->_getControlXpath('pageelement', 'access_denied', $uimap);
        $this->assertTrue($this->isElementPresent($xpath));
    }
}



