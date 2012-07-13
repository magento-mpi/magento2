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
* @method Community2_Mage_ImportExportScheduled_Helper  importExportScheduledHelper() importExportScheduledHelper()
*/
class Community2_Mage_ImportExportScheduled_Backward_Export_CustomerTest extends Mage_Selenium_TestCase
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
        $exportData = $this->loadDataSet('ImportExportScheduled','scheduled_export');
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
    }
    /**
     * Simple Test Scheduled Import
     *
     * @test
     */
    public function simpleScheduledImport()
    {
        $importData = $this->loadDataSet('ImportExportScheduled','scheduled_import');
        $this->importExportScheduledHelper()->createImport($importData);
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertEquals('Pending',
        $this->importExportScheduledHelper()->getLastOutcome(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        ),'Error is occurred');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            )
        );

    }
}