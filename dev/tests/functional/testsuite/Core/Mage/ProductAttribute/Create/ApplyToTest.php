<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Check the possibility to set user-defined attribute for selected product type
 */
class Core_Mage_ProductAttribute_Create_ApplyToTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     * Navigate to System - Manage Attributes.
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * Create user-defined attribute which applied only to Simple Product type
     *
     * @return array
     *
     * @test
     */
    public function createAttribute()
    {
        //Data
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown',
            array('apply_to' => 'Selected Product Types', 'apply_product_types' => 'Simple Product'));
        $associatedAttribute = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array ('assigned_attribute' => $attributeData['attribute_code'],
                      'title' => $attributeData['admin_title']);
        }

    /**
     * Verify that Apply To dropdown is enabled for new user-defined product attribute
     *
     * @test
     * @TestLinkId TL-MAGE-6424
     */
    public function checkApplyToEnabledForUserDefined()
    {
        //Steps
        $this->clickButton('add_new_attribute');
        //Verifying
        $this->assertTrue($this->controlIsPresent('dropdown', 'apply_to'), 'Apply To dropdown is absent');
        $this->assertFalse($this->controlIsPresent('multiselect', 'apply_product_types'));
        $element = $this->getControlElement('dropdown', 'apply_to');
        $this->assertTrue($element->enabled(), 'Apply To dropdown is disabled');
        $this->assertEquals('All Product Types', $this->select($element)->selectedLabel());
        //Steps
        $this->fillDropdown('apply_to', 'Selected Product Types');
        $this->assertTrue($this->controlIsPresent('multiselect', 'apply_product_types'),
            'Apply To multiselect is absent');
        $this->assertTrue($this->getControlElement('multiselect', 'apply_product_types')->enabled(),
            'Apply To multiselect is disabled');
    }

    /**
     * Verify that selection in Apply To control is used for selected product type's template
     *
     * @param array $attribute
     *
     * @test
     * @depends createAttribute
     * @TestLinkId TL-MAGE-6425
     */
    public function verifyDisplayingOnProductPage($attribute)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_required');
        $virtual = $this->loadDataSet('Product', 'virtual_product_required');
        $attributeTitle = $attribute['title'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple, 'simple', false);
        //Verifying presence for simple product type
        $this->openTab('general');
        $this->addParameter('attributeCodeDropdown', $attribute['assigned_attribute']);
        $this->assertTrue($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "Dropdown $attributeTitle is absent in this product type, but should not");
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($virtual, 'virtual', false);
        //Verifying absence for virtual product type
        $this->openTab('general');
        $this->assertFalse($this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "Dropdown $attributeTitle is present in this product type, but should not");
    }
}
