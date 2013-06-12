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
class Core_Mage_Acl_SalesTaxTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Admin user with Role Resource  System/Tax management can create Tax Rate,
     *    Tax Rule with Customer and product Tax Class "</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5961
     */
    public function permissionTaxItemsCreate()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-taxes'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Login as test admin user
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_tax_rule'), $this->getParsedMessages());
        //Upload Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test');
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required');
        //Create Tax Rate
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRateData);
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Create Tax Rule
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxRule($taxRuleData);
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        //Create Customer Tax Class
        $this->navigate('manage_tax_rule');
        $multiselect = 'customer_tax_class';
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $taxClassName = $this->generate('string', 20);
        $this->fillCompositeMultiselect($multiselect, array($taxClassName));
        $this->assertTrue($this->verifyCompositeMultiselect($multiselect, array($taxClassName)),
            'Failed to add new value');
        //Create Product Tax Class
        $this->navigate('manage_tax_rule');
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array('Taxable Goods')),
            '"Taxable Goods" is absent or not selected');
        //Verifying
        return array(
            'tax_rule_name' => $taxRuleData['name'],
            'tax_rate' => $taxRateData['tax_identifier']
        );
    }

    /**
     * <p>Admin user with Role Resource  System/Tax Management  can delete Tax Rate and Tax Rule with Customer and product Tax Class</p>
     *
     * @param $testData
     *
     * @depends permissionTaxItemsCreate
     * @test
     * @TestlinkId TL-MAGE-5962
     */
    public function permissionTaxItemsDelete($testData)
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'stores-taxes'));
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');
        //Create admin user with specific role
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');
        $this->logoutAdminUser();
        //Login as test admin user
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password' => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->assertTrue($this->checkCurrentPage('manage_tax_rule'), $this->getParsedMessages());
        //Delete Product Tax Class
        $this->navigate('manage_tax_rule');
        $taxClass = $this->generate('string', 26);
        $this->clickButton('add_rule');
        $this->clickControl('link', 'tax_rule_info_additional_link');
        $this->fillCompositeMultiselect('product_tax_class', array($taxClass));
        $this->assertTrue($this->verifyCompositeMultiselect('product_tax_class', array($taxClass)),
            $this->getParsedMessages());
        $this->deleteCompositeMultiselectOption('product_tax_class', $taxClass, 'confirmation_for_delete_class');
        //Delete Tax Rate
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->deleteTaxItem(array('filter_tax_id' => $testData['tax_rate']), 'rate');
        $this->assertMessagePresent('success', 'success_deleted_tax_rate');
        //Delete Tax Rule
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->deleteTaxItem(array($testData['tax_rule_name']), 'rule');
        $this->assertMessagePresent('success', 'success_deleted_tax_rule');
    }
}
