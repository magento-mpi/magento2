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
class Enterprise_Mage_ImportExportScheduled_ExportImportStatusFilterTest_CustomerTest extends Mage_Selenium_TestCase
{
    protected static $_currentDate;
    /**
     * Precondition:
     * Delete all existing imports/exports
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->importExportScheduledHelper()->deleteAllJobs();
    }

    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        $this->admin('scheduled_import_export');
    }

    /**
     * Open and verify scheduled import status
     *
     * @param array $scheduledData
     * @param string $status
     */
    protected function _openAndVerifyScheduledImportExport(array $scheduledData, $status)
    {
        // Verifying
        $this->importExportScheduledHelper()->openImportExport(
            array(
                'name' => $scheduledData['name'],
                'operation' => 'Import',
            )
        );
        //verify form
        $this->verifyForm(array('status' => $status));
    }

    /**
     * Scheduled Import statuses
     *
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
        $importRecordsCount = 1;

        $this->importExportScheduledHelper()->createImport($importData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'), 'Disabled');
        // Step 2
        $this->fillDropdown('status', 'Enabled');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_import');
        // Verifying
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'), 'Enabled');
        // Step 3
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $importData['name'],
            'operation' => 'Import',
        ), 'grid_and_filter');
        // Step 4
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Disabled');
        $this->clickButton('submit');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', $importRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'), 'Disabled');
        // Step 5
        $this->admin('scheduled_import_export');
        $importDataTwo = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers Main File',
            'behavior' => 'Delete Entities',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_1_customer.csv',
            'status' => 'Disabled',
        ));
        $importRecordsCount = 2;

        $this->importExportScheduledHelper()->createImport($importDataTwo);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importDataTwo['name'],
                'operation' => 'Import'), 'Disabled');
        // Step 6
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
                'name' => $importData['name'],
                'operation' => 'Import',
                'status' => 'Disabled',
            ), 'grid_and_filter'
        );
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $importDataTwo['name'],
            'operation' => 'Import',
        ), 'grid_and_filter');
        // Step 7
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Enabled');
        $this->clickButton('submit');
        //Verifying first import
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', $importRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importData['name'],
                'operation' => 'Import'), 'Enabled');
        // Verifying second import
        $this->admin('scheduled_import_export');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $importDataTwo['name'],
                'operation' => 'Import'), 'Enabled');
    }

    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _preconditionScheduledExportSearchByFilter(&$importDataProducts, &$importDataAddresses,
        &$importDataMain, &$importDataFinances, &$importDataCustomers
    ) {
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
        self::$_currentDate = $this->importExportScheduledHelper()->getLastRunDate(array(
            'name' => $importDataFinances['name'],
            'operation' => 'Import'
        ));
        //Convert to M/d/Y
        self::$_currentDate = date("m/d/Y", strtotime(self::$_currentDate));
        $this->assertMessagePresent('error', 'error_run');
    }

    /**
     * @param $data
     * @param $filterData
     * @param $status
     */
    protected function _checkScheduledImportSearchFilter($data, $filterData, $status)
    {
        foreach ($data as $value) {
            $this->admin('scheduled_import_export');
            $this->assertEquals($status, (bool)$this->importExportScheduledHelper()->searchImportExport(
                array_merge(array('name' => $value['name']), $filterData)
            ));
        }
    }
    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _checkScheduledImportSearchFilterFrequency(
        $importDataProducts, $importDataAddresses,
        $importDataMain, $importDataFinances, $importDataCustomers
    ) {
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataAddresses, $importDataFinances),
            array('frequency' => 'Daily'),
            false
        );

        // Step 3
        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataMain),
            array('frequency' => 'Daily'),
            true
        );

        // Step 4
        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataAddresses, $importDataMain, $importDataFinances),
            array('frequency' => 'Weekly'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataAddresses, $importDataMain, $importDataFinances),
            array('frequency' => 'Weekly'),
            false
        );

        // Step 5
        $this->_checkScheduledImportSearchFilter(
            array($importDataAddresses, $importDataFinances),
            array('frequency' => 'Monthly'),
            true
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataCustomers, $importDataMain),
            array('frequency' => 'Monthly'),
            false
        );
    }
    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _checkScheduledImportSearchFilterStatus(
        $importDataProducts, $importDataAddresses,
        $importDataMain, $importDataFinances, $importDataCustomers
    ) {
        // Step 6
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataMain),
            array('status' => 'Disabled'),
            true
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataAddresses, $importDataFinances),
            array('status' => 'Disabled'),
            false
        );

        // Step 7
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataMain),
            array('status' => 'Enabled'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataAddresses, $importDataFinances),
            array('status' => 'Enabled'),
            true
        );
    }
    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _checkScheduledImportSearchFilterOutcome(
        $importDataProducts, $importDataAddresses,
        $importDataMain, $importDataFinances, $importDataCustomers
    ) {
        // Step 8, 9, 10
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataAddresses, $importDataMain),
            array('last_outcome' => 'Pending'),
            true
        );
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataAddresses, $importDataMain),
            array('last_outcome' => 'Failed'),
            false
        );
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataAddresses, $importDataMain),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataFinances),
            array('last_outcome' => 'Pending'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataFinances),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataFinances),
            array('last_outcome' => 'Failed'),
            true
        );
    }

    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _checkScheduledImportSearchFilterDates(
        $importDataProducts, $importDataAddresses,
        $importDataMain, $importDataFinances, $importDataCustomers
    ) {
        $this->_checkScheduledImportSearchFilter(
            array($importDataProducts, $importDataAddresses, $importDataMain),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            false
        );

        // Step 11
        $this->_checkScheduledImportSearchFilter(
            array($importDataCustomers, $importDataFinances),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            true
        );
    }

    /**
     * Scheduled Import statuses
     *
     * @test
     *
     * @TestlinkId TL-MAGE-5803
     */
    public function scheduledImportSearchByFilter()
    {
        $importDataProducts  = array();
        $importDataCustomers = array();
        $importDataAddresses = array();
        $importDataMain      = array();
        $importDataFinances  = array();
        //Precondition
        $this->_preconditionScheduledExportSearchByFilter(
            $importDataProducts,
            $importDataAddresses,
            $importDataMain,
            $importDataFinances,
            $importDataCustomers);
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
        //Step 3, 4, 5
        $this->_checkScheduledImportSearchFilterFrequency(
            $importDataProducts, $importDataAddresses,
            $importDataMain, $importDataFinances, $importDataCustomers
        );
        // Step 6, 7
        $this->_checkScheduledImportSearchFilterStatus(
            $importDataProducts, $importDataAddresses,
            $importDataMain, $importDataFinances, $importDataCustomers
        );
        // Step 8, 9, 10
        $this->_checkScheduledImportSearchFilterOutcome(
            $importDataProducts, $importDataAddresses,
            $importDataMain, $importDataFinances, $importDataCustomers
        );
        // Step 11
        $this->_checkScheduledImportSearchFilterDates(
            $importDataProducts, $importDataAddresses,
            $importDataMain, $importDataFinances, $importDataCustomers
        );
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => 'Team_B',
                'operation' => 'Import',
            )
        ));
    }
}