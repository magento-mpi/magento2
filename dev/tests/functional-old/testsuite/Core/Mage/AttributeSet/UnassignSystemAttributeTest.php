<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
/**
 * Verifying the ability to unassign system attributes from attribute set
 */
class Core_Mage_AttributeSet_UnassignSystemAttributeTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attribute_sets');
    }

    /**
     * Create new attribute set based on Default.
     *
     * @return string
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $setData = $this->loadDataSet('AttributeSet', 'attribute_set');
        //Steps
        $this->attributeSetHelper()->createAttributeSet($setData);
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $setData['set_name'];
    }

    /**
     * Remove system attribute group
     *
     * @param string $setName
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6126
     */
    public function deleteGroupWithoutAttributes($setName)
    {
        //Data
        $attributeCodes = array('meta_title', 'meta_keyword', 'meta_description');
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        foreach ($attributeCodes as $attributeCode) {
            $this->attributeSetHelper()->unassignAttributeFromSet(array($attributeCode));
            $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode), false);
        }
        $this->attributeSetHelper()->deleteGroup(array('Search Engine Optimization'));
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'group_folder'),
            '"Search Engine Optimization" group was not deleted');
    }

    /**
     * Remove system attribute group with system attributes
     *
     * Expected results:
     *  1. Search Engine Optimization group has been deleted.
     *
     * @param string $setName
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6130
     */
    public function deleteGroupWithAttributes($setName)
    {
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        $this->attributeSetHelper()->deleteGroup(array('Search Engine Optimization'));
        //Verifying
        $this->assertFalse($this->controlIsPresent('link', 'group_folder'),
            '"Search Engine Optimization" group was not deleted');
    }

    /**
     * Remove system attributes from Default attribute set
     *
     * @param string $attributeCode
     * @param string $setName
     *
     * @test
     * @dataProvider unassignableSystemAttributesDataProvider
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6124
     */
    public function fromDefaultAttributeSet($attributeCode, $setName)
    {
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        $this->attributeSetHelper()->unassignAttributeFromSet(array($attributeCode));
        //Verifying
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode), false);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
    }

    /**
     * DataProvider for system attributes, which can be unassigned
     *
     * @return array
     */
    public function unassignableSystemAttributesDataProvider()
    {
        return array(
            array('cost'),
            array('country_of_manufacture'),
            array('custom_design'),
            array('custom_design_from'),
            array('custom_design_to'),
            array('custom_layout_update'),
            array('gallery'),
            array('is_recurring'),
            array('media_gallery'),
            array('meta_description'),
            array('meta_keyword'),
            array('meta_title'),
            array('msrp'),
            array('msrp_display_actual_price_type'),
            array('msrp_enabled'),
            array('news_from_date'),
            array('news_to_date'),
            array('options_container'),
            array('page_layout'),
            array('recurring_payment'),
            array('small_image'),
            array('special_from_date'),
            array('special_price'),
            array('special_to_date'),
            array('thumbnail'),
            array('url_key')
        );
    }

    /**
     * Create simple product based on modified Default attribute set
     *
     * @param string $attributeCode
     *
     * @test
     * @depends preconditionsForTests
     * @depends fromDefaultAttributeSet
     * @TestLinkId TL-MAGE-6125
     */
    public function verifyOnProductPage($attributeCode)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('product_attribute_set' => $attributeCode));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Non removable system attributes
     *
     * @param string $attributeCode
     *
     * @test
     * @dataProvider nonUnassignableSystemAttributesDataProvider
     * @TestLinkId TL-MAGE-6128
     */
    public function verifyBasicAttributes($attributeCode)
    {
        //Data
        $setName = 'Default';
        //Steps
        $this->attributeSetHelper()->openAttributeSet($setName);
        $this->attributeSetHelper()->unassignAttributeFromSet(array($attributeCode), true);
        //Verifying
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode));
    }

    /**
     * DataProvider with list of non unassignable system attributes
     *
     * @return array
     */
    public function nonUnassignableSystemAttributesDataProvider()
    {
        return array(
            array('category_ids'),
            array('description'),
            array('gift_message_available'),
            array('group_price'),
            array('image'),
            array('name'),
            array('price'),
            array('price_view'),
            array('quantity_and_stock_status'),
            array('short_description'),
            array('sku'),
            array('status'),
            array('tax_class_id'),
            array('tier_price'),
            array('visibility'),
            array('weight'),
        );
    }
}
