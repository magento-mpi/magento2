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
 *@package Selenium
 *@subpackage  tests
 *@license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_ImportExport_Backward_Export_Attribute_CustomerTest
    extends Mage_Selenium_TestCase
{

    /**
     * Preconditions:
     * Log in to Backend.
     * Navigate to System -> Export
     *
     * @return void
     */
    protected function assertPreConditions()
    {
        //logged in once for all tests
        $this->loginAdminUser();
        //Step 1
        $this->navigate('export');
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that after customer attribute creation it's shown in "Entity Attributes" block
     * Need to verify that after customer attribute updating it's updated in "Entity Attributes" block
     * Need to verify that after customer attribute deletion it's not shown in "Entity Attributes" block
     * Steps:
     * 1. Go to Customers -> Attributes -> Manage Customers Attributes
     * 2. Click "Add New Attribute" button
     * 3. Fill required values
     * 4. Click "Save Attribute" button
     * 5. Go to System -> Import/ Export -> Export
     * 6. In "Entity Type" drop-down field choose "Customers" parameter
     * 7. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter
     * 8. Go to Customers -> Attributes -> Manage Customer Attributes
     * 9. Edit attribute from precondition
     * 10. Change admin title
     * 11. Click "Save Attribute" button
     * 12. Go to System -> Import/ Export -> Export
     * 13. In "Entity Type" drop-down field choose "Customers" parameter
     * 14. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter
     * 15. Go to Customers -> Attributes -> Manage Customer Attributes
     * 16. Edit attribute from precondition
     * 17. Click "Delete Attribute" button, confirm
     * 18. Go to System -> Import/ Export -> Export
     * 19. In "Entity Type" drop-down field choose "Customers" parameter
     * 20. In "Export Format Version" drop-down field choose "Magento 1.7 format" parameter
     * Expected after steps 7: Check that added to system attribute is displayed in "Entity Attributes" list
     * Expected after step 14: Check that changes are applied for attribute in "Entity Attributes" block
     * Expected after step 20: Check that "Entity Attributes" block doesn't contain the attribute any more
     *
     * @test
     * @TestlinkId TL-MAGE-1310, TL-MAGE-1311, TL-MAGE-1312
     * @group skip_due_to_bug
     *
     * @return void
     */
    public function customerAttributeInFilterGrid()
    {
        //Step 1
        $this->navigate('manage_customer_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet(
            'CustomerAttribute', 'customer_attribute_textfield',
            array(
                'values_required' => 'No'
            )
        );
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Steps 6-7
        $this->importExportHelper()->chooseExportOptions(
            'Customers', 'Magento 1.7 format'
        );
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
                'attribute_code'=>$attrData['properties']['attribute_code']
            )
        );
        //Step 10
        $attrData['manage_labels_options']
        ['admin_title'] = 'Text_Field_Admin_' .
            $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillTabs(
            array(
                'manage_labels_options' => $attrData['manage_labels_options']
            )
        );
        //Step 11
        $this->attributesHelper()->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 12
        $this->navigate('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions(
            'Customers',
            'Magento 1.7 format'
        );
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']
                ['admin_title']
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
                'attribute_code'=>$attrData['properties']['attribute_code']
            )
        );
        //Step 17
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 18
        $this->navigate('export');
        //Steps 13-14
        $this->importExportHelper()->chooseExportOptions(
            'Customers',
            'Magento 1.7 format'
        );
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
     * Need to verify that after product attribute creation it's shown in "Entity Attributes" block
     * Need to verify that after product attribute updating it's updated in "Entity Attributes" block
     * Need to verify that after product attribute deletion it's not shown in "Entity Attributes" block
     * Steps:
     * 1. Go to Catalog -> Attributes -> Manage Attributes
     * 2. Click "Add New Attribute" button
     * 3. Fill required values
     * 4. Click "Save Attribute" button
     * 5. Go to System -> Import/ Export -> Export
     * 6. In "Entity Type" drop-down field choose "Products" parameter
     * 7. Go to Catalog -> Attributes -> Manage Attributes
     * 8. Edit attribute label from precondition
     * 9. Change admin title
     * 10. Click "Save Attribute" button
     * 11. Go to System -> Import/ Export -> Export
     * 12. In "Entity Type" drop-down field choose "Products" parameter
     * 13. Go to Catalog -> Attributes -> Manage Attributes
     * 14. Open edit page of attribute from precondition
     * 15. Click "Delete Attribute" button, confirm
     * 16. Go to System -> Import/ Export -> Export
     * 17. In "Entity Type" drop-down field choose "Products" parameter
     * Expected after steps 6: Check that added to system attribute is displayed in "Entity Attributes" list
     * Expected after step 12: Check that changes are applied for attribute in "Entity Attributes" block
     * Expected after step 17: Check that "Entity Attributes" block doesn't contain the attribute any more
     *
     * @return void
     * @test
     * @TestlinkId TL-MAGE-5925, TL-MAGE-5926, TL-MAGE-5927
     */
    public function productAttributeInFilterGrid()
    {
        //Step 1
        $this->navigate('manage_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet(
            'ProductAttribute',
            'product_attribute_textfield'
        );
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
        $this->attributesHelper()->fillTabs(
            array(
                'manage_labels_options' => $attrData['manage_labels_options']
            )
        );
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
                'attribute_label' => $attrData['manage_labels_options']
                    ['admin_title'],
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']
                    ['admin_title'],
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
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 16
        $this->navigate('export');
        //Steps 17
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->ImportExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['manage_labels_options']
                    ['admin_title'],
            )
        );
        $isFound = $this->ImportExportHelper()->customerSearchAttributes(
            array(
                'attribute_code' => $attrData['attribute_code'],
            ),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deletion');
    }
}
