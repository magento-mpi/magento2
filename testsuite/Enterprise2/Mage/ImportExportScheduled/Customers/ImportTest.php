<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Enterprise2_Mage_ImportExportScheduled_Helper  importExportScheduledHelper() importExportScheduledHelper()
 */
class Enterprise2_Mage_ImportExportScheduled_Import_CustomersTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }

    /**
     * Simple Scheduled Export Precondition
     *
     * @test
     */
    public function preconditionImport()
    {
        $this->navigate('manage_customers');
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        return $customerData;
    }

    /**
     * Running Scheduled Import of Customer Finances File (Add/Update)
     *
     * @dataProvider financeImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5790, TL-MAGE-5793, TL-MAGE-5797
     */
    public function importValidData($financesCsv, $behavior, $customerData)
    {
        $financesCsv = str_replace('<realEmail>', $customerData['email'], $financesCsv);
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customers Main File',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer_finances.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, array($financesCsv));
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('success', 'success_run');
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData['name'],
                    'operation' => 'Import'
                )
            ), 'Error is occurred');
        //verify changes
        $this->navigate('manage_customers');
        $this->addParameter(
            'customer_first_last_name',
            $financesCsv['firstname'] . ' ' . $financesCsv['lastname']);
        $this->customerHelper()->openCustomer(array('email' => $financesCsv['email']));
        $this->openTab('account_information');
        //Verifying
        $this->assertTrue($this->verifyForm(
            array(
                'email' => $financesCsv['email'],
                'first_name' => $financesCsv['firstname'],
                'last_name' => $financesCsv['lastname']
            ), 'account_information'), $this->getParsedMessages());
    }

    /**
     * Running Scheduled Import of Customer Finances File (Add/Update)
     *
     * dataProvider financeInvalidImportData
     * depends preconditionImport
     * test
     * testLinkId TL-MAGE-5801
     */
    public function importInvalidData($financesCsv, $behavior, $customerData)
    {
        $financesCsv = str_replace('<realEmail>', $customerData['email'], $financesCsv);
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customer Finances',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, $financesCsv);
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('error', 'error_run');
    }

    public function financeInvalidImportData()
    {
        $csvRow1 = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => 'test',
                'reward_points' => 'test',
            )
        );
        $csvRow2 = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '1',
                'reward_points' => '1',
            )
        );
        return array(
            array(array($csvRow1, $csvRow1), 'Add/Update Complex Data')
        );
    }

    public function financeImportData()
    {
        $csvRow1 = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $csvRow2 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => '<realEmail>',
                'group_id' => '2'
            )
        );
        $csvRow3 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                '_action' => 'Update'
        ));
        $csvRow4 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => $csvRow3['email'],
                '_action' => 'Delete'
        ));
        $csvRow5 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => '<realEmail>',
                '_action' => ''
        ));
        $csvRow6 = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $csvRow7 = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        return array(
            array($csvRow1, 'Add/Update Complex Data'),
            array($csvRow2, 'Add/Update Complex Data'),
            //array($csvRow3, 'Custom Action'),
            //array($csvRow4, 'Custom Action'),
            //array($csvRow5, 'Custom Action'),
            //array($csvRow1, 'Add/Update Complex Data'),
            //array($csvRow6, 'Delete Entities'),
            //array($csvRow1, 'Add/Update Complex Data'),
            //array($csvRow7, 'Delete Entities')
        );
    }
}