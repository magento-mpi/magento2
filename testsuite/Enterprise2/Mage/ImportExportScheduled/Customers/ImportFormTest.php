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
 * Scheduled Import Form Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @method Enterprise2_Mage_ImportExportScheduled_Helper  importExportScheduledHelper() importExportScheduledHelper()
 */
class Enterprise2_Mage_ImportExportScheduled_ImportForm_CustomerTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('scheduled_import_export');
    }

    /**
     * <p>Adding new Scheduled Import (Customers)</p>
     * <p>Steps:</p>
     * <p>1. Go to System > Import/Export > Scheduled Import/Export</p>
     * <p>2. Click “Add Scheduled Import” button</p>
     * <p>3. Fill all values (Entity Type: Customers, Import Format Version: Magento 2.0 format, Import Behavior: Add/Update Complex Data)</p>
     * <p>4. Click 'Save' button</p>
     * <p>Expected: scheduled Import/Export page is opened. Message “The scheduled import has been saved”</p>
     * <p>in green frame has appeared. Created Scheduled Import is available in grid.</p>
     * <p>5. Open created Scheduled Import from grid.</p>
     * <p>Expected: edit Scheduled Import page is opened. Scheduled Import form is filled correctly.</p>
     * <p>6. Go back to Scheduled Import/Export page. Click “Add Scheduled Import” button. Repeat step 3</p>
     * <p>7. Click 'Reset' button</p>
     * <p>Expected: new Scheduled Import form is empty</p>
     * <p>8. Repeat step 3</p>
     * <p>9. Click 'Back' button</p>
     * <p>Expected: scheduled Import/Export page is opened. Scheduled Import is not saved.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5765
     */
    public function addingNewScheduledImport()
    {
        //Steps 2-4
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
        ));
        $this->importExportScheduledHelper()->createImport($importData);
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->assertTrue($this->importExportScheduledHelper()->isImportExportPresentInGrid(array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )),
            'Scheduled Import not found in grid');
        //Step 5
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )
        );
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
        $this->verifyForm($importData);
        //Step 6
        $tempImportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
        ));
        $this->admin('scheduled_import_export');
        $this->clickButton('add_scheduled_import');
        $this->fillForm($tempImportData);
        //Step 7
        $this->clickButton('reset');
        //Verifying
        $emptyImportData = array(
            'name' => '',
            'description' => '',
            'entity_type' => '-- Please Select --',
            'server_type' => 'Local Server',
            'file_path' => '',
            'file_name' => '',

        );
        $this->verifyForm($emptyImportData);
        //Step 8
        $this->fillForm($tempImportData);
        //Step 9
        $this->clickButton('back');
        //Verifying
        $this->assertFalse($this->importExportScheduledHelper()->isImportExportPresentInGrid(array(
                'name' => $tempImportData['name'],
                'operation' => 'Import',
            )),
            'Scheduled Import is found in grid');

        return $importData;
    }

    /**
     * <p>Editing existing Scheduled Import (Customers)</p>
     * <p>Precondition: one Scheduled Import for customers is created</p
     * <p>Steps:</p>
     * <p>1. Go to System > Import/Export > Scheduled Import/Export</p>
     * <p>2. Open Scheduled Import from precondition.</p>
     * <p>Expected: The page "Edit Scheduled Import" is opened.</p>
     * <p>3. Select 'Delete Entities' parameter in 'Import Behavior' dropdown field.</p>
     * <p>4. Select 'Customer Addresses' parameter in 'Customer Entity Type' dropdown field.</p>
     * <p>5. Click 'Save' button</p>
     * <p>Expected: scheduled Import/Export page is opened. Message “The scheduled import has been saved”</p>
     * <p>in green frame has appeared.</p>
     * <p>6. Open Scheduled Import from grid.</p>
     * <p>Expected: edit Scheduled Import page is opened. Scheduled Import form is updated with last changes.</p>
     * <p>7. Select 'Custom Action' parameter in 'Import Behavior' dropdown field.</p>
     * <p>8. Select 'Customer Finances' parameter in 'Customer Entity Type' dropdown field.</p>
     * <p>9. Click 'Reset' button</p>
     * <p>Expected: 'Import Behavior' field value is 'Delete Entities'.</p>
     * <p>'Customer Entity Type' field value is 'Customer Addresses'. </p>
     * <p>10. Repeat steps 7-8</p>
     * <p>11. Click 'Back' button</p>
     * <p>Expected: Scheduled Import/Export page is opened.</p>
     * <p>12. Open Scheduled Import from precondition.</p>
     * <p>Expected: 'Import Behavior' field value is 'Delete Entities'.</p>
     * <p>'Customer Entity Type' field value is 'Customer Addresses'. </p>
     * <p>13. Go back to Scheduled Import/Export page. Choose “Edit” in column “Action” for import from precondition.</p>
     * <p>Expected: The page "Edit Scheduled Import" is opened.</p>
     *
     * @test
     * @depends addingNewScheduledImport
     * @TestlinkId TL-MAGE-5777,5782
     */
    public function editingScheduledImport($importData)
    {
        //Step 2
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )
        );
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
        //Step 3
        $this->fillDropdown('behavior', 'Delete Entities');
        //Step 4
        $this->fillDropdown('entity_subtype', 'Customer Addresses');
        //Step 5
        $this->clickButton('save');
        $importData['behavior'] = 'Delete Entities';
        $importData['entity_subtype'] = 'Customer Addresses';
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_saved_import');
        //Step 6
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )
        );
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
        $this->verifyForm($importData);
        //Step 7
        $this->fillDropdown('behavior', 'Custom Action');
        //Step 8
        $this->fillDropdown('entity_subtype', 'Customer Finances');
        //Step 9
        $this->clickButton('reset');
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
        $this->verifyForm($importData);
        //Step 10
        $this->fillDropdown('behavior', 'Custom Action');
        $this->fillDropdown('entity_subtype', 'Customer Finances');
        //Step 11
        $this->clickButton('back');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        //Step 12
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )
        );
        //Verifying
        $this->verifyForm($importData);
        //Step 13
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'
            ), 'Edit'
        );
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
    }

    /**
     * <p>Deleting Scheduled Import (Customers)</p>
     * <p>Precondition: one Scheduled Import for customers is created</p
     * <p>Steps:</p>
     * <p>1. In System > Import/Export > Scheduled Import/Export select Scheduled Import from precondition</p>
     * <p>2. Press "Delete" button</p>
     * <p>Expected:  Prompt with text "Are you sure you want to delete this scheduled export?"</p>
     * <p>3. Press "OK" button in the dialog box</p>
     * <p>Expected: the page "Scheduled Import/Export" is opened. Import from precondition is absent in the grid.</p>
     * <p>The message "The scheduled import has been deleted." is appeared in the top area.</p>
     *
     * @test
     * @depends addingNewScheduledImport
     * @TestlinkId TL-MAGE-5785
     */
    public function deletingScheduledImport($importData)
    {
        //Step 1
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )
        );
        //Step 2
        $this->clickButtonAndConfirm('delete', 'delete_confirmation_import');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_delete_import');
        $this->assertFalse($this->importExportScheduledHelper()->isImportExportPresentInGrid(array(
                'name' => $importData['name'],
                'operation' => 'Import',
            )),
            'Scheduled Import is found in grid');
    }

    /**
     * <p>Deleting Scheduled Imports/Exports (through action)</p>
     * <p>Precondition: three Scheduled Imports/Exports for customers is created</p
     * <p>Steps:</p>
     * <p>1. In System > Import/Export > Scheduled Import/Export select one Scheduled Import/Export from precondition</p>
     * <p>2. In "Actions" drop-down select "Delete"</p>
     * <p>3. Press "Submit" button</p>
     * <p>Expected: Prompt with text "Are you sure you want to delete the selected scheduled imports/exports?".</p>
     * <p>4. Press "OK" button.</p>
     * <p>Expected: The message "Total of 1 record(s) have been deleted" is appeared in the top area.</p>
     * <p>Import/Export from precondition is absent in the grid.</p>
     * <p>5. Repeat steps 1-3 for other 2 imports/exports</p>
     *
     * @test
     * @dataProvider massActionDelete
     * @TestlinkId TL-MAGE-5788, 5787, 5775, 5807
     */
    public function deletingScheduledImportsExportsThroughAction($data)
    {
        //Precondition
        foreach ($data as $value) {
            if (strstr($value['name'], 'import')) {
                $this->importExportScheduledHelper()->createImport($value);
                $this->assertMessagePresent('success', 'success_saved_import');
            } else {
                $this->importExportScheduledHelper()->createExport($value);
                $this->assertMessagePresent('success', 'success_saved_export');
            }
        }
        //Step 1
        foreach ($data as $value) {
            $this->importExportScheduledHelper()->searchAndChoose(array(
                'name' => $value['name'],
            ));
        }
        //Step 2
        $this->fillDropdown('grid_massaction_select', 'Delete');
        //Steps 3-4
        $this->clickButtonAndConfirm('submit', 'delete_confirmation');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyDeletedRecords', count($data));
        $this->assertMessagePresent('success', 'success_delete_records');
        foreach ($data as $value) {
            $this->assertFalse($this->importExportScheduledHelper()->isImportExportPresentInGrid(array(
                'name' => $value['name'],
            )), 'Scheduled Import/Export is found in the grid');
        }
    }

    public function massActionDelete()
    {
        return array(
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
                'file_format_version' => 'Magento 2.0 format',
                'behavior' => 'Add/Update Complex Data',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
                'file_format_version' => 'Magento 2.0 format',
                'behavior' => 'Delete Entities',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )), $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
                'file_format_version' => 'Magento 2.0 format',
                'behavior' => 'Custom Action',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'file_format_version' => 'Magento 2.0 format',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'file_format_version' => 'Magento 2.0 format',
            )), $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'file_format_version' => 'Magento 2.0 format',
            ))),
            ));
    }
}