<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ImportExport_EmptyValues_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('import');
    }
    /**
     * Empty values for existing attributes in csv for Customers Main File
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5639
     */
    public function emptyValuesAttributesInCsv($data)
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        $this->customerHelper()->fillForm(array('middle_name' => 'Test Middle Name'), 'account_information');
        $this->saveForm('save_customer');

        $data[0]['email'] = $userData['email'];
        $data[0]['firstname'] = $userData['first_name'];
        $data[0]['lastname'] = $userData['last_name'];
        $data[0]['password'] = $userData['password'];
        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Add/Update Complex Data');
        $this->waitForElementVisible($this->_getControlXpath('field', 'file_to_import'));
        //Steps 3-5
        $report = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $report, 'Import has been finished with issues:');
        $this->assertArrayHasKey('success', $report['import'], 'Import has been finished with issues:');
        //Step 6
        $this->navigate('manage_customers');
        //Step 7
        $this->customerHelper()->openCustomer(array('email' => $userData['email']));
        //Verify customer account
        $this->assertTrue($this->verifyForm(array('middle_name' => 'Test Middle Name'), 'account_information'),
            'Existent customer has been updated');
    }
    public function importData()
    {
        return array(
            array(
                array(
                    $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                            'confirmation' => '',
                            'created_at' => '19.06.2012 18:00',
                            'created_in' => 'Admin',
                            'default_billing' => '',
                            'default_shipping' => '',
                            'disable_auto_group_change' => '0',
                            'dob' => '',
                            'gender' => '',
                            'group_id' => '1',
                            'middlename' => '',
                            'prefix' => '',
                            'rp_token' => '',
                            'rp_token_created_at' => '',
                            'password_hash' => '48927b9ee38afb672504488a45c0719140769c24c10e5ba34d203ce5a9c15b27:2y',
                            'store_id' => '0',
                            'website_id' => '0',
                            'suffix' => '',
                            'taxvat' => '',
                            )
                    )
                )
            )
        );
    }
}