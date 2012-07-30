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
class Enterprise2_Mage_ImportExportScheduled_Export_CustomerTest extends Mage_Selenium_TestCase
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
        return $customerData;
    }

    /**
     * Simple Scheduled Export of Customers Main File
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5819
     */
    public function simpleExport(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customers Main File'));
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
        $exportData['file_name'] .= 'export_customer.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $customerData, $csv),
            "Customer not found in csv file"
        );
    }

    /**
     * Scheduled Export of Customers Main File with filters and skipped attributes
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5822
     */
    public function simpleExportWithFilterSkipped(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customers Main File',
                'filters' => array('email' => $customerData['email']),
                'skipped' => array('attribute_1' => array('attribute_code' => 'created_at'))));
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
        $exportData['file_name'] .= 'export_customer.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertEquals(1, count($csv), 'Export with filter returned more than one record');
        $this->assertArrayNotHasKey('created_at', $csv[0], 'Exported data contains skipped attribute');
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $customerData, $csv),
            "Customer not found in csv file"
        );
    }
}