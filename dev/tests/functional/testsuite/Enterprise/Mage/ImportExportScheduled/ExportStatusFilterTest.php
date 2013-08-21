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
class Enterprise_Mage_ImportExportScheduled_ExportStatusFilterTest extends Mage_Selenium_TestCase
{
    protected static $_currentDate;

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->importExportScheduledHelper()->deleteAllJobs();
    }

    protected function assertPreConditions()
    {
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
        $this->importExportScheduledHelper()->openImportExport(array(
            'name' => $scheduledData['name'],
            'operation' => 'Export'
        ));
        //verify form
        $this->verifyForm(array('status' => $status));
        $this->assertEmptyVerificationErrors();
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
        $exportData[] = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'entity_type' => 'Customer Finances',
            'status' => 'Disabled'
        ));
        $this->importExportScheduledHelper()->createExport($exportData[0]);
        // Verify
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->_openAndVerifyScheduledImportExport(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export'
        ), 'Disabled');
        // Step 2
        $this->fillDropdown('status', 'Enabled');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_export');
        // Verifying
        $this->_openAndVerifyScheduledImportExport(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export'
        ), 'Enabled');
        // Step 3
        $this->admin('scheduled_import_export');
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export',
        ), 'grid_and_filter');
        $exportRecordsCount = 1;
        // Step 4
        $this->fillForm(array(
            'grid_massaction_select' => 'Change status',
            'status_visibility' => 'Disabled'
        ));
        $this->clickButton('submit');
        // Verifying
        $this->addParameter('qtyUpdatedRecords', $exportRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export'
        ), 'Disabled');
        // Step 5
        $this->admin('scheduled_import_export');
        $exportData[] = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'entity_type' => 'Customer Finances',
            'status' => 'Disabled'
        ));
        $this->importExportScheduledHelper()->createExport($exportData[1]);
        // Verify
        $this->assertMessagePresent('success', 'success_saved_export');
        $this->_openAndVerifyScheduledImportExport(array(
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
        ), 'grid_and_filter');
        $this->importExportScheduledHelper()->searchAndChoose(array(
            'name' => $exportData[1]['name'],
            'operation' => 'Export',
        ), 'grid_and_filter');
        // Step7
        $this->fillForm(array(
            'grid_massaction_select' => 'Change status',
            'status_visibility' => 'Enabled'
        ));
        $this->clickButton('submit');
        //Verifying first import
        $this->addParameter('qtyUpdatedRecords', $exportRecordsCount);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport(array(
            'name' => $exportData[0]['name'],
            'operation' => 'Export'
        ), 'Enabled');
        // Verifying second import
        $this->admin('scheduled_import_export');
        $this->_openAndVerifyScheduledImportExport(array(
            'name' => $exportData[1]['name'],
            'operation' => 'Export'
        ), 'Enabled');
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _preconditionScheduledExportSearchByFilter(
        &$exportProducts, &$exportAddresses, &$exportMain, &$exportFinances
    )
    {
        $this->markTestIncomplete('BUG: behavior field is not editable');
        // Create product export
        $exportProducts = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'entity_type' => 'Products',
            'status' => 'Disabled',
            'frequency' => 'Weekly',
        ));
        $this->importExportScheduledHelper()->createExport($exportProducts);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportAddresses = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'name' => 'Team_B_Export_2',
            'entity_type' => 'Customer Addresses',
            'status' => 'Enabled',
            'frequency' => 'Monthly',
        ));
        $this->importExportScheduledHelper()->createExport($exportAddresses);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Create New  Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportMain = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'name' => 'Team_B_Export_3',
            'entity_type' => 'Customers Main File',
            'status' => 'Disabled',
            'frequency' => 'Daily',
        ));
        $this->importExportScheduledHelper()->createExport($exportMain);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Create New Customer Export
        $this->checkCurrentPage('scheduled_import_export');
        $exportFinances = $this->loadDataSet('ImportExportScheduled', 'scheduled_export', array(
            'entity_type' => 'Customer Finances',
            'status' => 'Enabled',
            'frequency' => 'Monthly',
            'user_name' => 'not_exist'
        ));
        $this->importExportScheduledHelper()->createExport($exportFinances);
        $this->assertMessagePresent('success', 'success_saved_export');
        // Run operation
        $this->importExportScheduledHelper()->applyAction(array(
            'name' => $exportFinances['name'],
            'operation' => 'Export'
        ));
        self::$_currentDate = $this->importExportScheduledHelper()->getLastRunDate(array(
            'name' => $exportFinances['name'],
            'operation' => 'Export'
        ));
        //Convert to M/d/Y
        self::$_currentDate = date("m/d/Y", strtotime(self::$_currentDate));
        $this->assertMessagePresent('error', 'error_run');
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _verifyScheduledExportSearchByFilter(
        &$exportProducts, &$exportAddresses, &$exportMain, &$exportFinances
    )
    {
        // Step 1, 2
        // Verifying filter by Products entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportProducts['name'],
            'entity_type' => 'Customers'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportProducts['name'],
            'entity_type' => 'Products'
        )));
        // Verifying filter by Customers Main File entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportMain['name'],
            'entity_type' => 'Customer Addresses'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportMain['name'],
            'entity_type' => 'Customer Finances'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportMain['name'],
            'entity_type' => 'Customers Main File'
        )));
        // Verifying filter by Customer Addresses entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportAddresses['name'],
            'entity_type' => 'Customers Main File'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportAddresses['name'],
            'entity_type' => 'Customer Finances'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportAddresses['name'],
            'entity_type' => 'Customer Addresses'
        )));
        // Verifying filter by Customer Finances entity type
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportFinances['name'],
            'entity_type' => 'Customers Main File'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportFinances['name'],
            'entity_type' => 'Customer Addresses'
        )));
        $this->admin('scheduled_import_export');
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => $exportFinances['name'],
            'entity_type' => 'Customer Finances'
        )));
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
            $filterData['name'] = $value['name'];
            $this->assertEquals($status, (bool)$this->importExportScheduledHelper()->searchImportExport($filterData));
        }
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _checkScheduledExportSearchFilterDates(
        $exportProducts, $exportAddresses, $exportMain, $exportFinances
    )
    {
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportAddresses, $exportMain),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            false
        );
        // Step 11
        $this->_checkScheduledExportSearchFilter(
            array($exportFinances),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            true
        );
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _checkScheduledExportSearchFilterOutcome(
        $exportProducts, $exportAddresses, $exportMain, $exportFinances
    )
    {
        // Step 8, 9, 10
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportAddresses, $exportMain),
            array('last_outcome' => 'Pending'),
            true
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportAddresses, $exportMain),
            array('last_outcome' => 'Failed'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportAddresses, $exportMain),
            array('last_outcome' => 'Successful'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportFinances),
            array('last_outcome' => 'Pending'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportFinances),
            array('last_outcome' => 'Successful'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportFinances),
            array('last_outcome' => 'Failed'),
            true
        );
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _checkScheduledExportSearchFilterStatus(
        $exportProducts, $exportAddresses, $exportMain, $exportFinances
    )
    {
        // Step 6
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportMain),
            array('status' => 'Disabled'),
            true
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportAddresses, $exportFinances),
            array('status' => 'Disabled'),
            false
        );
        // Step 7
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportMain),
            array('status' => 'Enabled'),
            false
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportAddresses, $exportFinances),
            array('status' => 'Enabled'),
            true
        );
    }

    /**
     * @param $exportProducts
     * @param $exportAddresses
     * @param $exportMain
     * @param $exportFinances
     */
    protected function _checkScheduledExportSearchFilterFrequency(
        $exportProducts, $exportAddresses, $exportMain, $exportFinances
    )
    {
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportAddresses, $exportFinances),
            array('frequency' => 'Daily'),
            false
        );
        // Step 3
        $this->_checkScheduledExportSearchFilter(
            array($exportMain),
            array('frequency' => 'Daily'),
            true
        );
        // Step 4
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts),
            array('frequency' => 'Weekly'),
            true
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportAddresses, $exportMain, $exportFinances),
            array('frequency' => 'Weekly'),
            false
        );
        // Step 5
        $this->_checkScheduledExportSearchFilter(
            array($exportAddresses, $exportFinances),
            array('frequency' => 'Monthly'),
            true
        );
        $this->_checkScheduledExportSearchFilter(
            array($exportProducts, $exportMain),
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
        $exportProducts = array();
        $exportAddresses = array();
        $exportMain = array();
        $exportFinances = array();
        //Precondition
        $this->_preconditionScheduledExportSearchByFilter(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        //Step 1, 2
        $this->_verifyScheduledExportSearchByFilter(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        //Steps 3, 4 , 5
        $this->_checkScheduledExportSearchFilterFrequency(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        // Step 6, 7
        $this->_checkScheduledExportSearchFilterStatus(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        // Step 8, 9, 10
        $this->_checkScheduledExportSearchFilterOutcome(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        // Step 11
        $this->_checkScheduledExportSearchFilterDates(
            $exportProducts, $exportAddresses, $exportMain, $exportFinances
        );
        $this->admin('scheduled_import_export');
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => 'Team_B',
            'operation' => 'Export',
        )));
    }
}