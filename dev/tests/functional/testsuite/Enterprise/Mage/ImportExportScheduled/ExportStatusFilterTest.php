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
class Enterprise_Mage_ImportExportScheduled_ExportStatusFilterTest_CustomerTest extends Mage_Selenium_TestCase
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
     * Open and verify scheduled import export status
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
                'operation' => 'Export',
            )
        );
        //verify form
        $this->verifyForm(array('status' => $status));
    }

    /**
     * Scheduled Export statuses
     *
     * @test
     * @TestlinkId TL-MAGE-5816
     */
    public function scheduledExportStatuses()
    {
        // Step 1
        $exportData[] = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances',
                'status' => 'Disabled'
            ));
        $this->importExportScheduledHelper()->createExport($exportData[0]);
        // Verify
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[0]['name'],
                'operation' => 'Export'), 'Disabled'
        );
        // Step 2
        $this->fillDropdown('status', 'Enabled');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_export');
        // Verifying
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[0]['name'],
                'operation' => 'Export'), 'Enabled'
        );
        // Step 3
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export',
        ), 'grid_and_filter');
        $exportRecordsCount = 1;
        // Step 4
        $this->fillForm(
            array(
                'grid_massaction_select' => 'Change status',
                'status_visibility' => 'Disabled'));
        $this->clickButton('submit');
        // Verifying
        $this->addParameter('qtyUpdatedRecords', $exportRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[0]['name'],
                'operation' => 'Export'), 'Disabled'
        );
        // Step 5
        $this->admin('scheduled_import_export');
        $exportData[] = $this->loadDataSet('ImportExportScheduled', 'scheduled_export',
            array(
                'entity_type' => 'Customer Finances',
                'status' => 'Disabled'
            ));
        $this->importExportScheduledHelper()->createExport($exportData[1]);
        // Verify
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[1]['name'],
                'operation' => 'Export'), 'Disabled'
        );
        $exportRecordsCount = 2;
        // Step 6
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
                'name' => $exportData[0]['name'],
                'operation' => 'Export',
                'status' => 'Disabled',
            ), 'grid_and_filter'
        );
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData[1]['name'],
            'operation' => 'Export',
        ), 'grid_and_filter');
        // Step7
        $this->fillForm(
            array(
                'grid_massaction_select' => 'Change status',
                'status_visibility' => 'Enabled'));
        $this->clickButton('submit');
        //Verifying first import
        $this->addParameter('qtyUpdatedRecords', $exportRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[0]['name'],
                'operation' => 'Export'), 'Enabled'
        );
        // Verifying second import
        $this->admin('scheduled_import_export');
        $this->_openAndVerifyScheduledImportExport(
            array(
                'name' => $exportData[1]['name'],
                'operation' => 'Export'), 'Enabled'
        );
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _preconditionScheduledExportSearchByFilter(&$exportDataProducts, &$exportDataAddresses,
        &$exportDataMain, &$exportDataFinances
    ) {
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
        self::$_currentDate = $this->importExportScheduledHelper()->getLastRunDate(array(
            'name' => $exportDataFinances['name'],
            'operation' => 'Export'
        ));
        //Convert to M/d/Y
        self::$_currentDate = date("m/d/Y", strtotime(self::$_currentDate));
        $this->assertMessagePresent('error', 'error_run');
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _verifyScheduledExportSearchByFilter(&$exportDataProducts, &$exportDataAddresses,
        &$exportDataMain, &$exportDataFinances
    ) {
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
    }

    /**
     * @param $data
     * @param $filterData
     * @param $status
     */
    protected function _checkScheduledExportSearchFilter($data, $filterData, $status)
    {
        foreach ($data as $value) {
            $this->admin('scheduled_import_export');
            $this->assertEquals($status, (bool)$this->importExportScheduledHelper()->searchImportExport(
                array_merge(array('name' => $value['name']), $filterData)
            ));
        }
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _checkScheduledExportSearchFilterDates(
        $exportDataProducts, $exportDataAddresses,
        $exportDataMain, $exportDataFinances
    ) {
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataAddresses, $exportDataMain),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            false
        );

        // Step 11
        $this->_checkScheduledExportSearchFilter(
            array($exportDataFinances),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            true
        );
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _checkScheduledExportSearchFilterOutcome(
        $exportDataProducts, $exportDataAddresses,
        $exportDataMain, $exportDataFinances
    ) {
        // Step 8, 9, 10
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataAddresses, $exportDataMain),
            array('last_outcome' => 'Pending'),
            true
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataAddresses, $exportDataMain),
            array('last_outcome' => 'Failed'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataAddresses, $exportDataMain),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataFinances),
            array('last_outcome' => 'Pending'),
            false
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataFinances),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataFinances),
            array('last_outcome' => 'Failed'),
            true
        );
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _checkScheduledExportSearchFilterStatus(
        $exportDataProducts, $exportDataAddresses,
        $exportDataMain, $exportDataFinances
    ) {
        // Step 6
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataMain),
            array('status' => 'Disabled'),
            true
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataAddresses, $exportDataFinances),
            array('status' => 'Disabled'),
            false
        );

        // Step 7
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataMain),
            array('status' => 'Enabled'),
            false
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataAddresses, $exportDataFinances),
            array('status' => 'Enabled'),
            true
        );
    }

    /**
     * @param $exportDataProducts
     * @param $exportDataAddresses
     * @param $exportDataMain
     * @param $exportDataFinances
     */
    protected function _checkScheduledExportSearchFilterFrequency(
        $exportDataProducts, $exportDataAddresses,
        $exportDataMain, $exportDataFinances
    ) {
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataAddresses, $exportDataFinances),
            array('frequency' => 'Daily'),
            false
        );

        // Step 3
        $this->_checkScheduledExportSearchFilter(
            array($exportDataMain),
            array('frequency' => 'Daily'),
            true
        );

        // Step 4
        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts),
            array('frequency' => 'Weekly'),
            true
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataAddresses, $exportDataMain, $exportDataFinances),
            array('frequency' => 'Weekly'),
            false
        );

        // Step 5
        $this->_checkScheduledExportSearchFilter(
            array($exportDataAddresses, $exportDataFinances),
            array('frequency' => 'Monthly'),
            true
        );

        $this->_checkScheduledExportSearchFilter(
            array($exportDataProducts, $exportDataMain),
            array('frequency' => 'Monthly'),
            false
        );
    }

    /**
     * Scheduled Export statuses
     *
     * @test
     * @TestlinkId TL-MAGE-5817
     */
    public function scheduledExportSearchByFilter()
    {
        $exportDataProducts  = array();
        $exportDataAddresses = array();
        $exportDataMain      = array();
        $exportDataFinances  = array();
        //Precondition
        $this->_preconditionScheduledExportSearchByFilter(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);
        //Step 1, 2
        $this->_verifyScheduledExportSearchByFilter(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);
        //Steps 3, 4 , 5
        $this->_checkScheduledExportSearchFilterFrequency(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);
        // Step 6, 7
        $this->_checkScheduledExportSearchFilterStatus(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);
        // Step 8, 9, 10
        $this->_checkScheduledExportSearchFilterOutcome(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);
        // Step 11
        $this->_checkScheduledExportSearchFilterDates(
            $exportDataProducts,
            $exportDataAddresses,
            $exportDataMain,
            $exportDataFinances);

        $this->admin('scheduled_import_export');
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
                'name' => 'Team_B',
                'operation' => 'Export',
            )
        ));
    }
}