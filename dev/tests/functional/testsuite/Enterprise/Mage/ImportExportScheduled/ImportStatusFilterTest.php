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
class Enterprise_Mage_ImportExportScheduled_ImportStatusFilterTest extends Mage_Selenium_TestCase
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
     * Open and verify scheduled import status
     *
     * @param string $importName
     * @param string $status
     */
    protected function _openAndVerifyScheduledImportExport($importName, $status)
    {
        // Verifying
        $this->importExportScheduledHelper()->openImportExport(array('name' => $importName, 'operation' => 'Import'));
        //verify form
        $this->verifyForm(array('status' => $status));
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Scheduled Import statuses
     *
     * @test
     * @TestlinkId TL-MAGE-5802
     */
    public function scheduledImportStatuses()
    {
        $this->markTestIncomplete('BUG: behavior field is not editable');
        // Step 1
        $importData = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers Main File',
            'behavior' => 'Add/Update Complex Data',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_customer.csv',
            'status' => 'Disabled',
        ));
        $this->importExportScheduledHelper()->createImport($importData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->_openAndVerifyScheduledImportExport($importData['name'], 'Disabled');
        // Step 2
        $this->fillDropdown('status', 'Enabled');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_import');
        // Verifying
        $this->_openAndVerifyScheduledImportExport($importData['name'], 'Enabled');
        // Step 3
        $this->admin('scheduled_import_export');
        $this->searchAndChoose(array('name' => $importData['name'], 'operation' => 'Import'), 'grid_and_filter');
        // Step 4
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Disabled');
        $this->clickButton('submit');
        //Verifying
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', 1);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport($importData['name'], 'Disabled');
        // Step 5
        $this->admin('scheduled_import_export');
        $importDataTwo = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
            'entity_type' => 'Customers Main File',
            'behavior' => 'Delete Entities',
            'file_name' => date('Y-m-d_H-i-s_') . 'import_1_customer.csv',
            'status' => 'Disabled',
        ));
        $this->importExportScheduledHelper()->createImport($importDataTwo);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_import');
        $this->_openAndVerifyScheduledImportExport($importDataTwo['name'], 'Disabled');
        // Step 6
        $this->admin('scheduled_import_export');
        $this->searchAndChoose(
            array('name' => $importData['name'], 'operation' => 'Import', 'status' => 'Disabled'),
            'grid_and_filter'
        );
        $this->searchAndChoose(
            array('name' => $importDataTwo['name'], 'operation' => 'Import'),
            'grid_and_filter'
        );
        // Step 7
        $this->fillDropdown('grid_massaction_select', 'Change status');
        $this->fillDropdown('status_visibility', 'Enabled');
        $this->clickButton('submit');
        //Verifying first import
        $this->checkCurrentPage('scheduled_import_export');
        $this->addParameter('qtyUpdatedRecords', 2);
        $this->assertMessagePresent('success', 'success_update_status');
        $this->_openAndVerifyScheduledImportExport($importData['name'], 'Enabled');
        // Verifying second import
        $this->admin('scheduled_import_export');
        $this->_openAndVerifyScheduledImportExport($importDataTwo['name'], 'Enabled');
    }

    /**
     * @param $importDataProducts
     * @param $importDataAddresses
     * @param $importDataMain
     * @param $importDataFinances
     * @param $importDataCustomers
     */
    protected function _preconditionScheduledExportSearchByFilter(
        &$importDataProducts, &$importDataAddresses, &$importDataMain, &$importDataFinances, &$importDataCustomers
    )
    {
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
        $this->importExportScheduledHelper()->applyAction(array(
            'name' => $importDataCustomers['name'],
            'operation' => 'Import'
        ));
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
        $importDataFinances = $this->loadDataSet('ImportExportScheduled', 'scheduled_import', array(
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
        ));
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
            $filterData['name'] = $value['name'];
            $this->assertEquals($status, (bool)$this->importExportScheduledHelper()->searchImportExport($filterData));
        }
    }

    /**
     * @param $importProducts
     * @param $importAddresses
     * @param $importMain
     * @param $importFinances
     * @param $importCustomers
     */
    protected function _checkScheduledImportSearchFilterFrequency(
        $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
    )
    {
        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importAddresses, $importFinances),
            array('frequency' => 'Daily'),
            false
        );

        // Step 3
        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importMain),
            array('frequency' => 'Daily'),
            true
        );

        // Step 4
        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importAddresses, $importMain, $importFinances),
            array('frequency' => 'Weekly'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importAddresses, $importMain, $importFinances),
            array('frequency' => 'Weekly'),
            false
        );

        // Step 5
        $this->_checkScheduledImportSearchFilter(
            array($importAddresses, $importFinances),
            array('frequency' => 'Monthly'),
            true
        );

        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importCustomers, $importMain),
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
    )
    {
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
     * @param $importProducts
     * @param $importAddresses
     * @param $importMain
     * @param $importFinances
     * @param $importCustomers
     */
    protected function _checkScheduledImportSearchFilterOutcome(
        $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
    )
    {
        // Step 8, 9, 10
        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importAddresses, $importMain),
            array('last_outcome' => 'Pending'),
            true
        );
        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importAddresses, $importMain),
            array('last_outcome' => 'Failed'),
            false
        );
        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importAddresses, $importMain),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importFinances),
            array('last_outcome' => 'Pending'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importFinances),
            array('last_outcome' => 'Successful'),
            false
        );

        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importFinances),
            array('last_outcome' => 'Failed'),
            true
        );
    }

    /**
     * @param $importProducts
     * @param $importAddresses
     * @param $importMain
     * @param $importFinances
     * @param $importCustomers
     */
    protected function _checkScheduledImportSearchFilterDates(
        $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
    )
    {
        $this->_checkScheduledImportSearchFilter(
            array($importProducts, $importAddresses, $importMain),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            false
        );

        // Step 11
        $this->_checkScheduledImportSearchFilter(
            array($importCustomers, $importFinances),
            array('date_from' => self::$_currentDate, 'date_to' => self::$_currentDate),
            true
        );
    }

    /**
     * Scheduled Import statuses
     *
     * @test
     * @TestlinkId TL-MAGE-5803
     */
    public function scheduledImportSearchByFilter()
    {
        $this->markTestIncomplete('BUG: behavior field is not editable');
        $importProducts = array();
        $importCustomers = array();
        $importAddresses = array();
        $importMain = array();
        $importFinances = array();
        //Precondition
        $this->_preconditionScheduledExportSearchByFilter(
            $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
        );
        // Step 1, 2
        $data = array($importCustomers, $importAddresses, $importMain, $importFinances, $importProducts);
        $allEntityTypes = array(
            'Customers', 'Customer Addresses', 'Customers Main File', 'Customer Finances', 'Products'
        );
        foreach ($data as $importDataKey => $importData) {
            foreach ($allEntityTypes as $entityTypeKey => $entityType) {
                $this->admin('scheduled_import_export');
                $result = $this->importExportScheduledHelper()->searchImportExport(array(
                    'name' => $importData['name'],
                    'entity_type' => $entityType
                ));
                if ($importDataKey == $entityTypeKey) {
                    $this->assertNotNull($result);
                } else {
                    $this->assertNull($result);
                }
            }
        }
        //Step 3, 4, 5
        $this->_checkScheduledImportSearchFilterFrequency(
            $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
        );
        // Step 6, 7
        $this->_checkScheduledImportSearchFilterStatus(
            $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
        );
        // Step 8, 9, 10
        $this->_checkScheduledImportSearchFilterOutcome(
            $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
        );
        // Step 11
        $this->_checkScheduledImportSearchFilterDates(
            $importProducts, $importAddresses, $importMain, $importFinances, $importCustomers
        );
        // Step 12
        $this->assertNotNull($this->importExportScheduledHelper()->searchImportExport(array(
            'name' => 'Team_B',
            'operation' => 'Import'
        )));
    }
}