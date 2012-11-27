<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products creation tests with ability to change attribute set during creation and editing products
 */
class Community2_Mage_Product_Create_ConfigurableWithVariations extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog - Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Preconditions for creating configurable product.</p>
     * <p>Create 2 dropdown attributes with 3 options, Global scope and assign it to created Attribute Set</p>
     *
     * @return array
     * @test
     */
    public function setConfigurableAttributesToNewSet()
    {
        //Data
        $attributeFirst = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attributeSecond = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttribute1 = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeFirst['attribute_code']));
        $associatedAttribute2 = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeSecond['attribute_code']));
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        //Steps (attributes)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeFirst);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attributeSecond);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->attributeSetHelper()->openAttributeSet($attributeSet['set_name']);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute1);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute2);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array('attribute1' => $attributeFirst['admin_title'],
            'attribute2' => $attributeSecond['admin_title'],
            'attributeSet' => $attributeSet['set_name'],
            'matrix' => array(
                '1' => array ('1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']),
                '2' => array ('1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']),
                '3' => array ('1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name']),
                '4' => array ('1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']),
                '5' => array ('1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']),
                '6' => array ('1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name']),
                '7' => array ('1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']),
                '8' => array ('1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']),
                '9' => array ('1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name'])),
        );
    }

    /**
     * <p>Preconditions for creating configurable product.</p>
     * <p>Create 2 dropdown attributes with 3 options, Global scope and assign it to created Attribute Set</p>
     *
     * @return array
     * @test
     */
    public function setConfigurableAttributesToDefault()
    {
        //Data
        $attributeThird = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attributeForth = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttribute3 = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeThird['attribute_code']));
        $associatedAttribute4 = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeForth['attribute_code']));
        //Steps (attributes)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeThird);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attributeForth);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute3);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute4);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array('attribute1' => $attributeThird['admin_title'],
            'attribute2' => $attributeForth['admin_title'],
            'matrix' => array(
                '1' => array ('1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']),
                '2' => array ('1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']),
                '3' => array ('1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name']),
                '4' => array ('1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']),
                '5' => array ('1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']),
                '6' => array ('1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name']),
                '7' => array ('1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']),
                '8' => array ('1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']),
                '9' => array ('1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name'])
            ),
        );
    }

    /**
     * <p>Configurable Product with Product Variations</p>
     *
     * @param array $defaultData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6476
     */
    public function checkGeneratedMatrixWhileCreate($defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'configurable_attribute_title' => array($defaultData['attribute1'], $defaultData['attribute2']),
        ));
        $this->productHelper()->selectTypeProduct('configurable');
        $this->productHelper()->fillProductInfo($productData);
        $this->openTab('general');
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')));
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'));
        $productData['configurable_attribute_title'] = $defaultData['attribute1'] . ', ' . $defaultData['attribute2'];
        $this->productHelper()->fillConfigurableSettings($productData);
        //Verifying
        $this->assertTrue($this->controlIsVisible('fieldset', 'variations_matrix'));
        $this->productHelper()->checkGeneratedMatrix($defaultData['matrix']);
    }

    /**
     * <p>Variation Matrix with Changing Attribute Set</p>
     *
     * @param array $data
     *
     * @test
     * @depends setConfigurableAttributesToNewSet
     * @TestlinkId TL-MAGE-6477
     */
    public function validMatrixAfterChangeAttributeSet($data)
    {
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')));
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'));
        $this->productHelper()->changeAttributeSet($data['attributeSet']);
        $this->openTab('general');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')));
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'));
        $this->productHelper()->fillConfigurableSettings(array(
                'configurable_attribute_title' => $data['attribute1'] . ', ' . $data['attribute2']
            )
        );
        //Verifying
        $this->productHelper()->checkGeneratedMatrix($data['matrix']);
    }
}
