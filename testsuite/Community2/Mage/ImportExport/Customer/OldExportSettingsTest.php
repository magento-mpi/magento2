<?php
/**
 * Created by JetBrains PhpStorm.
 * User: iglazunova
 * Date: 7/4/12
 * Time: 1:39 PM
 * To change this template use File | Settings | File Templates.
 */
class Community2_Mage_ImportExport_CustomerExportTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('export');
    }
    /**
     * <p>Old Export Settings General View</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "Magento 1.7 format"</p>
     * <p>Expected: The grid with attributes presents with buttons "Reset Filter", "Search", "Continue"</p>
     *<p>The same result should be if "Products" entity type is selected</p>
     * @test
     * @TestlinkId TL-MAGE-1181
     */
    public function oldExportSettingsGeneralView()
    {
        //Verifying
        $entityTypes = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'entity_type') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --', 'Products', 'Customers'), $entityTypes,
            'Entity Type dropdown contains incorrect values');
        $fileFormat = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'file_format') . '/option',
            'text');
        $this->assertEquals(array('CSV'), $fileFormat,
            'Export File Format dropdown contains incorrect values');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Verifying
        $exportFileVersion = $this->getElementsByXpath(
            $this->_getControlXpath('dropdown', 'export_file_version') . '/option',
            'text');
        $this->assertEquals(array('-- Please Select --', 'Magento 1.7 format', 'Magento 2.0 format'),
            $exportFileVersion,
            'Export File Version dropdown contains incorrect values');
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 1.7 format');
        //Verifying
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'grid_and_filter'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'reset_filter'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'search'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
        //Step 4
        $this->fillDropdown('entity_type', 'Products');
        //Verifying
        $this->waitForElementVisible($this->_getControlXpath('fieldset', 'grid_and_filter'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'reset_filter'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'search'));
        $this->waitForElementVisible($this->_getControlXpath('button', 'continue'));
    }
}