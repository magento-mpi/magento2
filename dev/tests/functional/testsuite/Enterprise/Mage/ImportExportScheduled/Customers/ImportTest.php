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
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExportScheduled_Customers_ImportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->markTestIncomplete('MAGETWO-11477');
    }

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
            'entity_type' => 'Customers Main File',
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
            'entity_type' => 'Customers Main File',
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
        $returnData = array();
        $returnData[] = array(
            array(
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                        'email' => '<realEmail>'
                    )
                ),
                $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
                        'email' => 'invalidEmail@@mail.com'
                    )
                ))
        , 'Add/Update Complex Data');
        return $returnData;
    }

    public function customerImportData()
    {
        $returnData = array();
        $returnData[1] = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $returnData[2] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => '<realEmail>',
            'group_id' => '2'
        ));
        $returnData[3] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            '_action' => 'Update'
        ));
        $returnData[4] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => $returnData[3]['email'],
            '_action' => 'Delete'
        ));
        $returnData[5] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'firstname' => $this->generate('string', 5),
            'lastname' => $this->generate('string', 5),
            'email' => '<realEmail>',
            '_action' => ''
        ));
        $returnData[6] = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $returnData[7] = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $returnData[8] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => $returnData[6]['email']
        ));
        $returnData[9] = $this->loadDataSet('ImportExport', 'generic_customer_csv', array(
            'email' => $returnData[7]['email'],
            'firstname' => $this->generate('string', 5),
            'lastname' => $this->generate('string', 5),
        ));
        return array(
            array($returnData[1], 'Add/Update Complex Data'),
            array($returnData[2], 'Add/Update Complex Data'),
            array($returnData[3], 'Custom Action'),
            array($returnData[4], 'Custom Action'),
            array($returnData[5], 'Custom Action'),
            array($returnData[6], 'Add/Update Complex Data'),
            array($returnData[7], 'Add/Update Complex Data'),
            array($returnData[8], 'Delete Entities'),
            array($returnData[9], 'Delete Entities')
        );
    }
}