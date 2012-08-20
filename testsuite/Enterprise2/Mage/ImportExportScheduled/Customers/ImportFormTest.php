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
 * Scheduled Import Form Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExportScheduled_ImportForm_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * Precondition:
     * Delete all existing imports/exports
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->admin('scheduled_import_export');
        if ($this->importExportScheduledHelper()->isImportExportPresentInGrid(array('operation' => 'Export')) ||
            $this->importExportScheduledHelper()->isImportExportPresentInGrid(array('operation' => 'Import'))) {
            $this->clickControl('link', 'selectall', false);
            $this->fillDropdown('grid_massaction_select', 'Delete');
            $this->clickButtonAndConfirm('submit', 'delete_confirmation');
        }
    }

    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('scheduled_import_export');
    }

    /**
     * Adding new Scheduled Import (Customers)
     * Steps:
     * 1. Go to System > Import/Export > Scheduled Import/Export
     * 2. Click “Add Scheduled Import” button
     * 3. Fill all values (Entity Type: Customers Main File, Import Behavior: Add/Update Complex Data)
     * 4. Click 'Save' button
     * Expected: scheduled Import/Export page is opened. Message “The scheduled import has been saved”
     * in green frame has appeared. Created Scheduled Import is available in grid.
     * 5. Open created Scheduled Import from grid.
     * Expected: edit Scheduled Import page is opened. Scheduled Import form is filled correctly.
     * 6. Go back to Scheduled Import/Export page. Click “Add Scheduled Import” button. Repeat step 3
     * 7. Click 'Reset' button
     * Expected: new Scheduled Import form is empty
     * 8. Repeat step 3
     * 9. Click 'Back' button
     * Expected: scheduled Import/Export page is opened. Scheduled Import is not saved.
     *
     * @test
     * @TestlinkId TL-MAGE-5765
     */
    public function addingNewScheduledImport()
    {
        //Steps 2-4
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers Main File',
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
            'entity_type' => 'Customers Main File',
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
     * Editing existing Scheduled Import (Customers)
     * Precondition: one Scheduled Import for customers is created</p
     * Steps:
     * 1. Go to System > Import/Export > Scheduled Import/Export
     * 2. Open Scheduled Import from precondition.
     * Expected: The page "Edit Scheduled Import" is opened.
     * 3. Select 'Delete Entities' parameter in 'Import Behavior' dropdown field.
     * 4. Select 'Customer Addresses' parameter in 'Customer Entity Type' dropdown field.
     * 5. Click 'Save' button
     * Expected: scheduled Import/Export page is opened. Message “The scheduled import has been saved”
     * in green frame has appeared.
     * 6. Open Scheduled Import from grid.
     * Expected: edit Scheduled Import page is opened. Scheduled Import form is updated with last changes.
     * 7. Select 'Custom Action' parameter in 'Import Behavior' dropdown field.
     * 8. Select 'Customer Finances' parameter in 'Customer Entity Type' dropdown field.
     * 9. Click 'Reset' button
     * Expected: 'Import Behavior' field value is 'Delete Entities'.
     * 'Customer Entity Type' field value is 'Customer Addresses'.
     * 10. Repeat steps 7-8
     * 11. Click 'Back' button
     * Expected: Scheduled Import/Export page is opened.
     * 12. Open Scheduled Import from precondition.
     * Expected: 'Import Behavior' field value is 'Delete Entities'.
     * 'Customer Entity Type' field value is 'Customer Addresses'.
     * Expected: The page "Edit Scheduled Import" is opened.
     * 13. Go back to Scheduled Import/Export page. Choose 'Edit' in column 'Action' for import from precondition.
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
        $this->fillDropdown('entity_type', 'Customer Addresses');
        //Step 5
        $this->clickButton('save');
        $importData['behavior'] = 'Delete Entities';
        $importData['entity_type'] = 'Customer Addresses';
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
        $this->fillDropdown('entity_type', 'Customer Finances');
        //Step 9
        $this->clickButton('reset');
        //Verifying
        $this->checkCurrentPage('scheduled_importexport_edit');
        $this->verifyForm($importData);
        //Step 10
        $this->fillDropdown('behavior', 'Custom Action');
        $this->fillDropdown('entity_type', 'Customer Finances');
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
     * Deleting Scheduled Import (Customers)
     * Precondition: one Scheduled Import for customers is created</p
     * Steps:
     * 1. In System > Import/Export > Scheduled Import/Export select Scheduled Import from precondition
     * 2. Press "Delete" button
     * Expected:  Prompt with text "Are you sure you want to delete this scheduled export?"
     * 3. Press "OK" button in the dialog box
     * Expected: the page "Scheduled Import/Export" is opened. Import from precondition is absent in the grid.
     * The message "The scheduled import has been deleted." is appeared in the top area.
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
     * Deleting Scheduled Imports/Exports (through action)
     * Precondition: three Scheduled Imports/Exports for customers is created</p
     * Steps:
     * 1. In System > Import/Export > Scheduled Import/Export select one Scheduled Import/Export from precondition
     * 2. In "Actions" drop-down select "Delete"
     * 3. Press "Submit" button
     * Expected: Prompt with text "Are you sure you want to delete the selected scheduled imports/exports?".
     * 4. Press "OK" button.
     * Expected: The message "Total of 1 record(s) have been deleted" is appeared in the top area.
     * Import/Export from precondition is absent in the grid.
     * 5. Repeat steps 1-3 for other 2 imports/exports
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
                'entity_type' => 'Customers Main File',
                'behavior' => 'Add/Update Complex Data',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
                'entity_type' => 'Customers Main File',
                'behavior' => 'Delete Entities',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )), $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
                'entity_type' => 'Customers Main File',
                'behavior' => 'Custom Action',
                'file_name' => date('Y-m-d_H-i-s_') . 'export_customer.csv',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'entity_type' => 'Customers Main File',
            )))),
            array(array($this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'entity_type' => 'Customers Main File',
            )),
                $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                    'entity_type' => 'Customers Main File',
                ))),
            ));
    }
}