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
    }
    /**
     * Simple Test Scheduled Import
     *
     * @depends simpleScheduledExport
     * @test
     */
    public function simpleScheduledImport($csv)
    {
        $importData = $this->loadDataSet('ImportExportScheduled','scheduled_import',
                                          array('file_format_version' => 'Magento 1.7 format',
                                                'behavior'           => 'Append Complex Data'));
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
    }
}