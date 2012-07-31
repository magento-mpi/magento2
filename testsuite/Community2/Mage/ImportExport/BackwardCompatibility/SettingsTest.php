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
 *
 */
class Community2_Mage_ImportExport_Backward_ExportSettings_CustomerTest extends Mage_Selenium_TestCase
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
     * <p>2. In the drop-down "Entity Type" select "Products"</p>
     * <p>Expected: The grid with attributes presents with buttons "Reset Filter", "Search", "Continue"</p>
     * <p> The same result should be if "Products" entity type is selected</p>
     * @test
     * @TestlinkId TL-MAGE-1181
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
        $this->fillDropdown('entity_type', 'Products');
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