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
     * <p>Precondition1:</p>
     * <p>1 Verify the search by fields "Attribute Label" and "Attribute Code"</p>
     * <p>2 This search should work with each file type </p>
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
}