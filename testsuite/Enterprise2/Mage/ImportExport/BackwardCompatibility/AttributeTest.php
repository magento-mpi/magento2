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
 */
class Enterprise2_Mage_ImportExport_Backward_Export_Attribute_CustomerTest extends Mage_Selenium_TestCase
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
        $this->navigate('manage_customer_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet('CustomerAttribute','customer_attribute_textfield',
            array('values_required' => 'No'));
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Steps 6-7
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 8
        $this->navigate('manage_customer_attributes');
        //Step 9
        $this->attributesHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['properties']['attribute_code']));
        //Step 10
        $attrData['manage_labels_options']['admin_title'] = 'Text_Field_Admin_' . $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillTabs(array('manage_labels_options' => $attrData['manage_labels_options']));
        //Step 11
        $this->attributesHelper()->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 12
        $this->navigate('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']['admin_title']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 15
        $this->navigate('manage_customer_attributes');
        //Step 16
        $this->attributesHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['properties']['attribute_code']));
        //Step 17
        $this->clickButtonAndConfirm('delete_attribute','delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 18
        $this->navigate('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['properties']['attribute_code']
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deleting');
    }

    /**
     * <p>Need to verify that after product attribute creation it's shown in "Entity Attributes" block</p>
     * <p>Need to verify that after product attribute updating it's updated in "Entity Attributes" block</p>
     * <p>Need to verify that after product attribute deletion it's not shown in "Entity Attributes" block</p>
     * <p>Steps:</p>
     * <p>1. Go to Catalog -> Attributes -> Manage Attributes</p>
     * <p>2. Click "Add New Attribute" button</p>
     * <p>3. Fill required values</p>
     * <p>4. Click "Save Attribute" button</p>
     * <p>5. Go to System -> Import/ Export -> Export</p>
     * <p>6. In "Entity Type" drop-down field choose "Products" parameter</p>
     * <p>7. Go to Catalog -> Attributes -> Manage Attributes</p>
     * <p>8. Edit attribute from precondition</p>
     * <p>9. Change admin title</p>
     * <p>10. Click "Save Attribute" button</p>
     * <p>11. Go to System -> Import/ Export -> Export</p>
     * <p>12. In "Entity Type" drop-down field choose "Products" parameter</p>
     * <p>13. Go to Catalog -> Attributes -> Manage Attributes</p>
     * <p>14. Edit attribute from precondition</p>
     * <p>15. Click "Delete Attribute" button, confirm</p>
     * <p>16. Go to System -> Import/ Export -> Export</p>
     * <p>17. In "Entity Type" drop-down field choose "Products" parameter</p>
     * <p>Expected after steps 6: Check that added to system attribute is displayed in "Entity Attributes" list</p>
     * <p>Expected after step 12: Check that changes are applied for attribute in "Entity Attributes" block</p>
     * <p>Expected after step 17: Check that "Entity Attributes" block doesn't contain the attribute any more</p>
     *
     * @test
     * @TestlinkId TL-MAGE-1305, TL-MAGE-1306, TL-MAGE-1307
     */
    public function productAttributeInFilterGrid()
    {
        //Step 1
        $this->navigate('manage_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet('ProductAttribute','product_attribute_textfield');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Step 6
        $this->importExportHelper()->chooseExportOptions('Products');
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
        //Step 7
        $this->navigate('manage_attributes');
        //Step 8
        $this->productAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']
            )
        );
        //Step 9
        $attrData['manage_labels_options']['admin_title'] = 'Text_Field_Admin_'
            . $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillTabs(array('manage_labels_options' => $attrData['manage_labels_options']));
        //Step 10
        $this->attributesHelper()->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 11
        $this->navigate('export');
        //Steps 12
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']['admin_title'],
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']['admin_title'],
            ),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 13
        $this->navigate('manage_attributes');
        //Step 14
        $this->productAttributeHelper()->openAttribute(
            array(
                'attribute_code'=>$attrData['attribute_code']
            )
        );
        //Step 15
        $this->clickButtonAndConfirm('delete_attribute','delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 16
        $this->navigate('export');
        //Steps 17
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']['admin_title'],
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']['admin_title'],
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deletion');
    }
}
