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
 * Customer Backward Compatibility Tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *
 * @method Enterprise2_Mage_CustomerAttribute_Helper customerAttributeHelper() customerAttributeHelper()
 * @method Enterprise2_Mage_CustomerAddressAttribute_Helper customerAddressAttributeHelper() customerAddressAttributeHelper()
 * @method Enterprise2_Mage_ImportExport_Helper importExportHelper() importExportHelper()
 */
class Enterprise2_Mage_ImportExport_Backward_Export_Attribute_CustomerTest extends Mage_Selenium_TestCase
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
     * <p>Need to verify that after customer attribute creation it's shown in "Entity Attributes" block</p>
     * <p>Need to verify that after customer attribute updating it's updated in "Entity Attributes" block</p>
     * <p>Need to verify that after customer attribute deletion it's not shown in "Entity Attributes" block</p>
     * <p>Steps:</p>
     * <p>1. Go to Customers -> Attributes -> Manage Customers Attributes</p>
     * <p>2. Click "Add New Attribute" button</p>
     * <p>3. Fill required values</p>
     * <p>4. Click "Save Attribute" button</p>
     * <p>5. Go to System -> Import/ Export -> Export</p>
     * <p>6. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>7. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter</p>
     * <p>8. Go to Customers -> Attributes -> Manage Customer Attributes</p>
     * <p>9. Edit attribute from precondition</p>
     * <p>10. Change admin title</p>
     * <p>11. Click "Save Attribute" button</p>
     * <p>12. Go to System -> Import/ Export -> Export</p>
     * <p>13. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>14. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter</p>
     * <p>15. Go to Customers -> Attributes -> Manage Customer Attributes</p>
     * <p>16. Edit attribute from precondition</p>
     * <p>17. Click "Delete Attribute" button, confirm</p>
     * <p>18. Go to System -> Import/ Export -> Export</p>
     * <p>19. In "Entity Type" drop-down field choose "Customers" parameter</p>
     * <p>20. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter</p>
     * <p>Expected after steps 7: Check that added to system attribute is displayed in "Entity Attributes" list</p>
     * <p>Expected after step 14: Check that changes are applied for attribute in "Entity Attributes" block</p>
     * <p>Expected after step 20: Check that "Entity Attributes" block doesn't contain the attribute any more</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1310, TL-MAGE-1311, TL-MAGE-1312
     */
    public function customerAttributeInFilterGrid()
    {
        //Step 1
        $this->admin('manage_customer_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet('CustomerAttribute','generic_customer_attribute');
        $this->customerAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->admin('export');
        //Steps 6-7
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 8
        $this->admin('manage_customer_attributes');
        //Step 9
        $this->customerAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']));
        //Step 10
        $attrData['admin_title'] = 'Text_Field_Admin_' . $this->generate('string', 5, ':lower:');
        $this->customerAttributeHelper()->fillform($attrData, 'manage_labels_options');
        //Step 11
        $this->customerAttributeHelper()->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 12
        $this->admin('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['admin_title']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 15
        $this->admin('manage_customer_attributes');
        //Step 16
        $this->customerAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']));
        //Step 17
        $this->clickButtonAndConfirm('delete_attribute','delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 18
        $this->admin('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deleting');
    }
}
