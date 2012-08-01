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
class Community2_Mage_ImportExport_Backward_Export_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * Set preconditions to run tests
     * System settings:
     * Secure Key is disabled
     * HttpOnly cookies is disabled
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('General/disable_httponly');
        $this->systemConfigurationHelper()->configure('Advanced/disable_secret_key');
    }
    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('export');
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that it is possible search by "Attribute Label" and after pressing "Reset Filter" button
     * result will be reset (list of all attributes will be displayed)
     * Steps:
     * 1. Go to System -> Import/ Export -> Export
     * 2. In "Entity Type" dropdown field choose "Customers" parameter
     * 3. In "Export Format Version" dropdown field choose "Magento 1.7 format" parameter
     * 4. Type in "Attribute Label" field any name that is present in the list
     * 5. Press "Search" button
     * 6. Press "Reset Filter" button
     * 7. Type in "Attribute Code" field any code that is present in the list
     * 8. Press "Search" button
     * 9. Press "Reset Filter" button
     * Expected after steps 5,8: Just corresponding attribute will be displayed
     * Expected after step 9: Result will be reset and the whole list of attributes will be displayed
     *
     * @test
     * @TestlinkId TL-MAGE-1308, 1309
     * @group skip_due_to_bug
     */
    public function searchByAttributeLabelAndResetFilter()
    {
        //Steps 2-5
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        $this->importExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => 'Created At'
            )
        );
        //Verifying that required attribute is present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => 'Created At'
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Verifying that another attribute is not present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => 'Is Confirmed'
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after filtering');
        //Step 6
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        //Steps 7-8
        $this->importExportHelper()->customerFilterAttributes(
            array(
                'attribute_code'  => 'created_at'
            )
        );
        //Verifying that required attribute is present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_code'  => 'created_at'
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Verifying that another attribute is not present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_code'  => 'confirmation'
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after filtering');
        //Step 9
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        //Verifying that two attributes are present in grid
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => 'Created At',
                'attribute_code'  => 'created_at'
            ),
            'grid_and_filter'
        );
        $this->assertNotNull(
            $isFound, 'Attribute was not found after resetting filter'
        );
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => 'Is Confirmed',
                'attribute_code'  => 'confirmation'
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after resetting filter');
    }

    /**
     * Has been excluded from functionality scope
     * Simple Export
     * Precondition: At least one attribute for your customer must be created
     * Steps:
     * 1. Go to System -> Import/ Export -> Export
     * 2. In "Entity Type" drop-down field choose "Customers" parameter
     * 3. In "Export Format Version" drop-down choose "Magento 1.7" parameter
     * 4. Click on the Continue button
     * 5. Save file to your computer
     * 6. Open it.
     * Expected: Check that among all customers your customer with attribute is present
     *
     * @test
     * @return array
     * @TestlinkId TL-MAGE-1192
     * @group skip_due_to_bug
     */
    public function simpleExportMasterFile()
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet(
            'Customers', 'generic_customer_account', array(
                'first_name' => $this->generate('string', 5)
            )
        );
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
        $this->assertNotNull(
            $this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file"
        );

        return $userData;
    }

    /**
     * Has been excluded from functionality scope
     * Customer export using some filters
     * Steps
     * 1. On backend in System -> Import/ Export -> Export select "Customers" entity
     * type
     * 2. In "Entity Type" dropdown field choose "Customers" parameter
     * 3. In "Export Format Version" dropdown field choose "Magento 1.7" parameter
     * 4. In the "Filter" column according to you attribute select option that was
     * used in your customer creation
     * 5. Press "Continue" button and save current file
     * 6. Open file
     * Expected: In generated file just your customer with selected option of
     * attribute is present
     *
     * @test
     * @depends simpleExportMasterFile
     * @TestlinkId TL-MAGE-1193
     * @group skip_due_to_bug
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
        $this->assertNotNull(
            $this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file"
        );
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }

    /**
     * Has been excluded from functionality scope
     * Export with skipped some attributes
     * Steps
     * 1. Go to System -> Import/ Export -> Export
     * 2. In "Entity Type" drop-down field choose "Customers" parameter
     * 3. In "Export Format Version" dropdown field choose "Magento 1.7 format" parameter
     * 4. Select  "SKIP" checkbox for the row with the attribute First Name
     * 5. In the "Filter" column for the attribute enter customer first name
     * 6. Press "Continue" button and save file to your computer
     * 7. Open exported file
     * Expected: file doesn't contain first name attribute
     *
     * @test
     * @depends simpleExportMasterFile
     * @TestlinkId TL-MAGE-1194
     * @group skip_due_to_bug
     */
    public function exportWithSkippedAttribute($userData)
    {
        //Step 1
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Steps 2-3
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Step 4
        $isFound = $this->importExportHelper()->customerSkipAttribute(
            array(
                'attribute_label' => 'First Name'),
            'grid_and_filter'
        );
        $this->assertTrue($isFound, 'First Name attribute was not found');
        //Step 5
        $this->importExportHelper()->setFilter(array('firstname' => $userData['first_name']));
        //Step 6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertFalse(
            array_key_exists('firstname', $report[0]),
            'Skipped attribute was found in export file. Attribute Code: firstname'
        );
    }
}