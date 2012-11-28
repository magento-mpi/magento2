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
     * <p>Admin user with Role Resource  Sales/Tax/Customer Tax Class can create new Customer Tax Class</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5957
     */
    public function permissionCustomerTaxClass()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Tax/Customer Tax Classes'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_customer_tax_class');
        //Create Customer Tax Class
        //Data
        $customerTaxClassData = $this->loadDataSet('Tax', 'new_customer_tax_class');
        //Steps
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        $this->taxHelper()->openTaxItem($customerTaxClassData, 'customer_class');
        $this->assertTrue($this->verifyForm($customerTaxClassData), $this->getParsedMessages());

        return $customerTaxClassData;
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax/Product Tax Class can create new Product Tax Class</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5958
     */
    public function  permissionProductTaxClass()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Tax/Product Tax Classes'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_product_tax_class');
        //Create new Product Tax Class
        $productTaxClassData = $this->loadDataSet('Tax', 'new_product_tax_class');
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        $this->taxHelper()->openTaxItem($productTaxClassData, 'product_class');
        $this->assertTrue($this->verifyForm($productTaxClassData), $this->getParsedMessages());

        return $productTaxClassData;
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax/Manage Tax Zones & Rates  can create new Tax Rate</p>
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5959
     */
    public function permissionTaxRates()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Tax/Manage Tax Zones & Rates'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_tax_zones_and_rates');
        //Create new Tax Rate
        $rate = $this->loadDataSet('Tax', 'tax_rate_create_test_zip_no');
        $search = $this->loadDataSet('Tax', 'search_tax_rate', array('filter_tax_id' => $rate['tax_identifier']));
        $this->taxHelper()->createTaxItem($rate, 'rate');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        $this->taxHelper()->openTaxItem($search, 'rate');
        $this->assertTrue($this->verifyForm($rate), $this->getParsedMessages());

        return $rate;
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax/Manage Tax Rules  can create new Tax Rule</p>
     *
     * @param array $customerTaxClassData
     * @param array $productTaxClassData
     * @param array $rate
     *
     * @depends permissionCustomerTaxClass
     * @depends permissionProductTaxClass
     * @depends permissionTaxRates
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5960
     */
    public function permissionTaxRule($customerTaxClassData, $productTaxClassData, $rate)
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Tax/Manage Tax Rules'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_tax_rule');
        //Create new Tax Rule
        //Data
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required',
            array('customer_tax_class'=> $customerTaxClassData['customer_class_name'],
                  'product_tax_class' => $productTaxClassData['product_class_name'],
                  'tax_rate'          => $rate['tax_identifier']));
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax can create "Customer Tax Class"</p>
     * <p>"Product Tax Class","Tax Rate", "Tax Rule"</p>
     *
     * @depends permissionTaxRule
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-5961
     */
    public function  permissionFullFlowTaxRuleCreation()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource =
            $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom', array('resource_1' => 'Sales/Tax'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_tax_rule');
        //Create tax rate, product tax class,
        //Data
        $taxRateData = $this->loadDataSet('Tax', 'tax_rate_create_test');
        $productTaxClassData = $this->loadDataSet('Tax', 'new_product_tax_class');
        $customerTaxClassData = $this->loadDataSet('Tax', 'new_customer_tax_class');
        //Create Tax Rate
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxItem($taxRateData, 'rate');
        $this->assertMessagePresent('success', 'success_saved_tax_rate');
        //Create Product Tax Class
        $this->navigate('manage_product_tax_class');
        $this->taxHelper()->createTaxItem($productTaxClassData, 'product_class');
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        //Create Customer Tax Class
        $this->navigate('manage_customer_tax_class');
        $this->taxHelper()->createTaxItem($customerTaxClassData, 'customer_class');
        $this->assertMessagePresent('success', 'success_saved_tax_class');
        $this->taxHelper()->openTaxItem($customerTaxClassData, 'customer_class');
        //Create Tax Rule
        $testData = array('tax_rate'          => $taxRateData['tax_identifier'],
                          'product_tax_class' => $productTaxClassData['product_class_name'],
                          'customer_tax_class'=> $customerTaxClassData['customer_class_name']);
        $taxRuleData = $this->loadDataSet('Tax', 'new_tax_rule_required', $testData);
        $searchTaxRuleData = $this->loadDataSet('Tax', 'search_tax_rule', array('filter_name' => $taxRuleData['name']));
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxItem($taxRuleData, 'rule');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_tax_rule');
        $this->taxHelper()->openTaxItem($searchTaxRuleData, 'rule');
        $this->assertTrue($this->verifyForm($taxRuleData), $this->getParsedMessages());
        $testData['tax_rule_name'] = $taxRuleData['name'];

        return $testData;
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax  can delete "Customer Tax Class"</p>
     * <p>"Product Tax Class","Tax Rate", "Tax Rule"</p>
     *
     * @param $testData
     * @depends permissionFullFlowTaxRuleCreation
     *
     * @test
     * @TestlinkId TL-MAGE-5962
     */
    public function permissionDeleteAllTaxItems($testData)
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource =
            $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
                array('resource_1' => 'Sales/Tax'));
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
        $loginData =
            array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_tax_rule');
        //Delete Tax Rule
        $taxRuleToDelete = array($testData['tax_rule_name']);
        $this->taxHelper()->deleteTaxItem($taxRuleToDelete, 'rule');
        $this->assertMessagePresent('success', 'success_deleted_tax_rule');
        //Delete Product Tax Class
        $this->navigate('manage_product_tax_class');
        $productClassToDelete['product_class_name'] = $testData['product_tax_class'];
        $this->taxHelper()->deleteTaxItem($productClassToDelete, 'product_class');
        $this->assertMessagePresent('success', 'success_deleted_tax_class');
        //Delete Tax Rate
        $this->navigate('manage_tax_zones_and_rates');
        $taxRateToDelete['filter_tax_id'] = $testData['tax_rate'];
        $this->taxHelper()->deleteTaxItem($taxRateToDelete, 'rate'); ///tut
        $this->assertMessagePresent('success', 'success_deleted_tax_rate');
        //Delete Customer Tax Class
        $this->navigate('manage_customer_tax_class');
        $customerClassToDelete['customer_class_name'] = $testData['customer_tax_class'];
        $this->taxHelper()->deleteTaxItem($customerClassToDelete, 'customer_class');
        $this->assertMessagePresent('success', 'success_deleted_tax_class');
    }

    /**
     * <p>Admin user with Role Resource  Sales/Tax has ability to use Import/Export functionality</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5963
     */
    public function permissionAllTaxImportExport()
    {
        $this->navigate('manage_roles');
        //Create new role with specific Resource
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_custom',
            array('resource_1' => 'Sales/Tax'));
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
        $loginData = array('user_name' => $testAdminUser['user_name'], 'password'  => $testAdminUser['password']);
        $this->adminUserHelper()->loginAdmin($loginData);
        $this->validatePage('manage_tax_rule');
        //Steps
        $this->navigate('manage_import_export_tax_rates');
        //Verifying
        $this->assertTrue($this->buttonIsPresent('import_tax_rates'), 'Button "import_tax_rates" is not presented');
        $this->assertTrue($this->buttonIsPresent('export_tax_rates'), 'Button "export_tax_rates" is not presented');
    }
}