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
class Enterprise2_Mage_ImportExport_CustomerAttributeTest extends Mage_Selenium_TestCase
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
     * <p>Add customer attribute</p>
     * <p>Steps</p>
     * <p>1. Admin is logged in at backend</p>
     * <p>2. New Customers Attribute is created in Customers -> Attributes -> Manage Customers Attributes</p>
     * <p>3. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>4. Select "Magento2.0" format and "Master Type" file</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5484
     * @return array
     */
    public function addCustomerAttribute()
    {
        //step1
        $this->admin('manage_customer_attributes');
        $attrData = $this->loadDataSet('ImportExport','generic_customer_attribute');
        $this->customerAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 2
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForAjax();
        //Step 5
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']));
        //Step 6
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']),
            'grid_and_filter'
        );
        $this->assertTrue(!is_null($isFound), 'Attribute was not found after filtering');
        //Step 7
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        return $attrData;
    }

    /**
     * <p>Edit customer attribute</p>
     * <p>Steps</p>
     * <p>1. Admin is logged in at backend</p>
     * <p>2. In Customers -> Attributes -> Manage Customers Attributes change a info in the field "Attribute Label" for existing Customer Attribute</p>
     * <p>3. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>4. Select "Magento2.0" format and "Master Type" file</p>
     *
     * @test
     * @param array $attrData
     * @depends addCustomerAttribute
     * @TestlinkId TL-MAGE-5485
     */
    public function editCustomerAttribute($attrData)
    {
        //step1
        $this->admin('manage_customer_attributes');
        $this->customerAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']));
        //Change label
        $attrData['admin_title'] = 'Text_Field_Admin_' . $this->generate('string', 5, ':lower:');
        $this->customerAttributeHelper()->fillForm($attrData, 'manage_labels_options');
        $this->customerAttributeHelper()->saveForm('save_attribute');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 2
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForAjax();
        //Step 5
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['attribute_code'],
                'attribute_label' => $attrData['attribute_label'])
            );
        //Step 6
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code'],
                'attribute_label' => $attrData['attribute_label']),
            'grid_and_filter'
            );
        $this->assertTrue(!is_null($isFound), 'Attribute was not found after filtering');
        //Step 7
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
        return $attrData;
    }

    /**
     * <p>Edit customer attribute</p>
     * <p>Steps</p>
     * <p>1. Admin is logged in at backend</p>
     * <p>2. Create new customer attribute in Customers -> Attributes -> Manage Customers Attributes</p>
     * <p>3. Delete the attribute from precondition 2 in Customers -> Attributes -> Manage Customers Attributes</p>
     * <p>4. In System-> Import/Export-> Export select "Customers" entity type</p>
     * <p>5. Select "Magento2.0" format and "Master Type" file</p>
     *
     * @test
     * @param array $attrData
     * @depends editCustomerAttribute
     * @TestlinkId TL-MAGE-5486
     */
    public function deleteCustomerAttribute($attrData)
    {
        //step1
        $this->admin('manage_customer_attributes');
        $this->customerAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']));
        //Delete attribute
        $this->clickButtonAndConfirm('delete_attribute','delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 2
        $this->admin('export');
        $this->fillDropdown('entity_type', 'Customers');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file_version'));
        //Step 3
        $this->fillDropdown('export_file_version', 'Magento 2.0 format');
        $this->waitForElementVisible($this->_getControlXpath('dropdown', 'export_file'));
        //Step 4
        $this->fillDropdown('export_file', 'Customers Main File');
        $this->waitForAjax();
        //Step 5
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['attribute_code'],
                'attribute_label' => $attrData['attribute_label'])
        );
        //Step 6
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code'],
                'attribute_label' => $attrData['attribute_label']),
            'grid_and_filter'
        );
        $this->assertTrue(is_null($isFound), 'Attribute was found after deleting');
        //Step 7
        $this->clickButton('reset_filter', false);
        $this->waitForAjax();
    }

}