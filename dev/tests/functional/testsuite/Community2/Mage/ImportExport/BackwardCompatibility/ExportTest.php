<?php
/**
* Magento
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@magentocommerce.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade Magento to newer
* versions in the future. If you wish to customize Magento for your
* needs please refer to http://www.magentocommerce.com for more information.
*
* @category    tests
* @package     selenium
* @subpackage  tests
* @author      Magento Core Team <core@magentocommerce.com>
* @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/**
* Customer Export
*
* @package     selenium
* @subpackage  tests
* @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*
* @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
* @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
* @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
*/
class Community2_Mage_ImportExport_Backward_Export_CustomerTest extends Mage_Selenium_TestCase
{
    /**
     * <p>set preconditions to run tests </p>
     * <p>System settings:</p>
     * <p>Secure Key is disabled</p>
     * <p>HttpOnly cookies is disabled</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('disable_httponly');
        $this->systemConfigurationHelper()->configure('disable_secret_key');
    }
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
     * <p>Need to verify that it is possible search by "Attribute Label" and after pressing "Reset Filter"</p>
     * <p>button result will be reset(list of all attributes will be displayed)</p>
     * <p>Steps:</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In "Entity Type" dropdown field choose "Customers" parameter</p>
     * <p>3. In "Export Format Version" dropdown field choose "Magento 1.7 format" parameter</p>
     * <p>4. Type in "Attribute Label" field any name that is present in the list</p>
     * <p>5. Press "Search" button</p>
     * <p>6. Press "Reset Filter" button</p>
     * <p>7. Type in "Attribute Code" field any code that is present in the list</p>
     * <p>8. Press "Search" button</p>
     * <p>9. Press "Reset Filter" button</p>
     * <p>Expected after steps 5,8: Just corresponding attribute will be displayed</p>
     * <p>Expected after step 9: Result will be reset and the whole list of attributes will be displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1308, 1309
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
        $this->assertNotNull($isFound, 'Attribute was not found after resetting filter');
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
     * <p>Simple Export</p>
     * <p>Precondition: At least one attribute for your customer must be created</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>3. In "Export Format Version" drop-down choose "Magento 1.7" parameter</p>
     * <p>4. Click on the Continue button</p>
     * <p>5. Save file to your computer</p>
     * <p>6. Open it.</p>
     * <p>Expected: Check that among all customers your customer with attribute is present</p>
     *
     * @test
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
        $this->admin('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Steps 2-3
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Steps 4-6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file"
        );

        return $userData;
    }

    /**
     * <p>Customer export using some filters</p>
     * <p>Steps</p>
     * <p>1. On backend in System -> Import/ Export -> Export select "Customers" entity type</p>
     * <p>2. In "Entity Type" dropdown field choose "Customers" parameter</p>
     * <p>3. In "Export Format Version" dropdown field choose "Magento 1.7" parameter</p>
     * <p>4. In the "Filter" column according to you attribute select option that was used in your customer creation</p>
     * <p>5. Press "Continue" button and save current file</p>
     * <p>6. Open file</p>
     * <p>Expected: In generated file just your customer with selected option of attribute is present</p>
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
        $this->importExportHelper()
            ->setFilter(array('firstname' => $userData['first_name']));
        //Steps 5-6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
        $this->assertEquals(1, count($report), "Other customers are present in csv file");
    }

    /**
     * <p>Export with skipped some attributes</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>3. In "Export Format Version" dropdown field choose "Magento 1.7 format" parameter</p>
     * <p>4. Select  "SKIP" checkbox for the row with the attribute First Name</p>
     * <p>5. In the "Filter" column for the attribute enter customer first name</p>
     * <p>6. Press "Continue" button and save file to your computer</p>
     * <p>7. Open exported file</p>
     * <p>Expected: file doesn't contain first name attribute</p>
     *
     * @test
     * @depends simpleExportMasterFile
     * @TestlinkId TL-MAGE-1194
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
        $this->importExportHelper()
            ->setFilter(array('firstname' => $userData['first_name']));
        //Step 6
        $report = $this->importExportHelper()->export();
        //Verifying
        $this->assertFalse(array_key_exists('firstname', $report[0]),
            'Skipped attribute was found in export file. Attribute Code: firstname'
        );
    }
}