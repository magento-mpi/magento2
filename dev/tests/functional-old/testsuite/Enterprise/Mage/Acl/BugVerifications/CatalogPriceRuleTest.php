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

class Enterprise_Mage_Acl_BugVerifications_CatalogPriceRuleTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Bug Cover<p/>
     * <p>MAGETWO-2587:</p>
     * <p> Fatal error on page if user with permission to one website try to save Catalog Price rule
     * using "Save and Continue Edit" button (EE only)</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6081
     */
    public function createCatalogPriceRuleSaveAndContinue()
    {
        //Data
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_acl' => 'marketing-promotions'));
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule',
            array('from_date' => '%noValue%', 'to_date' => '%noValue%'));
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_catalog_price_rules'), $this->getParsedMessages());
        $this->priceRulesHelper()->createRuleAndContinueEdit($priceRuleData);
        $this->assertTrue($this->checkCurrentPage('edit_rule_page'), $this->getParsedMessages());
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->priceRulesHelper()->verifyRuleData($priceRuleData);
    }
}