<?php
/**
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
 * @package Selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_ImportExport_BackwardCompatibility_AttributeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that after customer attribute creation it's shown in "Entity Attributes" block
     *
     * @test
     * @TestlinkId TL-MAGE-1310
     *
     * @return array
     */
    public function customerAttributeCreate()
    {
        //Step 1
        $this->navigate('manage_customer_attributes');
        //Steps 2-4
        $attrData = $this->loadDataSet('CustomerAttribute', 'customer_attribute_textfield',
            array('values_required' => 'No'));
        $this->attributesHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Steps 6-7
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']));
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');

        return $attrData;
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that after customer attribute updating it's updated in "Entity Attributes" block
     *
     * @test
     * @TestlinkId TL-MAGE-1310
     * @depends customerAttributeCreate
     * @TestlinkId TL-MAGE-1311
     * @param array $attrData
     */
    public function customerAttributeUpdate($attrData)
    {
        //Step 1
        $this->navigate('manage_customer_attributes');
        //Step 2
        $this->attributesHelper()->openAttribute(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']));
        //Step 3
        $attrData['attribute_properties']['attribute_label'] = 'Text_Field_Admin_'
            . $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillTabs(
            array(
                 'attribute_properties' => array(
                     'attribute_label' => $attrData['attribute_properties']['attribute_label']
                 )
            )
        );
        //Step 4
        $this->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Steps 6-7
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['attribute_properties']['attribute_label']
            )
        );
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
    }

    /**
     * Has been excluded from functionality scope
     * Need to verify that after customer attribute deletion it's not shown in "Entity Attributes" block
     *
     * @test
     * @TestlinkId TL-MAGE-1310
     * @depends customerAttributeCreate
     * @param $attrData
     * @TestlinkId TL-MAGE-1312
     *
     * @return void
     */
    public function customerAttributeDelete($attrData)
    {
        //Step 1
        $this->navigate('manage_customer_attributes');
        //Step 2
        $this->attributesHelper()->openAttribute(
            array('attribute_code'=>$attrData['attribute_properties']['attribute_code'])
        );
        //Step 3
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 4
        $this->navigate('export');
        //Steps 5
        $this->importExportHelper()->chooseExportOptions('Customers', 'Magento 1.7 format');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']));
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deleting');
    }

    /**
     * Need to verify that after product attribute creation it's shown in "Entity Attributes" block
     * Need to verify that after product attribute updating it's updated in "Entity Attributes" block
     * Need to verify that after product attribute deletion it's not shown in "Entity Attributes" block
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
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 5
        $this->navigate('export');
        //Step 6
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_code' => $attrData['advanced_attribute_properties']['attribute_code']));
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['advanced_attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 7
        $this->navigate('manage_attributes');
        //Step 8
        $this->productAttributeHelper()->openAttribute(
            array('attribute_code'=>$attrData['advanced_attribute_properties']['attribute_code'])
        );
        //Step 9
        $attrData['attribute_properties']['attribute_label'] = 'Text_Field_Admin_'
            . $this->generate('string', 5, ':lower:');
        $this->attributesHelper()->fillTabs(
            array(
                 'attribute_properties' => array(
                     'attribute_label' => $attrData['attribute_properties']['attribute_label']
                 )
            )
        );
        //Step 10
        $this->saveForm('save_attribute');
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Step 11
        $this->navigate('export');
        //Steps 12
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array('attribute_label' => $attrData['attribute_properties']['attribute_label'])
        );
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_label' => $attrData['attribute_properties']['attribute_label']),
            'grid_and_filter'
        );
        $this->assertNotNull($isFound, 'Attribute was not found after filtering');
        //Step 13
        $this->navigate('manage_attributes');
        //Step 14
        $this->productAttributeHelper()->openAttribute(
            array('attribute_code' => $attrData['advanced_attribute_properties']['attribute_code']));
        //Step 15
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        $this->assertMessagePresent('success', 'success_deleted_attribute');
        //Step 16
        $this->navigate('export');
        //Steps 17
        $this->importExportHelper()->chooseExportOptions('Products');
        //Verifying
        $this->importExportHelper()->customerFilterAttributes(
            array(
                'attribute_label' => $attrData['attribute_properties']['attribute_label'],
            )
        );
        $isFound = $this->importExportHelper()->customerSearchAttributes(
            array('attribute_code' => $attrData['advanced_attribute_properties']['attribute_code']),
            'grid_and_filter'
        );
        $this->assertNull($isFound, 'Attribute was found after deletion');
    }
}
