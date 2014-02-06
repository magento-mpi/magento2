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
class Enterprise_Mage_ImportExportScheduled_Customers_Finances_ExportTest extends Mage_Selenium_TestCase
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
    public function preconditionExport()
    {
        $this->navigate('manage_customers');
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
        $storeCreditBalance = $this->generate('string', 3, ':digit:');
        $rewardPointsBalance = $this->generate('string', 3, ':digit:');
        $this->customerHelper()->updateStoreCreditBalance(array('update_balance' => $storeCreditBalance), true);
        $this->customerHelper()->updateRewardPointsBalance(array('update_balance' => $rewardPointsBalance));
        $financesData['_email'] = $customerData['email'];
        $financesData['_website'] = 'base';
        $financesData['_finance_website'] = 'base';
        $financesData['store_credit'] = $storeCreditBalance;
        $financesData['reward_points'] = $rewardPointsBalance;
        return array('customer' => $customerData, 'finance' => $financesData);
    }

    /**
     * Simple Scheduled Export of Customer Finances File
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5821
     */
    public function simpleExport(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances'));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $this->assertMessagePresent('success', 'success_run');
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportData['name'],
                    'operation' => 'Export'
                )
            ), 'Error is occurred');
        //get file
        $exportData['file_name'] = $this->importExportScheduledHelper()->
            getFilePrefix(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $exportData['file_name'] .= 'export_customer_finance.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertNotNull($this->importExportHelper()->lookForEntity('finances', $customerData['finance'], $csv),
            "Finance not found in csv file"
        );
    }

    /**
     * Scheduled Export of Customer Finances File with filters
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5824
     */
    public function simpleExportWithFilterSkipped(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances',
                'filters' => array('email' => $customerData['customer']['email'])));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $this->assertMessagePresent('success', 'success_run');
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportData['name'],
                    'operation' => 'Export'
                )
            ), 'Error is occurred');
        //get file
        $exportData['file_name'] = $this->importExportScheduledHelper()->
            getFilePrefix(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $exportData['file_name'] .= 'export_customer_finance.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertEquals(1, count($csv), 'Export with filter returned more than one record');
        $this->assertNotNull($this->importExportHelper()->lookForEntity('finance', $customerData['finance'], $csv),
            "Finance not found in csv file"
        );
    }
}