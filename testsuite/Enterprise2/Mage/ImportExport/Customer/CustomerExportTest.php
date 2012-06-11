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
 */
class Enterprise2_Mage_ImportExport_CustomerExportTest extends Mage_Selenium_TestCase
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
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "New Export"</p>
     * <p>Expected: dropdowns contain correct values</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5479
     */
    public function exportSettingsGeneralView()
    {
        //Verifying
        $entityTypes = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'entity_type') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --','Products','Customers'),$entityTypes,
            'Entity Type dropdown contains incorrect values');
        $fileFormat = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'file_format') . '/option',
            'text');
        $this->assertEquals(array('CSV'),$fileFormat,
            'Export File Format dropdown contains incorrect values');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'export_file_version') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --','Magento 1.7 format','Magento 2.0 format'),$exportFileVersion,
            'Export File Version dropdown contains incorrect values');
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'export_file') . '/option',
            'text');
        $this->assertEquals(array('Customers Main File','Customer Addresses','Customer Finances'),
            $exportFileVersion,
            'Export File Version dropdown contains incorrect values');
    }

    /**
     * <p>Verify the search by fields "Attribute Label" and "Attribute Code"</p>
     * <p>This search should work with each file type</p>
     * <p>Steps:</p>
     * <p>1. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>2. Select "Magento2.0" format</p>
     * <p>3. Select file type (Customers Main File/Customer Addresses/Customer Finances)</p>
     * <p>4. Type in "Attribute Code" field any name that is present in the list ('email'), click 'Search' button</p>
     * <p>5. Verify that attribute is found</p>
     * <p>6. Click 'Reset filter' button</p>
     * <p>7. Type in "Attribute Label" field any name that is present in the list ('Email'), click 'Search' button</p>
     * <p>8. Verify that attribute is found</p>
     * <p>6. Click 'Reset filter' button</p>
     * @test
     * @TestlinkId TL-MAGE-5482, TL-MAGE-5483, TL-MAGE-5495, TL-MAGE-5497, TL-MAGE-5496, TL-MAGE-5498
     */
    public function SearchByAttributeLabelCode()
    {

        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 2
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 3
        $arr = array('Customers Main File', 'Customer Addresses', 'Customer Finances');
        foreach($arr as $value) {

         $this->fillDropdown('export_file', $value);
         $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
         //Step 4
         $this->ImportExportHelper()->customerFilterAttributes(
                array(
                    'attribute_code' => 'email'));
         //Step 5
         $isFound = $this->ImportExportHelper()->customerSearchAttributes(
                array(
                    'attribute_code' => 'email'),
                'grid_and_filter'
         );
         $this->assertTrue(!is_null($isFound), 'Attribute was not found after filtering');
         //Step 6
         $this->clickButton('reset_filter', false);
         $this->waitForAjax();
         //Step 7
         $this->ImportExportHelper()->customerFilterAttributes(
                array(
                    'attribute_label' => 'Email'));
         //Step 8
         $isFound = $this->ImportExportHelper()->customerSearchAttributes(
                array(
                    'attribute_label' => 'Email'),
                'grid_and_filter'
            );
         $this->assertTrue(!is_null($isFound), 'Attribute was not found after filtering');
         //Step 9
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
    public function EntityAttributesBlockAllFileTypes()
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 2
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 3
        $this->fillDropdown('export_file', 'Customers Main File');
        //Verify
        $this->waitForElementVisible($this->_getControlXpath('fieldset','grid_and_filter'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
        //Step 4
        $isFound = $this->ImportExportHelper()->customerSkipAttribute(
            array(
                'attribute_label' => 'Created At'),
            'grid_and_filter'
        );
        $this->assertTrue($isFound, 'Checkbox was not found');
        //Step 5
        $arr = array('Customer Addresses', 'Customer Finances');
        foreach($arr as $value) {

            $this->fillDropdown('export_file', $value);
            //Verify
            $this->waitForElementVisible($this->_getControlXpath('fieldset','grid_and_filter'));
            $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
            //Step 6
            $isFound = $this->ImportExportHelper()->customerSkipAttribute(
                array(
                    'attribute_label' => 'Created At'),
                'grid_and_filter'
            );
            $this->assertFalse($isFound, 'Checkbox was not found');
        }
    }

    /**
     * <p>Simple Export Master file</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>3. Select new Export flow</p>
     * <p>4. Choose Customer (Master) file to export</p>
     * <p>5. Click on the Continue button</p>
     * <p>6. Save file to your computer</p>
     * <p>7. Open it.</p>
     * <p>Expected: Check that among all customers your customer with attribute is present</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5487
     */
    public function simpleExportMasterFile()
    {
        //Precondition: create customer
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('ImportExport.yml', 'generic_customer_account');

        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->admin('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
        //Step5-6
        $report = $this->ImportExportHelper()->export();
        //Verifying
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
    }

    /**
     * <p>Customer Master file export with using some filters</p>
     * <p>Steps</p>
     * <p>1. On backend in System -> Import/ Export -> Export select "Customers" entity type</p>
     * <p>2. Select the export version "Magento 2.0" and "Master Type File"</p>
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
        $this->navigate('manage_customer_attributes');
        $attrData = $this->loadDataSet('ImportExport.yml', 'generic_customer_attribute');
        $this->customerAttributeHelper()->createAttribute($attrData);
        $this->addParameter('attribute_name', $attrData['attribute_code']);
        $this->navigate('manage_customers');
        $userData = $this->loadDataSet('ImportExport.yml', 'customer_account_with_attribute');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        //Step 1
        $this->admin('export');
        $this->assertTrue($this->checkCurrentPage('export'), $this->getParsedMessages());
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
        //Step5-6
        $report = $this->ImportExportHelper()->export();
        //Verifying
        $userData[$attrData['attribute_code']] = $userData['custom_attribute'];
        unset($userData['custom_attribute']);
        $this->assertNotNull($this->importExportHelper()->lookForEntity('master', $userData, $report),
            "Customer not found in csv file");
    }
}