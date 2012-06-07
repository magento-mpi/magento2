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
     public function setUpBeforeTests()
    {
        //logged in once for all tests
        $this->loginAdminUser();
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
        //Step 1
        $this->navigate('export');
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
        $this->assertEquals(array('-- Please Select --','Customers Main File','Customer Addresses','Customer Finances'),
                $exportFileVersion,
                'Export File Version dropdown contains incorrect values');
    }
    /**
     * <p>Search by attribute label Master File</p>
     * <p>Steps</p>
     * <p>1. Go to System -> Import/ Export -> Export</p>
     * <p>2. In the drop-down "Entity Type" select "Customers"</p>
     * <p>3. Select "New Export"</p>
     * <p>4. Select Master File Type</p>
     * <p>5. Enter any existing attribute name in the "Attribute Label" field</p>
     * <p>6. Press "Search" button</p>
     * <p>7. SPress "Reset Filter" button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5482
     */
    public function SearchByAttributeLabel()
    {
        //Step 1
        $this->navigate('export');
        //Step 2
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForElementVisible($this->_getControlXpath('fildset', 'grid_and_filter'));
        //Step 5
        $this->SearchByAttributeLabel();

    }
}