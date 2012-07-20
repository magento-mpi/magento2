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
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customer Finances',
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
            'file_format_version' => 'Magento 2.0 format',
            'entity_subtype' => 'Customer Finances',
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
     * <p> Create old Customer Export with keyword 'test' in the name</p>
     * <p> Create New Customer Export with keyword 'test' in the name</p>
     * <p> Create yet another New Customer Export with keyword 'test' in the name</p>
     * <p> Create yet another New Customer Export with another name</p>
     * <p> All Exports have different 'entity subtype', 'status', 'frequency','last run date'</p>
     * <p> Steps: </p>
     * <p>1. On 'Scheduled Import/Export' page in filter 'Entity Type' select 'Products' and press 'Search'</p>
     * <p> Result: Only 'product Exports' should be displayed in the grid</p>
     * <p>2. in filter 'Entity Type' select 'Customers' and press 'Search'</p>
     * <p>Result: Only 'customer Exports' should be displayed in the grid</p>
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
     * <p>11. In grid select 'entity subtype' 'Customers Main File'</p>
     * <p>Result:Only the Exports with subtype 'Customers Main File' are displayed in the grid</p>
     * <p>12. In grid select 'entity subtype' 'Customer Addresses'</p>
     * <p>Result: Only the Exports with subtype 'Customer Addresses' are displayed in the grid</p>
     * <p>13. In grid select 'entity subtype' 'Customer Finances'</p>
     * <p>Result: Only the Exports with subtype 'Customer Finances' are displayed in the grid</p>
     * <p>14. Enter in the grid proper date to the fields 'From' and 'To'</p>
     * <p>Result: Only Exports with this last run date  are displayed in the grid</p>
     * <p>15. In grid in the field 'Name' enter 'test' and press 'Search' button</p>
     * <p>Result: Only Exports which have the key 'test' in the name are displayed in the grid </p>
     * @test
     *
     * @TestlinkId TL-MAGE-5817
     */
    public function scheduledExportSearchByFilter()
    {
        //Preconditions:
        // Create old product export
        $exportData = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Products',
                'status' => 'Disabled',
                'frequency' => 'Weekly'
            ));
        $this->importExportScheduledHelper()->createExport($exportData);
        $this->assertMessagePresent('success', 'success_saved_export');
        // 2. Create Customer Old Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportData1 = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'name' => 'Team_B_Export_1',
                'file_format_version' => 'Magento 1.7 format',
                'status' => 'Enabled',
                'frequency' => 'Daily'
                ));
        $this->importExportScheduledHelper()->createExport($exportData1);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run operation
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData1['name'],
                'operation' => 'Export'
            )
        );
        $this->assertMessagePresent('error', 'error_run');
        // 3. Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportData2 = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'name' => 'Team_B_Export_2',
                'file_format_version' => 'Magento 2.0 format',
                'entity_subtype' => 'Customer Addresses',
                'status' => 'Enabled',
                'frequency' => 'Monthly'
            ));
        $this->importExportScheduledHelper()->createExport($exportData2);
        $this->assertMessagePresent('success', 'success_saved_export');
        // 4. CreateNew  Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportData3 = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'name' => 'Team_B_Export_3',
                'file_format_version' => 'Magento 2.0 format',
                'entity_subtype' => 'Customers Main File',
                'status' => 'Disabled',
                'frequency' => 'Daily'
            ));
        $this->importExportScheduledHelper()->createExport($exportData3);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportData4 = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'file_format_version' => 'Magento 2.0 format',
                'entity_subtype' => 'Customer Finances',
                'status' => 'Enabled',
                'frequency' => 'Monthly'
            ));
        $this->importExportScheduledHelper()->createExport($exportData4);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run operation
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $exportData4['name'],
                'operation' => 'Export'
            )
        );
        $this->assertMessagePresent('error', 'error_run');

        $data[0]['name'] = $exportData1['name'];
        $data[1]['name'] = $exportData2['name'];
        $data[2]['name'] = $exportData3['name'];
        $data[3]['name'] = $exportData4['name'];

        // Step 1, 2
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData['name'],
                'entity_type' => 'Customers'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData['name'],
                'entity_type' => 'Products'
            )
        ));
        $this->admin('scheduled_import_export');
        foreach ($data as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'entity_type' => 'Customers'
                )
            ));
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'entity_type' => 'Products'
                )
            ));
        }
        // Step 3
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData2, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
        }
        $arr = array($exportData1, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
        }
        // Step 4
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData['name'],
                'frequency' => 'Weekly'
            )
        ));
        foreach ($data as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Weekly'
                )
            ));
        }
        // Step 5
        $this->admin('scheduled_import_export');
        $arr = array($exportData2, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData1, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
        }
        // Step 6
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportData1, $exportData2, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
        }
        // Step 7
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportData1, $exportData2, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
        }
        // Step 8, 9, 10
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData2, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Pending',
                )
            ));
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Failed',
                )
            ));
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Successful',
                )
            ));
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportData1, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Pending',
                )
            ));
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Successful',
                )
            ));
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'last_outcome' => 'Failed',
                )
            ));
        }
        // Step 11 "Customers Main File"
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData3['name'],
                'entity_subtype' => 'Customers Main File'
            )
        ));

        $arr = array($exportData, $exportData1, $exportData2, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'entity_subtype' => 'Customers Main File'
                )
            ));
        }
        // Step 12 'Customer Addresses'
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData2['name'],
                'entity_subtype' => 'Customer Addresses'
            )
        ));

        $arr = array($exportData, $exportData1, $exportData3, $exportData4);
        foreach ($arr as $value)
        {
            $this->admin('scheduled_import_export');
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'entity_subtype' => 'Customer Addresses'
                )
            ));
        }
        // Step 13  'Customer Finances'
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $exportData4['name'],
                'entity_subtype' => 'Customer Finances'
            )
        ));
        $arr = array($exportData, $exportData1, $exportData2, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'entity_subtype' => 'Customer Finances'
                )
            ));
        }
        // Step 14
        $this->admin('scheduled_import_export');
        $arr = array($exportData, $exportData2, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'date_from' => date("d/m/Y"),
                    'date_to' => date("d/m/Y")
                )
            ));
        }
        $this->admin('scheduled_import_export');
        $arr = array($exportData1, $exportData4);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'date_from' => date("d/m/Y"),
                    'date_to' => date("d/m/Y")
                )
            ));
        }
        // Step 15
        $this->admin('scheduled_import_export');
        $arr = array($exportData1, $exportData2, $exportData3);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => 'Team_B',
                    'operation' => $value['operation']
                )
            ));

            $arr = array($exportData, $exportData4);
        }
        foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => 'Team_B',
                    'operation' => $value['operation']
                )
            ));
        }
    }
}