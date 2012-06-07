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
class Community2_Mage_ImportExport_CustomerExportTest extends Mage_Selenium_TestCase
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
     * @test
     */
    public function simpleExport()
    {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        $this->fillDropdown('export_file_version', 'Magento 1.7 format');
        $this->waitForAjax();
        $report = $this->ImportExportHelper()->export();
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
      * @test
      */
     public function simpleExportCustomer()
     {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForAjax();
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForAjax();
        $customersMain = $this->ImportExportHelper()->export();
        $this->fillDropdown('export_file', 'Customer Addresses');
        $this->waitForAjax();
        $customerAddresses = $this->ImportExportHelper()->export();
        $this->fillDropdown('export_file', 'Customer Finances');
        $this->waitForAjax();
        $customerFinances = $this->ImportExportHelper()->export();
     }

     /**
      * @test
      */
     public function simpleAttributeFilterAndSearch()
     {
        //Step 1
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForAjax();
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForAjax();
        $this->ImportExportHelper()->customerFilterAttributes(
                array(
                    'attribute_label' => 'Created At',
                    'attribute_code' => 'created_at')
                );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
                array(
                    'attribute_label' => 'Created At',
                    'attribute_code' => 'created_at'),
                'grid_and_filter'
                );
        $this->assertTrue(!is_null($isFound), 'Attribute was not found after filtering');
        //mark attribute as skipped
        $this->ImportExportHelper()->customerSkipAttribute(
                array(
                    'attribute_label' => 'Created At',
                    'attribute_code' => 'created_at'),
                'grid_and_filter'
                );
     }
}