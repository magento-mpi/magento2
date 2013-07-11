<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

/**
 * Add new option to configurable attribute via product page.
 */
class Core_Mage_ProductAttribute_AddOptionsFromProductPageTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate Products -> Inventory -> Catalog.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Preconditions for creating configurable product.
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return $attributeData;
    }

    /**
     * Delete added value from configurable attribute on Product page
     *
     * @param array $attributeData
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGETWO-9
     */
    public function deleteOptionValue($attributeData)
    {
        //Data
        $newOption = $this->loadDataSet('Product', 'general_configurable_attribute_with_price/option_1',
            array('associated_attribute_value' => $this->generate('string', 15)));
        $attribute['attribute_1'] = $this->loadDataSet('Product', 'general_configurable_attribute_without_price',
            array('option_2' => $newOption),
            array(
                'general_attribute_1' => $attributeData['attribute_properties']['attribute_label'],
                'var1_attr_value1' => $attributeData['option_1']['admin_option_name']
            )
        );
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'general_configurable_attributes' => '%noValue%',
            'general_configurable_variations' => '%noValue%'
        ));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data', array(
            'attribute_code' => $attributeData['advanced_attribute_properties']['attribute_code']
        ));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->openProductTab('general');
        $this->productHelper()->fillConfigurableSettings($attribute, false);
        $this->assertTrue($this->controlIsVisible('button', 'delete_new_option'),
            '"Delete New Option" button is not visible for new option on the page');
        $this->clickControl('button', 'delete_new_option', false);
        $this->clickButton('generate_product_variations', false);
        $this->assertFalse($this->controlIsVisible('button', 'delete_new_option'),
            '"Delete New Option" button is visible for new option on the page');
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attributeData);
    }

    /**
     * Add new value to configurable attribute on Product page
     *
     * @param array $attributeData
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGETWO-8, TL-MAGETWO-10
     */
    public function addOptionValue($attributeData)
    {
        //Data
        $newOption = $this->loadDataSet('Product', 'general_configurable_attribute_with_price/option_1',
            array('associated_attribute_value' => ''));
        $attribute['attribute_1'] = $this->loadDataSet('Product', 'general_configurable_attribute_without_price',
            array('option_1' => $newOption),
            array('general_attribute_1' => $attributeData['attribute_properties']['attribute_label'])
        );
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'general_configurable_attributes' => '%noValue%',
            'general_configurable_variations' => '%noValue%'
        ));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data', array(
            'attribute_code' => $attributeData['advanced_attribute_properties']['attribute_code']
        ));
        $attributeData['option_4'] = array(
            'admin_option_name' => $newOption['associated_attribute_value'] = $this->generate('string', 15)
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->openProductTab('general');
        $this->productHelper()->fillConfigurableSettings($attribute, false);
        $this->clickButton('generate_product_variations', false);
        $this->addFieldIdToMessage(self::FIELD_TYPE_INPUT, 'associated_attribute_value');
        $this->assertMessagePresent(self::MESSAGE_TYPE_VALIDATION, 'empty_required_field');
        $this->fillField('associated_attribute_value', $attributeData['option_4']['admin_option_name']);
        $this->clickButton('generate_product_variations', false);
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($attributeData);
    }

    /**
     * Verify new option after save product on Product page
     *
     * @param array $attributeData
     *
     * @return array
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGETWO-11, TL-MAGETWO-12
     */
    public function verifyNewOptionAfterSave($attributeData)
    {
        //Data
        $newOption = $this->loadDataSet('Product', 'general_configurable_attribute_with_price/option_1',
            array('associated_attribute_value' => $this->generate('string', 15)));
        $attribute['attribute_1'] = $this->loadDataSet('Product', 'general_configurable_attribute_without_price',
            array('option_1' => $newOption),
            array('general_attribute_1' => $attributeData['attribute_properties']['attribute_label'])
        );
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'general_configurable_attributes' => $attribute,
            'general_configurable_variations' => '%noValue%'
        ));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data', array(
            'attribute_code' => $attributeData['advanced_attribute_properties']['attribute_code']
        ));
        $verifyOption = array('option_5' => array('admin_option_name' => $newOption['associated_attribute_value']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->productAttributeHelper()->verifyAttribute($verifyOption);
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        //@TODO remove when verification method for configurable options will be added
        $this->addParameter('title', $attributeData['store_view_titles']['Default Store View']);
        $this->addParameter('optionTitle', $newOption['associated_attribute_value']);
        $this->assertTrue($this->controlIsVisible(self::FIELD_TYPE_PAGEELEMENT, 'custom_option_select'));
    }
}