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
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom_website',
            array('resource_1' => 'Promotions/Catalog Price Rules'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //create admin user with  rights to Catalog Price Rules Menu
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Steps
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_catalog_price_rules');
        $priceRuleData = $this->loadDataSet('CatalogPriceRule', 'test_catalog_rule');
        $this->clickButton('add_new_rule');
        $this->priceRulesHelper()->fillTabs($priceRuleData);
        $this->saveForm('save_and_continue_edit', false);
        $this->addParameter('elementTitle', $priceRuleData['info']['rule_name']);
        $this->validatePage('edit_rule_page');
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->verifyForm($priceRuleData, 'rule_information');
        $this->verifyForm($priceRuleData, 'rule_actions');
    }
}