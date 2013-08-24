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
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ImportExport_BackwardCompatibility_ExportTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/disable_http_only');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }

    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('export');
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/enable_http_only');
        $this->systemConfigurationHelper()->configure('Advanced/enable_secret_key');
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that it is possible search by "Attribute Label" and after pressing "Reset Filter" button
     *
     * @test
     * @TestlinkId TL-MAGE-1308, 1309
     */
    public function searchByAttributeLabelAndResetFilter()
    {
        $this->markTestIncomplete('BUG: Search not work');
        //Steps 2-5
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        $this->importExportHelper()->customerFilterAttributes(array('attribute_label' => 'Created At'));
        //Verifying that required attribute is present in grid
        $isFound = $this->importExportHelper()
            ->customerSearchAttributes(array('attribute_label' => 'Created At'), 'grid_and_filter');
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Verifying that another attribute is not present in grid
        $isFound = $this->importExportHelper()
            ->customerSearchAttributes(array('attribute_label' => 'Is Confirmed'), 'grid_and_filter');
        $this->assertNull($isFound, 'Attribute was found after filtering');
        //Step 6
        $this->clickButton('reset_filter', false);
        $this->pleaseWait();
        //Steps 7-8
        $this->importExportHelper()->customerFilterAttributes(array('attribute_code' => 'created_at'));
        //Verifying that required attribute is present in grid
        $isFound = $this->importExportHelper()
            ->customerSearchAttributes(array('attribute_code' => 'created_at'), 'grid_and_filter');
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Verifying that another attribute is not present in grid
        $isFound = $this->importExportHelper()
            ->customerSearchAttributes(array('attribute_code' => 'confirmation'), 'grid_and_filter');
        $this->assertNull($isFound, 'Attribute was found after filtering');
        //Step 9
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        //Verifying that two attributes are present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_label' => 'Created At', 'attribute_code' => 'created_at'),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after resetting filter');
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_label' => 'Is Confirmed', 'attribute_code' => 'confirmation'),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after resetting filter');
    }

    /**
     * Has been excluded from functionality scope
     * Simple Export
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-1192
     */
    public function simpleExportMasterFile()
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('first_name' => $this->generate('string', 5)));
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Steps 2-3
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Steps 4-6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");

        return $userData;
    }

    /**
     * Has been excluded from functionality scope
     * Customer export using some filters
     *
     * @test
     * @depends simpleExportMasterFile
     * @TestlinkId TL-MAGE-1193
     */
    public function exportWithFilters($userData)
    {
        //Step 1
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Steps 2-3
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Step 4
        $this->importExportHelper()->setFilter(array('firstname' => $userData['first_name']));
        //Steps 5-6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }

    /**
     * Has been excluded from functionality scope
     * Export with skipped some attributes
     *
     * @test
     * @depends simpleExportMasterFile
     * @TestlinkId TL-MAGE-1194
     *
     * @param array $userData
     */
    public function exportWithSkippedAttribute($userData)
    {
        //Step 1
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Steps 2-3
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Step 4
        $isFound = $this->importExportHelper()
            ->customerSkipAttribute(array('attribute_label' => 'First Name'), 'grid_and_filter');
        $this->assertTrue($isFound, 'First Name attribute was not found');
        //Step 5
        $this->importExportHelper()->setFilter(array('firstname' => $userData['first_name']));
        //Step 6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertFalse(array_key_exists('firstname', $report[0]),
            'Skipped attribute was found in export file. Attribute Code: firstname');
    }
}