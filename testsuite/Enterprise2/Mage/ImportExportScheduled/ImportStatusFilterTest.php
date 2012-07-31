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
class Enterprise2_Mage_ImportExportScheduled_ExportImportStatusFilterTest_CustomerTest extends Mage_Selenium_TestCase
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
     * Scheduled Import statuses
     * Steps:
     * 1. Create new import with status "Disable"
     * Result: Import was saved with the status "Disable"
     * 2. Edit Import - change status to "Enable"
     * Result: The changes was saved with "Enable" status
     * 3. Select this Import in grid
     * 4. Change status to "Disable" with a help "Actions"
     * Result: Status is changed to "Disable"
     * 5. Create new import with status disabled
     * 6. Choose both imports in the grid
     * 7. Change status to "Enabled" with a help "Actions for both imports"
     *  Result: Status is changed to "Enabled" for both imports
     * @test
     * @TestlinkId TL-MAGE-5802
     */
    public function scheduledImportStatuses()
    {
        // Step 1
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers Main File',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer.csv',
            'status' => 'Disabled',
        ));
        $importRecordsCountOne = 1;

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
        $this->addParameter('qtyUpdatedRecords', count($importRecordsCountOne));
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
            'entity_type' => 'Customers Main File',
            'behavior' => 'Delete Entities',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_1_customer.csv',
            'status' => 'Disabled',
        ));
        $importRecordsCountTwo = 2;

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
        $this->addParameter('qtyUpdatedRecords', $importRecordsCountTwo);
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
     * Scheduled Import statuses
     *  Create Product Import in System-> Import/Export-> Scheduled Import/Export
     *  Create three customer imports with keyword 'test' in the name
     *  Create another new customer import with another name
     *  All imports have different 'entity subtype', 'status', 'frequency','last run date'
     *  Steps:
     * 1. On 'Scheduled Import/Export' page in filter 'Entity Type' select 'Products' and press 'Search'
     *  Result: Only 'product imports' should be displayed in the grid
     * 2. in filter 'Entity Type' select all customer entity types and press 'Search'
     * Result: Only 'customer imports' should be displayed in the grid
     * 3. Select 'Daily' frequency and press 'Search'
     *  Result: Only the imports with frequency 'Daily' are displayed in the grid
     * 4. Select 'Weekly' frequency and press 'Search'
     *  Result: Only the imports with frequency 'Weekly' are displayed in the grid
     * 5. Select 'Monthly' frequency and press 'Search'
     *  Result: Only the imports with frequency 'Monthly' are displayed in the grid
     * 6. In the filter select 'Disabled' status and press 'Search'
     * Result: Only the imports with status 'Disabled' are displayed in the grid
     * 7. In the filter select 'Enabled' status and press 'Search'
     * Result: Only the imports with status 'Enabled' are displayed in the grid
     * 8. In the filter 'Last Outcome' select 'Pending' and press 'Search'
     * Result: Only Pending imports  are displayed in the grid
     * 9. In the filter 'Last Outcome' select 'Successful' and press 'Search'
     * Result: Only Successful imports  are displayed in the grid
     * 10. In the filter 'Last Outcome' select 'Failed' and press 'Search'
     * Result: Only Failed imports  are displayed in the grid
     * 11. Enter in the grid proper date to the fields 'From' and 'To'
     * Result: Only imports with this last run date  are displayed in the grid
     * 12. In grid in the field 'Name' enter 'test' and press 'Search' button
     * Result: Only imports which have the key 'test' in the name are displayed in the grid
     * @test
     *
     * @TestlinkId TL-MAGE-5803
     */
    public function scheduledImportSearchByFilter()
    {
        //Preconditions:
        // 1. Create Product Import
        $importDataProducts = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Products',
            'behavior' => 'Append Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_product.csv',
            'status' => 'Disabled',
            'frequency' => 'Weekly',
        ));
        $this->importExportScheduledHelper()->createImport($importDataProducts);
        // 2. Create Customer Old Import
        $importDataCustomers = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_1',
            'entity_type' => 'Customers',
            'behavior' => 'Delete Entities',
            'file_name' => date('Y-m-d_H-i-s_') . 'old_customer.csv',
            'frequency' => 'Daily'
        ));
        $this->importExportScheduledHelper()->createImport($importDataCustomers);
        // Run customer old import
        $this->importExportScheduledHelper()->applyAction(
            array(
                'name' => $importDataCustomers['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('error', 'error_run');
        // 3. Create Customer New Import with other status and behavior
        $importDataAddresses = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_2',
            'behavior' => 'Delete Entities',
            'entity_type' => 'Customer Addresses',
            'file_name' => date('Y-m-d_H-i-s_') . 'old_customer_2.csv',
            'frequency' => 'Monthly',
        ));
        $this->importExportScheduledHelper()->createImport($importDataAddresses);
        // 4. Create Customer New Import
        $importDataMain = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'name' => 'Team_B_3',
            'entity_type' => 'Customers Main File',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer_3.csv',
            'status' => 'Disabled',
            'frequency' => 'Daily'
        ));
        $this->importExportScheduledHelper()->createImport($importDataMain);

        // 5. Create Customer New Import with other status and behavior
        $importDataFinances = $this->loadDataSet(
            'ImportExportScheduled', 'scheduled_import', array(
            'behavior' => 'Custom Action',
            'entity_type' => 'Customer Finances',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer_3.csv',
            'status' => 'Enabled',
            'frequency' => 'Monthly'
            ));
        $this->importExportScheduledHelper()->createImport($importDataFinances);
        // Run customer import
        $this->importExportScheduledHelper()->applyAction(array(
                'name' => $importDataFinances['name'],
                'operation' => 'Import'
            )
        );
        $this->assertMessagePresent('error', 'error_run');

        // Step 1, 2
        $data = array(
            $importDataCustomers,
            $importDataAddresses,
            $importDataMain,
            $importDataFinances,
            $importDataProducts
        );
        $allEntityTypes = array(
            'Customers',
            'Customer Addresses',
            'Customers Main File',
            'Customer Finances',
            'Products',
        );
        foreach ($data as $importDataKey => $importData) {
            foreach ($allEntityTypes as $entityTypeKey => $entityType) {
                $this->admin('scheduled_import_export');
                if ($importDataKey == $entityTypeKey) {
                    $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                            'name' => $importData['name'],
                            'entity_type' => $entityType,
                        )
                    ));
                } else {
                    $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                            'name' => $importData['name'],
                            'entity_type' => $entityType,
                        )
                    ));
                }
            }
        }
        // Step 3
        $arr = array($importDataProducts, $importDataAddresses, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($importDataCustomers, $importDataMain);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Daily'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 4
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => $importDataProducts['name'],
                'frequency' => 'Weekly'
            )
        ));
        $this->admin('scheduled_import_export');
        unset($data[4]);
        foreach ($data as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Weekly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 5
        $arr = array($importDataAddresses, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($importDataProducts, $importDataCustomers, $importDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'frequency' => 'Monthly'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 6
        $arr = array($importDataProducts, $importDataMain);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($importDataCustomers, $importDataAddresses, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Disabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 7
        $arr = array($importDataProducts, $importDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($importDataCustomers, $importDataAddresses, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'status' => 'Enabled'
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 8, 9, 10
        $arr = array($importDataProducts, $importDataAddresses, $importDataMain);
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
        $arr = array($importDataCustomers, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Pending',
                )
            ));
            $this->admin('scheduled_import_export');
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Successful',
                )
            ));
            $this->admin('scheduled_import_export');
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'last_outcome' => 'Failed',
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 11
        $arr = array($importDataProducts, $importDataAddresses, $importDataMain);
        foreach ($arr as $value) {
            $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'date_from' => date("m/d/Y"),
                    'date_to' => date("m/d/Y")
                )
            ));
            $this->admin('scheduled_import_export');
        }
        $arr = array($importDataCustomers, $importDataFinances);
        foreach ($arr as $value) {
            $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $value['name'],
                    'date_from' => date("m/d/Y"),
                    'date_to' => date("m/d/Y")
                )
            ));
            $this->admin('scheduled_import_export');
        }
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => 'Team_B',
                'operation' => 'Import',
            )
        ));
    }
}