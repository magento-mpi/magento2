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
* Customer Export
*
* @package     selenium
* @subpackage  tests
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/
class Community2_Mage_ImportExport_Export_CustomerTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Preconditions:</p>
     * <p>Log in to Backend.</p>
     * <p>Navigate to System -> Export/p>
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('export');
    }

    /**
     * <p>Export Settings General View</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>Expected: dropdown contain correct values</p>
     * <p>Select Entity Type</p>
     * <p>Expected: Entity Attributes grid is visible, buttons Reset Filter, Search and Continue appeared</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5479, 1181
     */
    public function exportSettingsGeneralView()
    {
        //Verifying
        $entityTypes = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'entity_type') . '/option',
            'text');
        $expectedEntityTypeValues = array_merge(array('-- Please Select --', 'Products'),
            $this->importExportHelper()->getCustomerEntityType());
        $this->assertEquals($expectedEntityTypeValues, $entityTypes, 'Entity Type dropdown contains incorrect values');
        $fileFormat = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'file_format') . '/option',
            'text');
        $this->assertEquals(array('CSV'), $fileFormat,
            'Export File Format dropdown contains incorrect values');
        //Step 2
        $allEntityTypes = $expectedEntityTypeValues;
        unset($allEntityTypes[0]);
        foreach ($allEntityTypes as $value) {
            $this->fillDropdown('entity_type', $value);
            //Verifying
            $this->assertTrue($this->waitForElementVisible($this->_getControlXpath('fieldset', 'grid_and_filter')),
                'Grid and filter are not displayed');
            $this->assertTrue($this->waitForElementVisible($this->_getControlXpath('button', 'reset_filter')),
                'Reset button is not displayed');
            $this->assertTrue($this->waitForElementVisible($this->_getControlXpath('button', 'search')),
                'Search button is not displayed');
            $this->assertTrue($this->waitForElementVisible($this->_getControlXpath('button', 'continue')),
                'Continue button is not displayed');
        }
    }

    /**
     * @test
     */
    public function simpleExport()
    {
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers','generic_customer_account');
        $userDataAddress = $this->loadDataSet('Customers','generic_address');
        $this->customerHelper()->createCustomer($userData,$userDataAddress);
        //Step 1
        $this->navigate('export');
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        $report = $this->importExportHelper()->export();
    }

    /**
     * @test
     */
    public function simpleExportAddress()
    {
        //Step 1
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        $report = $this->importExportHelper()->export();
        $csv =  $this->importExportHelper()->arrayToCsv($report);
    }

    /**
     * <p>Simple Export Master file</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. Choose Customer (Master) file to export</p>
     * <p>3. Click on the Continue button</p>
     * <p>4. Save file to your computer</p>
     * <p>5. Open it.</p>
     * <p>Expected: Check that among all customers your customer with attribute is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5487
     */
    public function simpleExportMasterFile()
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');

        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Steps 3-6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file"
        );
    }

    /**
     * <p>Customer Master file export with using some filters</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. Select "Master Type File"</p>
     * <p>3. In the "Filter" column according to you attribute select option that was used in your customer creation</p>
     * <p>4. Press "Continue" button and save current file</p>
     * <p>5. Open file</p>
     * <p>Expected: In generated file just your customer with selected option of attribute is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5488
     */
    public function exportMasterFileWithFilters()
    {
        //Precondition: create attribute, create new customer, fill created attribute
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('first_name' => $this->generate('string', 5)));
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Steps 1-2
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step3
        $this->importExportHelper()
            ->setFilter(array('firstname' => $userData['first_name']));
        //Step4-5
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }

    /**
     * @test
     */
    public function simpleExportCustomer()
    {
        //Step 1
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        $customersMain = $this->importExportHelper()->export();
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        $customerAddresses = $this->ImportExportHelper()->export();
    }

    /**
     * @test
     */
    public function simpleAttributeFilterAndSearch()
    {
        //Step 1
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        $this->importExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => 'Created At',
                'attribute_code'  => 'created_at'
            )
        );

        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => 'Created At',
                'attribute_code'  => 'created_at'
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //mark attribute as skipped
        $this->importExportHelper()->customerSkipAttribute(
            array(
                'attribute_label' => 'Created At',
                'attribute_code'  => 'created_at'
            ),
            'grid_and_filter'
        );
    }

    /**
     * <p>Verify the search by fields "Attribute Label" and "Attribute Code"</p>
     * <p>This search should work with each file type</p>
     * <p>Steps:</p>
     * <p>1. Go to System-> Import/Export-> Export</p>
     * <p>2. Select file type (Customers Main File/Customer Addresses)</p>
     * <p>3. Type in "Attribute Code" field any name that is present in the list ('email'), click 'Search' button</p>
     * <p>4. Verify that attribute is found</p>
     * <p>5. Click 'Reset filter' button</p>
     * <p>6. Type in "Attribute Label" field any name that is present in the list ('Email'), click 'Search' button</p>
     * <p>7. Verify that attribute is found</p>
     * <p>8. Click 'Reset filter' button</p>
     * @test
     * @TestlinkId TL-MAGE-5482, TL-MAGE-5483, TL-MAGE-5495, TL-MAGE-5497, TL-MAGE-5496, TL-MAGE-5498
     */
    public function searchByAttributeLabelCode()
    {
        //Steps 1-2
        $arr = $this->importExportHelper()->getCustomerEntityType();
        foreach ($arr as $value) {
            $this->importExportHelper()->chooseExportOptions($value);
            //Step 3
            $this->importExportHelper()->customerFilterAttributes(
                array(
                    'attribute_code' => 'email'
                ));
            //Step 4
            $isFound = $this->importExportHelper()->customerSearchAttributes(
                array(
                    'attribute_code' => 'email'
                ),
                'grid_and_filter'
            );
            $this->assertNotNull($isFound, 'Attribute was not found after filtering');
            //Step 5
            $this->clickButton('reset_filter', false);
            $this->waitForAjax();
            //Step 6
            $this->importExportHelper()->customerFilterAttributes(
                array(
                    'attribute_label' => 'Email'
                ));
            //Step 7
            $isFound = $this->importExportHelper()->customerSearchAttributes(
                array(
                    'attribute_label' => 'Email'
                ),
                'grid_and_filter'
            );
            $this->assertNotNull($isFound, 'Attribute was not found after filtering');
            //Step 8
            $this->clickButton('reset_filter', false);
            $this->waitForAjax();
        }
    }
    /**
     * <p>Preconditions:</p>
     * <p>1. The grid with attributes should be presented for each file type</p>
     * <p>2. The column "Skip" is available only for "Customer Main File"</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5492, TL-MAGE-5493, TL-MAGE-5494
     */
    public function entityAttributesBlockAllFileTypes()
    {
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Verify
        $isFound = $this->importExportHelper()->customerSkipAttribute(
            array(
                'attribute_label' => 'Created At'
            ), 'grid_and_filter'
        );
        $this->assertTrue($isFound, 'Skip checkbox was not found');
        //Steps
        $customerTypes = $this->importExportHelper()->getCustomerEntityType();
        foreach (array_slice($customerTypes, 1) as $customerType) {
            $this->importExportHelper()->chooseExportOptions($customerType);
            $this->waitForElementVisible($this->_getControlXpath('fieldset', 'grid_and_filter'));
            $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
            //Steps
            $isFound = $this->importExportHelper()->customerSkipAttribute(
                array(
                    'attribute_label' => 'Created At'
                ),
                'grid_and_filter'
            );
            $this->assertFalse($isFound, 'Checkbox was found');
        }
    }
    /**
     * <p>Export with skipped some attributes</p>
     * <p>Steps</p>
     * <p>1. Admin is logged in at backend</p>
     * <p>2. Select "Master Type File"</p>
     * <p>3. Select  "SKIP" checkbox for the row with the attribute Date of Birth (for example)</p>
     * <p>4. Press "Continue" button and save file to your computer</p>
     * <p>5. Verify exported file</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5489
     */
    public function exportCustomerWithSkippedAttribute()
    {
        //Step 1,2
        $this->importExportHelper()->chooseExportOptions('Customers Main File');
        //Step 3
        $isFound = $this->importExportHelper()->customerSkipAttribute(
            array(
                'attribute_label' => 'Date Of Birth'),
            'grid_and_filter'
        );
        $this->assertTrue($isFound, 'Date of Birth attribute was not found');
        //Step 4
        $report = $this->importExportHelper()->export();
        //Verifying
        //search in array key = 'dob'
        $this->assertFalse(array_key_exists('dob', $report[0]),
            'Skipped attribute was found in export file. Attribute Code: dob'
        );
    }
    /**
     * <p>Simple Export Address file</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. Choose Customer Address file to export</p>
     * <p>3. Click on the Continue button</p>
     * <p>4. Save file to your computer</p>
     * <p>5. Open it.</p>
     * <p>Expected: Check that among all customers addresses your customer address with attribute is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5490
     */
    public function simpleExportAddressFile()
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($userData, $addressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        //Steps 3-5
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('address', $addressData, $report),
            "Customer address not found in csv file"
        );
    }

    /**
     * <p>Customer Address file export with some filters</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. Select "Address Type File"</p>
     * <p>3. In the "Filter" column select "Male" for the attribute "Gender"</p>
     * <p>4. Press "Continue" button and save current file</p>
     * <p>5. Open file</p>
     * <p>Expected: In generated file only "Male" customer presents. The addresses attributes are presented also.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5504
     */
    public function exportAddressFileWithFilters()
    {
        //Precondition: create 2 new customers
        $this->navigate('manage_customers');
        // 0.1. create male customer with address
        $maleUserData = $this->loadDataSet('Customers', 'all_fields_customer_account', array('gender' => 'Male'));
        $maleUserAddressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($maleUserData, $maleUserAddressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        // 0.2. create female customer with address
        $this->navigate('manage_customers');
        $femaleUserData = $this->loadDataSet('Customers', 'all_fields_customer_account', array('gender' => 'Female'));
        $femaleUserAddressData = $this->loadDataSet('Customers', 'generic_address');
        $this->customerHelper()->createCustomer($femaleUserData, $femaleUserAddressData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->navigate('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Step 2
        $this->importExportHelper()->chooseExportOptions('Customer Addresses');
        //Step 3
        $this->importExportHelper()
            ->setFilter(array('gender ' => 'Male'));
        //Step 4-5
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($report, "Export csv file is empty");
        $this->assertNotNull($this->importExportHelper()->lookForEntity('address', $maleUserAddressData, $report),
            "Male customer address data not found in csv file"
        );
    }
}
