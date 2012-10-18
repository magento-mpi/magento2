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
class Enterprise2_Mage_ImportExportScheduled_ExportForm_CustomerTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->navigate('scheduled_import_export');
    }

    /**
     * Adding new Scheduled Export
     * Steps:
     * 1. Press "Add Scheduled Export" button in System > Import/Export > Scheduled Import/Export.
     * 2. Select entity type "Customers Main File"
     * 3. Fill all other fields
     * 4. Click "Reset" button
     * 5. Repeat steps 2-5
     * 6. Click "Back" button
     * 7. Repeat steps 1-5
     * 8. Press "Save" button
     * Result1: After step 6 the creation form should be cleared
     * Result2: After step 8 the grid "Scheduled Import/Export" is opened, new export isn't created
     * Result3: After step 10 the grid "Scheduled Import/Export" is opened, new export is created
     * @test
     * @TestlinkId TL-MAGE-5769
     */
    public function addingNewScheduledExport()
    {
        // Step 1
        $this->assertTrue($this->buttonIsPresent('add_scheduled_export'),
                          'Button "Add Scheduled Export" is absent in current page');
        $this->addParameter('type', 'Export');
        $this->clickButton('add_scheduled_export');
        // Verify
        $this->assertTrue($this->checkCurrentPage('scheduled_importexport_add'));
        // Step 2
        $this->fillDropdown('entity_type', 'Customers Main File');
        // Step 3
        $this->fillField('name', 'test_name_export');
        $this->fillField('description', 'test_description_export');
        $this->fillDropdown('server_type', 'Local Server');
        $this->fillField('file_path', 'var/export');
        // Step 4
        $this->clickButton('reset');
        // Result
        $this->assertTrue($this->checkCurrentPage('scheduled_importexport_add'));
        $emptyExportData = array(
            'name' => '',
            'description' => '',
            'entity_type' => '-- Please Select --',
            'server_type' => 'Local Server',
            'file_path' => '',
        );
        $this->verifyForm($emptyExportData);

        // Step 5
        $this->fillDropdown('entity_type', 'Customer Addresses');
        $this->fillField('name', 'test_name_export');
        $this->fillField('description', 'test_description_export');
        $this->fillDropdown('server_type', 'Local Server');
        $this->fillField('file_path', 'var/export');
        // Step 6
        $this->clickButton('back');
        $this->assertTrue($this->checkCurrentPage('scheduled_import_export'), 'The grid is not appeared');
        // Step 1, 2, 3, 4 ,5
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances'));
        $this->importExportScheduledHelper()->createExport($exportData);
        // Step 10
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            )
        );
    }

    /**
     * Editing new Scheduled Export
     * Preconditions:
     * The Scheduled export for new export format with entity subtype "Customers Main File" exists
     * Steps:
     * 1. Search and open export from precondition in System > Import/Export > Scheduled Import/Export.
     * 2. Select "Customer Addresses" type, edit info in all others fields all other fields
     * 3. Click "Save" button
     * 4. Open this export
     *  Result: The  changed info is saved
     * 5.Select "Customer Finances" entity subtype, edit info in all others fields again
     * 6. Press "Save" button
     * Result: The changes should be applied
     * @test
     * @TestlinkId TL-MAGE-5770
     */
    public function editingNewScheduledExport()
    {
        // Precondition
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customers Main File'));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        //Step 1
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        // Step 2
        $this->fillField('name', 'Edit_Export_Name_1078769789');
        $this->fillField('description', 'Edit_Export_Description');
        $this->fillDropdown('entity_type', 'Customer Addresses');
        $this->fillDropdown('frequency', 'Monthly');
        $this->fillField('file_path', 'test/directory');
        // Step 3
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_export');
        // Step 4
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => 'Edit_Export_Name_1078769789',
                'operation' => 'Export',
            )
        );
        // Verifying
        $updateExportData = array(
            'name' => 'Edit_Export_Name_1078769789',
            'description' => 'Edit_Export_Description',
            'entity_type' => 'Customer Addresses',
            'frequency' => 'Monthly',
            'file_path' => 'test/directory'
        );
        $this->verifyForm($updateExportData);
        // Step 5
        $this->fillField('name', 'Edit_Export_Name_again');
        $this->fillField('description', 'Edit_Export_Description_again');
        $this->fillDropdown('entity_type', 'Customer Finances');
        $this->fillDropdown('frequency', 'Weekly');
        $this->fillField('file_path', 'test/directory/again');
        // Step 6
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_export');
        // Verifying
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => 'Edit_Export_Name_again',
                'operation' => 'Export',
            )
        );
        $updateExportData = array(
            'name' => 'Edit_Export_Name_again',
            'description' => 'Edit_Export_Description_again',
            'entity_type' => 'Customer Finances',
            'frequency' => 'Weekly',
            'file_path' => 'test/directory/again'
        );
        $this->verifyForm($updateExportData);
    }

    /**
     * Editing new Scheduled Export
     * Preconditions:
     * The Scheduled export for new export format with entity subtype "Customers Main File" exists
     * Steps:
     * 1. Search export from precondition in System > Import/Export > Scheduled Import/Export.
     * 2. In column "Action"  select "Edit"
     *  Result: The  edit page of export from precondition is opened
     * @test
     * @TestlinkId TL-MAGE-5771
     */
    public function editingNewScheduledExportThroughAction()
    {
        // Precondition
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customers Main File'));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Step 1, 2
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export'
            ), 'Edit'
        );
        // Verifying
        $this->assertTrue($this->checkCurrentPage('scheduled_importexport_edit'), 'Edit page is not opened');
        unset($exportData['password']);
        $this->assertTrue($this->verifyForm($exportData), 'Export Data is incorrect');
    }

    /**
     * Deleting new Scheduled Export
     * Preconditions:
     * The Scheduled export for new export format with entity subtype "Customers Main File" exists
     * Steps:
     * 1. Search and open export from precondition in System > Import/Export > Scheduled Import/Export.
     * 2. Press "Delete" button
     * 3. Press "OK" in appearing dialog box
     *  Result: The grid Scheduled Import/Export is appeared with message about deleting.
     * @test
     * @TestlinkId TL-MAGE-5773
     */
    public function deletingScheduledExport()
    {
        // Precondition
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customers Main File'));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Step 1, 2
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        $this->clickButtonAndConfirm('delete', 'delete_confirmation_export');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_delete_export');
        $this->assertFalse($this->importExportScheduledHelper()->isImportExportPresentInGrid(array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )),
            'Scheduled Export is found in grid');
    }
}