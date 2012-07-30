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
class Enterprise2_Mage_ImportExportScheduled_ExportStatusFilterTest_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Precondition:</p>
     * <p>Delete all existing imports/exports</p>
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
     * <p>Scheduled Export statuses</p>
     * <p>Steps: </p>
     * <p>1. Create new export with status "Disable"</p>
     * <p>Result: Export was saved with the status "Disable"</p>
     * <p>2. Edit Export - change status to "Enable"</p>
     * <p>Result: The changes was saved with "Enable" status</p>
     * <p>3. Select this Export in grid</p>
     * <p>4. Change status to "Disable" with a help "Actions"</p>
     * <p>Result: Status is changed to "Disable"</p>
     * <p>5. Select this Export in grid</p>
     * <p>6. Create new import with status disabled</p>
     * <p>7. Change status to "Enabled" with a help "Actions for both exports"</p>
     * <p> Result: Status is changed to "Enabled" for both exports</p>
     * @test
     * @TestlinkId TL-MAGE-5816
     */
    public function scheduledExportStatuses()
    {
        // Step 1
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances',
                'status' => 'Disabled'
            ));
        $this->importExportScheduledHelper()->createExport($exportData);
        // Verify
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        $updateExportData = array(
            'status' => 'Disabled',
        );
        $this->verifyForm($updateExportData);
        // Step 2
        $this->fillDropdown('status', 'Enabled');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_export');
        // Verifying
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        $updateData = array(
            'status' => 'Enabled',
        );
        $this->verifyForm($updateData);
        // Step 3
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData['name'],
            'operation' => 'Export',
        ));
        $exportRecordsCount = 1;
        // Step 4
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Disabled');
        $this->clickButton('submit');
        // Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', count($exportRecordsCount));
        $this->assertMessagePresent('success', 'success_update_status');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        $actionExportData = array(
            'status' => 'Disabled',
        );
        $this->verifyForm($actionExportData);
        // Step 5
        $this->admin('scheduled_import_export');
        $exportData1 = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances',
                'status' => 'Disabled'
            ));
        $this->importExportScheduledHelper()->createExport($exportData1);
        // Verify
        $this->checkCurrentPage('scheduled_import_export');
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export',
            )
        );
        $exportRecordsCount1 = 2;
        $updateExportData1 = array(
            'status' => 'Disabled',
        );
        $this->verifyForm($updateExportData1);
        // Step 6
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
                'name' => $exportData['name'],
                'operation' => 'Export',
                'status' => 'Disabled',
            )
        );
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData1['name'],
            'operation' => 'Export',
        ));
        // Step7
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Enabled');
        $this->clickButton('submit');
        //Verifying first import
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', $exportRecordsCount1);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData['name'],
                'operation' => 'Export',
            )
        );
        $exportData = array(
            'status' => 'Enabled',
        );
        $this->verifyForm($exportData);
        // Verifying second import
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export',
            )
        );
        $exportData1 = array(
            'status' => 'Enabled',
        );
        $this->verifyForm($exportData1);
    }
    /**
     * <p>Scheduled Export statuses</p>
     * <p> Create Product Export in System-> Import/Export-> Scheduled Import/Export</p>
     * <p> Create Customer Export with keyword 'test' in the name</p>
     * <p> Create another New Customer Export with keyword 'test' in the name</p>
     * <p> Create another New Customer Export with another name</p>
     * <p> All Exports have different 'entity subtype', 'status', 'frequency','last run date'</p>
     * <p> Steps: </p>
     * <p>1. On 'Scheduled Import/Export' page in filter 'Entity Type' select 'Products' and press 'Search'</p>
     * <p> Result: Only 'product Exports' should be displayed in the grid</p>
     * <p>2. in filter 'Entity Type' select Customers Main File entity type and press 'Search'</p>
     * <p>Result: Only 'customer Exports' should be displayed in the grid</p>
     * <p>Repeat step 2 for Customer Addresses and Customer Finances entity types</p>
     * <p>3. Select 'Daily' frequency and press 'Search'</p>
     * <p> Result: Only the Exports with frequency 'Daily' are displayed in the grid</p>
     * <p>4. Select 'Weekly' frequency and press 'Search'</p>
     * <p> Result: Only the Exports with frequency 'Weekly' are displayed in the grid</p>
     * <p>5. Select 'Monthly' frequency and press 'Search'</p>
     * <p> Result: Only the Exports with frequency 'Monthly' are displayed in the grid</p>
     * <p>6. In the filter select 'Disabled' status and press 'Search'</p>
     * <p>Result: Only the Exports with status 'Disabled' are displayed in the grid</p>
     * <p>7. In the filter select 'Enabled' status and press 'Search'</p>
     * <p>Result: Only the Exports with status 'Enabled' are displayed in the grid</p>
     * <p>8. In the filter 'Last Outcome' select 'Pending' and press 'Search'</p>
     * <p>Result: Only Pending Exports  are displayed in the grid</p>
     * <p>9. In the filter 'Last Outcome' select 'Successful' and press 'Search'</p>
     * <p>Result: Only Successful Exports  are displayed in the grid</p>
     * <p>10. In the filter 'Last Outcome' select 'Failed' and press 'Search'</p>
     * <p>Result: Only Failed Exports  are displayed in the grid</p></p>
     * <p>11. Enter in the grid proper date to the fields 'From' and 'To'</p>
     * <p>Result: Only Exports with this last run date  are displayed in the grid</p>
     * <p>12. In grid in the field 'Name' enter 'test' and press 'Search' button</p>
     * <p>Result: Only Exports which have the key 'test' in the name are displayed in the grid </p>
     * @test
     *
     * @TestlinkId TL-MAGE-5817
     */
    public function scheduledExportSearchByFilter()
    {
        //Preconditions:
        // Create product export
        $exportDataProducts = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'entity_type' => 'Products',
                'status' => 'Disabled',
                'frequency' => 'Weekly',
            ));
        $this->importExportScheduledHelper()->createExport($exportDataProducts);
        $this->assertMessagePresent('success', 'success_saved_export');

        // Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportDataAddresses = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'name' => 'Team_B_Export_2',
                'entity_type' => 'Customer Addresses',
                'status' => 'Enabled',
                'frequency' => 'Monthly',
            ));
        $this->importExportScheduledHelper()->createExport($exportDataAddresses);
        $this->assertMessagePresent('success', 'success_saved_export');

        // Create New  Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportDataMain = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'name' => 'Team_B_Export_3',
                'entity_type' => 'Customers Main File',
                'status' => 'Disabled',
                'frequency' => 'Daily',
            ));
        $this->importExportScheduledHelper()->createExport($exportDataMain);
        $this->assertMessagePresent('success', 'success_saved_export');

        // Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportDataFinances = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
                'entity_type' => 'Customer Finances',
                'status' => 'Enabled',
                'frequency' => 'Monthly',
                'user_name' => 'not_exist'
            ));
        $this->importExportScheduledHelper()->createExport($exportDataFinances);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run operation
        $this->importExportScheduledHelper()->applyAction(array(
                'name' => $exportDataFinances['name'],
                'operation' => 'Export'
            )
        );
        $this->assertMessagePresent('error', 'error_run');

        // Step 1, 2
        // Verifying filter by Products entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataProducts['name'],
                'entity_type' => 'Customers'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataProducts['name'],
                'entity_type' => 'Products'
            )
        ));

        // Verifying filter by Customers Main File entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataMain['name'],
                'entity_type' => 'Customer Addresses'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataMain['name'],
                'entity_type' => 'Customer Finances'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataMain['name'],
                'entity_type' => 'Customers Main File'
            )
        ));

        // Verifying filter by Customer Addresses entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataAddresses['name'],
                'entity_type' => 'Customers Main File'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataAddresses['name'],
                'entity_type' => 'Customer Finances'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataAddresses['name'],
                'entity_type' => 'Customer Addresses'
            )
        ));

        // Verifying filter by Customer Finances entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'entity_type' => 'Customers Main File'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'entity_type' => 'Customer Addresses'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'entity_type' => 'Customer Finances'
            )
        ));

        // Step 3
        $arr = array($exportDataProducts, $exportDataAddresses, $exportDataFinances);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataMain['name'],
                'frequency' => 'Daily'
            )
        ));
        $this->admin('scheduled_import_export');
        // Step 4
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataProducts['name'],
                'frequency' => 'Weekly'
            )
        ));
        $this->admin('scheduled_import_export');
        $data[0]['name'] = $exportDataAddresses['name'];
        $data[1]['name'] = $exportDataMain['name'];
        $data[2]['name'] = $exportDataFinances['name'];
        foreach ($data as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Weekly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 5
        $arr = array($exportDataAddresses, $exportDataFinances);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportDataProducts, $exportDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 6
        $arr = array($exportDataProducts, $exportDataMain);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($exportDataAddresses, $exportDataFinances);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 7
        $arr = array($exportDataProducts, $exportDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($exportDataAddresses, $exportDataFinances);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 8, 9, 10
        $arr = array($exportDataProducts, $exportDataAddresses, $exportDataMain);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Pending',
                )
            ));
            $this->admin('scheduled_import_export');
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Failed',
                )
            ));
            $this->admin('scheduled_import_export');
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Successful',
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'last_outcome' => 'Pending',
            )
        ));
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'last_outcome' => 'Successful',
            )
        ));
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'last_outcome' => 'Failed',
            )
        ));
        $this->admin('scheduled_import_export');
        // Step 11
        $arr = array($exportDataProducts, $exportDataAddresses, $exportDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'date_from' => date("m/d/Y"),
                    'date_to' => date("m/d/Y")
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $exportDataFinances['name'],
                'date_from' => date("m/d/Y"),
                'date_to' => date("m/d/Y")
            )
        ));
        $this->admin('scheduled_import_export');
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => 'Team_B',
                'operation' => 'Export',
            )
        ));
    }
}