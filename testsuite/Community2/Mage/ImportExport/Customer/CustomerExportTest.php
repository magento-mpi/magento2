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
        //make export page Url
        $pageUrl = $this->ImportExportHelper()->getUrl($this->getCurrentUimapPage()->getMca());
        //make export file Url
        $exportUrl = $pageUrl . '/export/entity/customer/file_format/csv';
        //prepare parameters array
        $parameters = $this->ImportExportHelper()->_prepareParameters();
        $parameters = $this->ImportExportHelper()->_prepareSkipAttributes($parameters);
        //get CSV file
        $report = $this->ImportExportHelper()->getFile($pageUrl,$exportUrl,$parameters);
        //convert Csv to array
        $report = $this->ImportExportHelper()->csvToArray($report);
     }
}