<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete product attributes
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_DeleteTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System -> Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * Delete Product Attributes
     *
     * @param $dataName
     *
     * @test
     * @dataProvider deleteProductAttributeDeletableDataProvider
     * @TestlinkId TL-MAGE-3343
     */
    public function deleteProductAttributeDeletable($dataName)
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', $dataName);
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $attrData['attribute_code']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_attribute');
    }

    public function deleteProductAttributeDeletableDataProvider()
    {
        return array(
            array('product_attribute_textfield'),
            array('product_attribute_textarea'),
            array('product_attribute_date'),
            array('product_attribute_yesno'),
            array('product_attribute_multiselect'),
            array('product_attribute_dropdown'),
            array('product_attribute_price'),
            array('product_attribute_fpt')
        );
    }

    /**
     * Delete system Product Attributes
     *
     * @test
     * @TestlinkId TL-MAGE-3342
     */
    public function deletedSystemAttribute()
    {
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code'  => 'description',
                  'attribute_label' => 'Description',
                  'system'          => 'Yes'));
        //Steps
        $this->productAttributeHelper()->openAttribute($searchData);
        //Verifying
        $this->assertFalse($this->buttonIsPresent('delete_attribute'),
            '"Delete Attribute" button is present on the page');
    }

    /**
     * Delete attribute that used in Configurable Product
     *
     * @test
     */
    public function deletedDropdownAttributeUsedInConfigurableProduct()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['attribute_code']));
        $productData = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['attribute_label']));
        $searchData = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code'  => $attrData['attribute_code'],
                  'attribute_label' => $attrData['attribute_label']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->openAttribute($searchData);
        $this->clickButtonAndConfirm('delete_attribute', 'delete_confirm_message');
        //Verifying
        $this->assertMessagePresent('error', 'attribute_used_in_configurable');
    }
}
