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
class Enterprise2_Mage_ImportExportScheduled_Backward_Export_CustomerTest extends Mage_Selenium_TestCase
{

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $customerData = $this->loadDataSet('Customers', 'generic_customer_account');
        $this->customerHelper()->createCustomer($customerData);
        return $customerData;
    }
    /**
     * <p>Precondition:</p>
     * <p>Create new product</p>
     *
     * @test
     * @return array
     */
    public function preconditionAppendImportExport()
    {
        $this->navigate('manage_products');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }
    /**
     * Simple Test Scheduled Export
     *
     * @test
     */
    public function simpleScheduledExport()
    {
        // Export Customer
        $exportData = $this->loadDataSet('ImportExportScheduled','scheduled_export',
            array('file_format_version' => 'Magento 1.7 format'));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportData['name'],
                    'operation' => 'Export'
                )
            ),'Error is occurred');
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
        return $csv;
        //Export Product
        $exportData1 = $this->loadDataSet('ImportExportScheduled','scheduled_export',
            array(
                'entity_type' => 'Products'
            ));
        $this->importExportScheduledHelper()->createExport($exportData1);
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export'
            )
        );
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export'
            )
        );
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportData1['name'],
                    'operation' => 'Export'
                )
            ),'Error is occurred');
        //get file
        $exportData1['file_name'] = $this->importExportScheduledHelper()->
            getFilePrefix(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export'
            )
        );
        $exportData1['file_name'] .= 'export_catalog_product.csv';
        $csv = $this->importExportScheduledHelper()->getCsvFromFtp($exportData1);
        return $csv;

    }
    /**
     * Simple Test Scheduled Import
     *
     * @depends simpleScheduledExport
     * @test
     */
    public function simpleScheduledImport($csv)
    {
        // Import Customer
        $importData = $this->loadDataSet('ImportExportScheduled','scheduled_import',
                                          array('file_format_version' => 'Magento 1.7 format',
                                                'behavior'  => 'Append Complex Data'));
        $importData['file_name'] = date('Y-m-d_H-i-s_') . 'export_customer.csv';
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertEquals('Pending',
        $this->importExportScheduledHelper()->getLastOutcome(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        ),'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importData, $csv);
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        //Import Product
        $importData1 = $this->loadDataSet('ImportExportScheduled','scheduled_import',
            array(
                'entity_type' => 'Products',
                'behavior' => 'Append Complex Data'
            ));
        $importData1['file_name'] = date('Y-m-d_H-i-s_') . 'export_catalog_product.csv';
        $this->importExportScheduledHelper()->createImport($importData1);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertEquals('Pending',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData1['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importData1, $csv);
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData1['name'],
                'operation' => 'Import'
            )
        );
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importData1['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData1['name'],
                'operation' => 'Import'
            )
        );
    }
}