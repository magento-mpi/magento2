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
class Enterprise_Mage_ImportExportScheduled_BackwardCompatibility_ExportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        return $customerData;
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }

    /**
     * Precondition: Create new product
     *
     * @test
     * @return array
     */
    public function preconditionAppendImportExport()
    {
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * Running Scheduled Export
     *
     * @test
     * @TestlinkId TL-MAGE-1499
     */
    public function simpleScheduledExport()
    {
        // Precondition
        $exportProducts = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array('entity_type' => 'Products'));
        $this->importExportScheduledHelper()->createExport($exportProducts);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run export
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(array(
            'name' => $exportProducts['name'],
            'operation' => 'Export'
        ));
        //Verifying
        $lastOutcome = $this->importExportScheduledHelper()->getLastOutcome(array(
            'name' => $exportProducts['name'],
            'operation' => 'Export'
        ));
        $this->assertEquals('Successful', $lastOutcome, 'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
        //get file
        $exportProducts['file_name'] = $this->importExportScheduledHelper()->getFilePrefix(array(
            'name' => $exportProducts['name'],
            'operation' => 'Export'
        ));
        $exportProducts['file_name'] .= 'export_catalog_product.csv';

        return $this->importExportScheduledHelper()->getCsvFromFtp($exportProducts);
    }

    /**
     * Running Scheduled Import
     *
     * @test
     * @dataProvider simpleScheduledImportData
     * @depends simpleScheduledExport
     * @TestlinkId TL-MAGE-1528
     */
    public function simpleScheduledImport($customersCsv, $productsCsv)
    {
        $this->markTestIncomplete('MAGETWO-11477');
        // Import Customer
        $importDataCustomers = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers',
            'behavior' => 'Append Complex Data'
        ));
        $importDataCustomers['file_name'] = date('Y-m-d_H-i-s_') . 'export_customer.csv';
        $this->importExportScheduledHelper()->createImport($importDataCustomers);
        $this->assertMessagePresent('success', 'success_saved_import');
        $lastOutcome = $this->importExportScheduledHelper()->getLastOutcome(array(
            'name' => $importDataCustomers['name'],
            'operation' => 'Import'
        ));
        $this->assertEquals('Pending', $lastOutcome, 'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importDataCustomers, $customersCsv);
        $this->importExportScheduledHelper()->applyAction(array(
            'name' => $importDataCustomers['name'],
            'operation' => 'Import'
        ));
        //Verifying import
        $lastOutcome = $this->importExportScheduledHelper()->getLastOutcome(array(
            'name' => $importDataCustomers['name'],
            'operation' => 'Import'
        ));
        $this->assertEquals('Successful', $lastOutcome, 'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
        //Import Product
        $importDataProducts = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Products',
            'behavior' => 'Append Complex Data'
        ));
        $importDataProducts['file_name'] = date('Y-m-d_H-i-s_') . 'export_catalog_product.csv';
        $this->importExportScheduledHelper()->createImport($importDataProducts);
        $this->assertMessagePresent('success', 'success_saved_import');
        $lastOutcome = $this->importExportScheduledHelper()->getLastOutcome(array(
            'name' => $importDataProducts['name'],
            'operation' => 'Import'
        ));
        $this->assertEquals('Pending', $lastOutcome, 'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importDataProducts, $productsCsv);
        $this->importExportScheduledHelper()->applyAction(array(
            'name' => $importDataProducts['name'],
            'operation' => 'Import'
        ));
        //Verifying import
        $lastOutcome = $this->importExportScheduledHelper()->getLastOutcome(array(
            'name' => $importDataProducts['name'],
            'operation' => 'Import'
        ));
        $this->assertEquals('Successful', $lastOutcome, 'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
    }

    public function simpleScheduledImportData()
    {
        $customerCsvFile = $this->loadDataSet('ImportExport', 'generic_customer_csv');
        $customerCsvFile['_address_city'] = 'Kingsport';
        $customerCsvFile['_address_company'] = 'Weingarten\'s';
        $customerCsvFile['_address_country_id'] = 'US';
        $customerCsvFile['_address_fax'] = '423-389-1069';
        $customerCsvFile['_address_firstname'] = 'Linda';
        $customerCsvFile['_address_lastname'] = 'Gilbert';
        $customerCsvFile['_address_middlename'] = 'S.';
        $customerCsvFile['_address_postcode'] = '37663';
        $customerCsvFile['_address_region'] = 'Tennessee';
        $customerCsvFile['_address_street'] = '1596 Public Works Drive';
        $customerCsvFile['_address_telephone'] = '423-389-1069';

        return array(
            array(array($customerCsvFile)),
        );
    }
}