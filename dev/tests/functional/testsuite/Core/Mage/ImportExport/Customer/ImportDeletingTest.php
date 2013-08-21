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
class Core_Mage_ImportExport_Customer_ImportDeletingTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->runMassAction('Delete', 'all', 'confirmation_for_massaction_delete');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('import');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Deleting Customer via Customers Main File
     *
     * @test
     * @dataProvider importData
     * @TestlinkId TL-MAGE-5675
     */
    public function deletingCustomer($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData[0] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['email'] = $userData[0]['email'];
        $data[0]['firstname'] = $userData[0]['first_name'];
        $data[0]['lastname'] = $userData[0]['last_name'];

        $data[1]['email'] = $userData[1]['email'];
        $data[1]['firstname'] = 'firstname_new';
        $data[1]['lastname'] = 'lastname_new';

        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()
            ->chooseImportOptions('Customers Main File', 'Delete Entities');
        //Steps 3-5
        $importReport = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayHasKey('import', $importReport,
            'Import has been finished with issues: ' . print_r($importReport, true));
        $this->assertArrayHasKey('success', $importReport['import'],
            'Import has been finished with issues: ' . print_r($importReport, true));
        //Step 6
        $this->navigate('manage_customers');
        //Verify that the first customer is absent after import 'Delete Entities'
        $this->assertFalse($this->customerHelper()->isCustomerPresentInGrid($userData[0]),
            'Customer is found with data: ' . print_r($userData[0], true));
        //Verify that the second customer is absent after import 'Delete Entities'
        $this->assertFalse($this->customerHelper()->isCustomerPresentInGrid($userData[1]),
            'Customer is found with data: ' . print_r($userData[1], true));
    }

    public function importData()
    {
        return array(
            array(array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv'),
                $this->loadDataSet('ImportExport', 'generic_customer_csv')
            ))
        );
    }

    /**
     * Deleting Customer via Customers Main File
     *
     * @test
     * @dataProvider importCustomerData
     * @TestlinkId TL-MAGE-5678
     */
    public function deletingCustomerWithDifferentEmailOrWebsite($data)
    {
        //Create Customer1
        $this->navigate('manage_customers');
        $userData[0] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[0]);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Create Customer2
        $this->navigate('manage_customers');
        $userData[1] = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($userData[1]);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $data[0]['email'] = 'not_existing_email@example.co';
        $data[0]['firstname'] = $userData[0]['first_name'];
        $data[0]['lastname'] = $userData[0]['last_name'];

        $data[1]['email'] = $userData[1]['email'];
        $data[1]['firstname'] = $userData[1]['first_name'];
        $data[1]['lastname'] = $userData[1]['last_name'];

        //Steps 1-2
        $this->navigate('import');
        $this->importExportHelper()->chooseImportOptions('Customers Main File', 'Delete Entities');
        //Steps 3-5
        $importReport = $this->importExportHelper()->import($data);
        //Check import
        $this->assertArrayNotHasKey('import', $importReport,
            'Import has been finished with issues: ' . print_r($importReport, true));
        $this->assertArrayHasKey('error', $importReport['validation'], 'Import has been finished with issues:');
        //Step 5
        $this->navigate('manage_customers');
        //Verify that the first customer is present after import 'Delete Entities'
        $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($userData[0]), 'Customer not found');
        //Verify that the second customer is present after import 'Delete Entities'
        $this->assertTrue($this->customerHelper()->isCustomerPresentInGrid($userData[1]), 'Customer not found');
    }

    public function importCustomerData()
    {
        return array(
            array(array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv'),
                $this->loadDataSet('ImportExport', 'generic_customer_csv',
                    array('_website' => $this->generate('string', 30, ':digit:')))
            ))
        );
    }
}