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
class Enterprise2_Mage_ImportExportScheduled_ExportImportStatusFilterTest_CustomerTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('scheduled_import_export');
    }

    /**
     * <p>Scheduled Import statuses</p>
     * <p>Steps: </p>
     * <p>1. Create new import with status "Disable"</p>
     * <p>Result: Import was saved with the status "Disable"</p>
     * <p>2. Edit Import - change status to "Enable"</p>
     * <p>Result: The changes was saved with "Enable" status</p>
     * <p>3. Select this Import in grid</p>
     * <p>4. Change status to "Disable" with a help "Actions"</p>
     * <p>Result: Status is changed to "Disable"</p>
     * <p>5. Create new import with status disabled</p>
     * <p>6. Choose both impors in the grid </p>
     * <p>7. Change status to "Enabled" with a help "Actions for both imports"</p>
     * <p> Result: Status is changed to "Enabled" for both imports</p>
     * @test
     * @TestlinkId TL-MAGE-5802
     */
public function scheduledImportStatuses()
{
    // Step 1
    $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
        'file_format_version' => 'Magento 2.0 format',
        'behavior' => 'Add/Update Complex Data',
        'file_name' => date('Y-m-d_H-i-s_') . 'import_customer.csv',
        'status' => 'Disabled',
    ));
    $importRecordsCount = 1;

    $this->importExportScheduledHelper()->createImport($importData);
    //Verifying
    $this->checkCurrentPage('scheduled_import_export');
    $this->assertMessagePresent('success', 'success_saved_import');
    $this->importExportScheduledHelper()->openImportExport(
        array(
            'name' => $importData['name'],
            'operation' => 'Import',
        )
    );
    $updateImportData = array(
        'status' => 'Disabled',
    );
    $this->verifyForm($updateImportData);
    // Step 2
    $this->fillDropdown('status', 'Enabled');
    $this->clickButton('save');
    $this->assertMessagePresent('success', 'success_saved_import');
    // Verifying
    $this->importExportScheduledHelper()->openImportExport(
        array(
            'name' => $importData['name'],
            'operation' => 'Import',
        )
    );
    $updateData = array(
        'status' => 'Enabled',
    );
    $this->verifyForm($updateData);
    // Step 3
    $this->admin('scheduled_import_export');
    $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $importData['name'],
            'operation' => 'Import',
        ));
    // Step 4
    $this->fillDropdown('grid_massaction_select', 'Change status');
    $this->fillDropdown('status_visibility', 'Disabled');
    $this->clickButton('submit');
    //Verifying
    $this->checkCurrentPage('scheduled_import_export');
    $this->addParameter('qtyUpdatedRecords', count($importRecordsCount));
    $this->assertMessagePresent('success', 'success_update_status');
    $this->importExportScheduledHelper()->openImportExport(
        array(
            'name' => $importData['name'],
            'operation' => 'Import',
        )
    );
    $actionImportData = array(
        'status' => 'Disabled',
    );
    $this->verifyForm($actionImportData);
    // Step 5
    $this->admin('scheduled_import_export');
    $importDataTwo = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
        'file_format_version' => 'Magento 2.0 format',
        'behavior' => 'Delete Entities',
        'file_name' => date('Y-m-d_H-i-s_') . 'import_1_customer.csv',
        'status' => 'Disabled',
    ));
    $importRecordsCount2 = 2;

    $this->importExportScheduledHelper()->createImport($importDataTwo);
    //Verifying
    $this->checkCurrentPage('scheduled_import_export');
    $this->assertMessagePresent('success', 'success_saved_import');
    $this->importExportScheduledHelper()->openImportExport(
        array(
            'name' => $importDataTwo['name'],
            'operation' => 'Import',
        ));
    $updateImportDataTwo = array(
        'status' => 'Disabled',
    );
    $this->verifyForm($updateImportDataTwo);
    // Step 6
    $this->admin('scheduled_import_export');
    $this->importExportScheduledHelper()->searchAndChoose(array(
        'name' => $importData['name'],
        'operation' => 'Import',
        'status' => 'Disabled',
        )
    );
        $this->importExportScheduledHelper()->searchAndChoose(array(
        'name' => $importDataTwo['name'],
        'operation' => 'Import',
    ));
    // Step 7
    $this->fillDropdown('grid_massaction_select', 'Change status');
    $this->fillDropdown('status_visibility', 'Enabled');
    $this->clickButton('submit');
    //Verifying first import
    $this->checkCurrentPage('scheduled_import_export');
    $this->addParameter('qtyUpdatedRecords', $importRecordsCount2);
    $this->assertMessagePresent('success', 'success_update_status');
    $this->importExportScheduledHelper()->openImportExport(
        array(
            'name' => $importData['name'],
            'operation' => 'Import',
        )
    );
    $importData = array(
        'status' => 'Enabled',
    );
    $this->verifyForm($importData);
    // Verifying second import
    $this->admin('scheduled_import_export');
    $this->importExportScheduledHelper()->openImportExport(
    array(
        'name' => $importDataTwo['name'],
        'operation' => 'Import',
    )
    );
    $importDataTwo = array(
        'status' => 'Enabled',
    );
    $this->verifyForm($importDataTwo);
}
    /**
     * <p>Scheduled Import statuses</p>
     * <p> Create Product Import in System-> Import/Export-> Scheduled Import/Export</p>
     * <p> Create old Customer Import with keyword 'test' in the name</p>
     * <p> Create New Customer Import with keyword 'test' in the name</p>
     * <p> Create yet another New Customer Import with keyword 'test' in the name</p>
     * <p> Create yet another New Customer Import with another name</p>
     * <p> All imports have different 'entity subtype', 'status', 'frequency','last run date'</p>
     * <p> Steps: </p>
     * <p>1. On 'Scheduled Import/Export' page in filter 'Entity Type' select 'Products' and press 'Search'</p>
     * <p> Result: Only 'product imports' should be displayed in the grid</p>
     * <p>2. in filter 'Entity Type' select 'Customers' and press 'Search'</p>
     * <p>Result: Only 'customer imports' should be displayed in the grid</p>
     * <p>3. Select 'Daily' frequency and press 'Search'</p>
     * <p> Result: Only the imports with frequency 'Daily' are displayed in the grid</p>
     * <p>4. Select 'Weekly' frequency and press 'Search'</p>
     * <p> Result: Only the imports with frequency 'Weekly' are displayed in the grid</p>
     * <p>5. Select 'Monthly' frequency and press 'Search'</p>
     * <p> Result: Only the imports with frequency 'Monthly' are displayed in the grid</p>
     * <p>6. In the filter select 'Disabled' status and press 'Search'</p>
     * <p>Result: Only the imports with status 'Disabled' are displayed in the grid</p>
     * <p>7. In the filter select 'Enabled' status and press 'Search'</p>
     * <p>Result: Only the imports with status 'Enabled' are displayed in the grid</p>
     * <p>8. In the filter 'Last Outcome' select 'Pending' and press 'Search'</p>
     * <p>Result: Only Pending imports  are displayed in the grid</p>
     * <p>9. In the filter 'Last Outcome' select 'Successful' and press 'Search'</p>
     * <p>Result: Only Successful imports  are displayed in the grid</p>
     * <p>10. In the filter 'Last Outcome' select 'Failed' and press 'Search'</p>
     * <p>Result: Only Failed imports  are displayed in the grid</p></p>
     * <p>11. In grid select 'entity subtype' 'Customers Main File'</p>
     * <p>Result:Only the imports with subtype 'Customers Main File' are displayed in the grid</p>
     * <p>12. In grid select 'entity subtype' 'Customer Addresses'</p>
     * <p>Result: Only the imports with subtype 'Customer Addresses' are displayed in the grid</p>
     * <p>13. In grid select 'entity subtype' 'Customer Finances'</p>
     * <p>Result: Only the imports with subtype 'Customer Finances' are displayed in the grid</p>
     * <p>14. Enter in the grid proper date to the fields 'From' and 'To'</p>
     * <p>Result: Only imports with this last run date  are displayed in the grid</p>
     * <p>15. In grid in the field 'Name' enter 'test' and press 'Search' button</p>
     * <p>Result: Only imports which have the key 'test' in the name are displayed in the grid </p>
     * @test
     *
     * @TestlinkId TL-MAGE-5803
     */
    public function scheduledImportSearchByFilter()

    {
        //Preconditions:
        // 1. Create Product Import
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Products',
            'behavior' => 'Append Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_product.csv',
            'status' => 'Disabled',
            'frequency' => 'Weekly',
        ));
        $this->importExportScheduledHelper()->createImport($importData);
        // 2. Create Customer Old Import
        $importData1 = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_1',
            'file_format_version' => 'Magento 1.7 format',
            'behavior' => 'Delete Entities',
            'file_name' => date('Y-m-d_H-i-s_') . 'old_customer.csv',
            'frequency' => 'Daily'
        ));
        $this->importExportScheduledHelper()->createImport($importData1);
        // Run customer old import
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData1['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('error', 'error_run');
        // 3. Create Customer New Import with other status and behavior
        $importData2 = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_2',
            'file_format_version' => 'Magento 2.0 format',
            'behavior' => 'Delete Entities',
            'entity_subtype' => 'Customer Addresses',
            'file_name' => date('Y-m-d_H-i-s_') . 'old_customer_2.csv',
            'frequency' => 'Monthly',
        ));
        $this->importExportScheduledHelper()->createImport($importData2);
        // 4. Create Customer New Import
        $importData3 = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_3',
            'file_format_version' => 'Magento 2.0 format',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer_3.csv',
            'status' => 'Disabled',
            'frequency' => 'Daily'
        ));
        $this->importExportScheduledHelper()->createImport($importData3);

        // 5. Create Customer New Import with other status and behavior
        $importData4 = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'file_format_version' => 'Magento 2.0 format',
            'behavior' => 'Custom Action',
            'entity_subtype' => 'Customer Finances',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer_3.csv',
            'status' => 'Enabled',
            'frequency' => 'Monthly'
        ));
        $this->importExportScheduledHelper()->createImport($importData4);
        // Run customer import
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importData4['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('error', 'error_run');

        $data[0]['name'] = $importData1['name'];
        $data[1]['name'] = $importData2['name'];
        $data[2]['name'] = $importData3['name'];
        $data[3]['name'] = $importData4['name'];
        // Step 1, 2
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                        'name' => $importData['name'],
                        'entity_type' => 'Customers'
            )
        ));

        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
            array(
                'name' => $importData['name'],
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
            $arr = array($importData, $importData2, $importData4);
            foreach ($arr as $value)
        {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
        }
            $arr = array($importData1, $importData3);
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
                'name' => $importData['name'],
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
        $arr = array($importData2, $importData4);
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
        $arr = array($importData, $importData1, $importData3);
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
        $arr = array($importData, $importData3);
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
        $arr = array($importData1, $importData2, $importData4);
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
        $arr = array($importData, $importData3);
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
        $arr = array($importData1, $importData2, $importData4);
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
        $arr = array($importData, $importData2, $importData3);
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
        $arr = array($importData1, $importData4);
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
                'name' => $importData3['name'],
                'entity_subtype' => 'Customers Main File'
            )
        ));

        $arr = array($importData, $importData1, $importData2, $importData4);
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
                'name' => $importData2['name'],
                'entity_subtype' => 'Customer Addresses'
            )
        ));

        $arr = array($importData, $importData1, $importData3, $importData4);
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
                'name' => $importData4['name'],
                'entity_subtype' => 'Customer Finances'
            )
        ));
        $arr = array($importData, $importData1, $importData2, $importData3);
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
        $arr = array($importData, $importData2, $importData3);
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
        $arr = array($importData1, $importData4);
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
        $arr = array($importData1, $importData2, $importData3);
        foreach ($arr as $value)
        {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(
                array(
                    'name' => 'Team_B',
                    'operation' => $value['operation'],
                )
            ));

            $arr = array($importData, $importData4);
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
