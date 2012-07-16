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
class Enterprise2_Mage_ImportExportScheduled_Export_Addresses_CustomerTest extends Mage_Selenium_TestCase
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
        $addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($customerData, $addressData);
        $this->addParameter('customer_first_last_name', $customerData['first_name'] . ' ' . $customerData['last_name']);
        $this->customerHelper()->openCustomer(array('email' => $customerData['email']));
        $this->openTab('addresses');
        $addressData['_entity_id'] = $this->customerHelper()->isAddressPresent($addressData);
        $addressData['_email'] = $customerData['email'];
        return array('customer' => $customerData, 'address' => $addressData);
    }

    /**
     * Simple Scheduled Export of Customer Address File
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5823
     */
    public function simpleExport(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'file_format_version' => 'Magento 2.0 format',
                'entity_subtype' => 'Customer Addresses'));
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
        $exportData['file_name'] .= 'export_customer_address.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertNotNull($this->importExportHelper()->lookForEntity('address', $customerData['address'], $csv),
            "Address not found in csv file"
        );
    }

    /**
     * Scheduled Export of Customer Address File with filters
     *
     * @depends preconditionExport
     * @test
     * @testLinkId TL-MAGE-5820
     */
    public function simpleExportWithFilterSkipped(array $customerData)
    {
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'file_format_version' => 'Magento 2.0 format',
                'entity_subtype' => 'Customer Addresses',
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
        $exportData['file_name'] .= 'export_customer_address.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData);
        $this->assertEquals(1, count($csv), 'Export with filter returned more than one record');
        $this->assertArrayNotHasKey('created_at', $csv[0], 'Exported data contains skipped attribute');
        $this->assertNotNull($this->importExportHelper()->lookForEntity('address', $customerData['address'], $csv),
            "Address not found in csv file"
        );
    }
}