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
     * Running Scheduled Import of Customer Main File (Add/Update, Delete Entities, Custom Action)
     *
     * @dataProvider customerImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5786, TL-MAGE-5791, TL-MAGE-5794
     */
    public function importValidData($customersCsv, $behavior, $customerData)
    {
        $customersCsv = str_replace('<realEmail>', $customerData['email'], $customersCsv);
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customers Main File',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, array($customersCsv));
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
        if ((isset($customersCsv['_action']) && strtolower($customersCsv['_action']) == 'delete')
            || $behavior == 'Delete Entities'
        ) {
            $this->assertFalse(
                $this->customerHelper()->isCustomerPresentInGrid(
                    array('email' => $customersCsv['email'])),
                'Deleted customer was found');
        } else {
            $this->addParameter(
                'customer_first_last_name',
                $customersCsv['firstname'] . ' ' . $customersCsv['lastname']);
            $this->customerHelper()->openCustomer(array('email' => $customersCsv['email']));
            $this->openTab('account_information');
            //Verifying
            $this->assertTrue($this->verifyForm(
                array(
                    'email' => $customersCsv['email'],
                    'first_name' => $customersCsv['firstname'],
                    'last_name' => $customersCsv['lastname']),
                'account_information'), $this->getParsedMessages());
        }
    }

    /**
     * Invalid data in Customer Main File
     *
     * @dataProvider customerInvalidImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5799
     */
    public function importInvalidData($customersCsv, $behavior, $customerData)
    {
        $customersCsv = str_replace('<realEmail>', $customerData['email'], $customersCsv);
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customers',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
        ));
        $this->importExportScheduledHelper()->putCsvToFtp($importData, $customersCsv);
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

    public function customerInvalidImportData()
    {
        $csvRow1 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => '<realEmail>'
            )
        );
        $csvRow2 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                'email' => 'invalidEmail@@mail.com'
            )
        );
        return array(
            array(array($csvRow1, $csvRow2), 'Add/Update Complex Data')
        );
    }

    public function customerImportData()
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
            'firstname' => $this->generate('string', 5),
            'lastname' => $this->generate('string', 5),
            'email' => '<realEmail>',
            '_action' => ''
        ));
        $csvRow6 = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $csvRow7 = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $csvRow8 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => $csvRow6['email']
        ));
        $csvRow9 = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => $csvRow7['email'],
            'firstname' => $this->generate('string', 5),
            'lastname' => $this->generate('string', 5),
        ));
        return array(
            array($csvRow1, 'Add/Update Complex Data'),
            array($csvRow2, 'Add/Update Complex Data'),
            array($csvRow3, 'Custom Action'),
            array($csvRow4, 'Custom Action'),
            array($csvRow5, 'Custom Action'),
            array($csvRow6, 'Add/Update Complex Data'),
            array($csvRow7, 'Add/Update Complex Data'),
            array($csvRow8, 'Delete Entities'),
            array($csvRow9, 'Delete Entities')
        );
    }
}