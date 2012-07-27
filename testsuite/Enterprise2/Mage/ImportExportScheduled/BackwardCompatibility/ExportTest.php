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
     * <p> Running Scheduled Export </p>
     * <p> Precondition: </p>
     * <p> 1. Two Old Scheduled Exports for customers and for products are created </p>
     * <p> Steps: </p>
     * <p> 1. In System->Import/Export->Scheduled Import Export select export for customers </p>
     * <p> 2. Select "Run" in "Action" column </p>
     * <p> Expected Result: "Last Outcome" changes from "Pending" to "Successful", message about successful operation is appeared </p>
     * <p> 3. In System->Import/Export->Scheduled Import Export select export for products </p>
     * <p> 4. Select "Run" in "Action" column </p>
     * <p> Expected Result: "Last Outcome" changes from "Pending" to "Successful", message about successful operation is appeared</p>
     * @test
     * TL-MAGE-1499
     */
    public function simpleScheduledExport()
    {
        // Export Customer
        $this->navigate('scheduled_import_export');
        $exportDataCustomers = $this->loadDataSet('ImportExportScheduled','scheduled_export');
        $this->importExportScheduledHelper()->createExport($exportDataCustomers);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run export
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportDataCustomers['name'],
                'operation' => 'Export'
            )
        );
        //Verifying
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportDataCustomers['name'],
                    'operation' => 'Export'
                )
            ),'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
        //get file
        $exportDataCustomers['file_name'] = $this->importExportScheduledHelper()->
            getFilePrefix(
            array(
                'name' => $exportDataCustomers['name'],
                'operation' => 'Export'
            )
        );
        $exportDataCustomers['file_name'] .= 'export_customer.csv';
        $csv['customers'] = $this->importExportScheduledHelper()->getCsvFromFtp($exportDataCustomers);
        // Export Product
        $exportDataProducts = $this->loadDataSet('ImportExportScheduled','scheduled_export',
            array(
                'entity_type' => 'Products'
            ));
        $this->importExportScheduledHelper()->createExport($exportDataProducts);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run export
        $this->navigate('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportDataProducts['name'],
                'operation' => 'Export'
            )
        );
        //Verifying
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $exportDataProducts['name'],
                    'operation' => 'Export'
                )
            ),'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
        //get file
        $exportDataProducts['file_name'] = $this->importExportScheduledHelper()->
            getFilePrefix(
            array(
                'name' => $exportDataProducts['name'],
                'operation' => 'Export'
            )
        );
        $exportDataProducts['file_name'] .= 'export_catalog_product.csv';
        $csv['products'] = $this->importExportScheduledHelper()->getCsvFromFtp($exportDataProducts);
        return $csv;

    }
    /**
     * Running Scheduled Import
     * <p> Precondition: </p>
     * <p> 1. Two old Scheduled Imports are created for customers and products </p>
     * <p> "Steps:" </p>
     * <p> 1. In System > Import/Export > Scheduled Import/Export select import for customers </p>
     * <p> 2. in "Action" column select "Run" </p>
     * <p> Expected Result: "Last Outcome" changes from "Pending" to "Successful", message about successful operation is appeared </p>
     * <p> 3. In System > Import/Export > Scheduled Import/Export select import for products </p>
     * <p> in "Action" column select "Run" </p>
     * <p> Expected Result: "Last Outcome" changes from "Pending" to "Successful", message about successful operation is appeared </p>
     * @depends simpleScheduledExport
     * @test
     * TL-MAGE-1528
     */
    public function simpleScheduledImport($csv)
    {
        // Import Customer
        $importDataCustomers = $this->loadDataSet('ImportExportScheduled','scheduled_import',
            array('file_format_version' => 'Magento 1.7 format',
                'behavior'  => 'Append Complex Data'));
        $importDataCustomers['file_name'] = date('Y-m-d_H-i-s_') . 'export_customer.csv';
        $this->importExportScheduledHelper()->createImport($importDataCustomers);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertEquals('Pending',
        $this->importExportScheduledHelper()->getLastOutcome(
            array(
                'name' => $importDataCustomers['name'],
                'operation' => 'Import'
            )
        ),'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importDataCustomers, $csv['customers']);
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importDataCustomers['name'],
                'operation' => 'Import'
            )
        );
        //Verifying import
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importDataCustomers['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
        //Import Product
        $importDataProducts = $this->loadDataSet('ImportExportScheduled','scheduled_import',
            array(
                'entity_type' => 'Products',
                'behavior' => 'Append Complex Data'
            ));
        $importDataProducts['file_name'] = date('Y-m-d_H-i-s_') . 'export_catalog_product.csv';
        $this->importExportScheduledHelper()->createImport($importDataProducts);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertEquals('Pending',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importDataProducts['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        //upload file to ftp
        $this->importExportScheduledHelper()->putCsvToFtp($importDataProducts, $csv['products']);
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importDataProducts['name'],
                'operation' => 'Import'
            )
        );
        //Verifying import
        $this->assertEquals('Successful',
            $this->importExportScheduledHelper()->getLastOutcome(
                array(
                    'name' => $importDataProducts['name'],
                    'operation' => 'Import'
                )
            ),'Error is occurred');
        $this->assertMessagePresent('success', 'success_run');
    }
}