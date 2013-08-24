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
class Enterprise_Mage_ImportExportScheduled_Customers_Finances_ImportTest extends Mage_Selenium_TestCase
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
     * Running Scheduled Import of Customer Finances File (Add/Update, Delete Entities, Custom Action)
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
            'entity_type' => 'Customer Finances',
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
        $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
        if ((isset($financesCsv['_action']) && strtolower($financesCsv['_action']) == 'delete')
            || $behavior == 'Delete Entities') {
            $financesCsv['store_credit'] = '0.00';
            $financesCsv['reward_points'] = '0';
        }
        $this->assertEquals(
            $financesCsv['store_credit'],
            str_replace('$', '', $this->customerHelper()->getStoreCreditBalance()),
            'Store credit balance is wrong');
        $this->assertEquals(
            $financesCsv['reward_points'],
            $this->customerHelper()->getRewardPointsBalance(),
            'Reward points balance is wrong');
    }

    /**
     * Invalid data in Customer Finances File
     *
     * @dataProvider financeInvalidImportData
     * @depends preconditionImport
     * @test
     * @testLinkId TL-MAGE-5801
     */
    public function importInvalidData($financesCsv, $behavior, $customerData)
    {
        $financesCsv = str_replace('<realEmail>', $customerData['email'], $financesCsv);
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customer Finances',
            'behavior' => $behavior,
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer_finances.csv',
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
        $returnData = array();
        $returnData[1] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => 'test',
                'reward_points' => 'test',
            )
        );
        $returnData[2] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '1',
                'reward_points' => '1',
            )
        );
        return array(
            array(array($returnData[1], $returnData[2]), 'Add/Update Complex Data')
        );
    }

    public function financeImportData()
    {
        $returnData = array();
        $returnData[1] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '500',
                'reward_points' => '200',
            )
        );
        $returnData[2] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '100',
                'reward_points' => '500',
            )
        );
        $returnData[3] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '80',
                'reward_points' => '100',
                '_action' => 'update'
            )
        );
        $returnData[4] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '60',
                'reward_points' => '40',
                '_action' => ''
            )
        );
        $returnData[5] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '1000',
                'reward_points' => '1000',
                '_action' => 'delete'
            )
        );
        $returnData[6] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '500',
                'reward_points' => '200'
            )
        );
        $returnData[7] = $this->loadDataSet('ImportExport', 'generic_finance_csv', array(
                '_email' => '<realEmail>',
                'store_credit' => '1500',
                'reward_points' => '1200'
            )
        );
        return array(
            array($returnData[1], 'Add/Update Complex Data'),
            array($returnData[2], 'Add/Update Complex Data'),
            array($returnData[3], 'Custom Action'),
            array($returnData[4], 'Custom Action'),
            array($returnData[5], 'Custom Action'),
            array($returnData[6], 'Add/Update Complex Data'),
            array($returnData[6], 'Delete Entities'),
            array($returnData[1], 'Add/Update Complex Data'),
            array($returnData[7], 'Delete Entities')
        );
    }
}