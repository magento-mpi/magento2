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
class Community2_Mage_Product_Create_ProductVariationsTest extends Mage_Selenium_TestCase
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

        return array(
            'attribute1' => $attributeFirst['admin_title'],
            'attribute2' => $attributeSecond['admin_title'],
            'attributeSet' => $attributeSet['set_name'],
            'matrix' => array(
                '1' => array(
                    '1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']
                ),
                '2' => array(
                    '1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']
                ),
                '3' => array(
                    '1' => $attributeFirst['option_1']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name']
                ),
                '4' => array(
                    '1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']
                ),
                '5' => array(
                    '1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']
                ),
                '6' => array(
                    '1' => $attributeFirst['option_2']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name']
                ),
                '7' => array(
                    '1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_1']['admin_option_name']
                ),
                '8' => array(
                    '1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_2']['admin_option_name']
                ),
                '9' => array(
                    '1' => $attributeFirst['option_3']['admin_option_name'],
                    '2' => $attributeSecond['option_3']['admin_option_name']
                )
            ),
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

        return array(
            'attribute1' => $attributeThird['admin_title'],
            'attribute2' => $attributeForth['admin_title'],
            'matrix' => array(
                '1' => array(
                    '1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']
                ),
                '2' => array(
                    '1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']
                ),
                '3' => array(
                    '1' => $attributeThird['option_1']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name']
                ),
                '4' => array(
                    '1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']
                ),
                '5' => array(
                    '1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']
                ),
                '6' => array(
                    '1' => $attributeThird['option_2']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name']
                ),
                '7' => array(
                    '1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_1']['admin_option_name']
                ),
                '8' => array(
                    '1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_2']['admin_option_name']
                ),
                '9' => array(
                    '1' => $attributeThird['option_3']['admin_option_name'],
                    '2' => $attributeForth['option_3']['admin_option_name']
                )
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
    public function checkGeneratedMatrix($defaultData)
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
    public function checkGeneratedMatrixAfterChangeAttributeSet($data)
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

    /**
     * <p>Create simple product via product variation grid in configurable product</p>
     *
     * @param array $defaultData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6519
     */
    public function createSimpleViaVariationGrid($defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $defaultData['attribute1']));
        $fillVariation = $this->loadDataSet('Product', 'product_variation',
            array('variation_qty' => 12, 'variation_weight' => 12));
        $associatedProductData = $this->loadDataSet('Product', 'associated_configurable_data',
            array(
                'associated_search_sku' => $fillVariation['variation_sku'],
                'associated_product_attribute_value' => $defaultData['matrix'][1][1]
            )
        );
        $verifySimple = $productData;
        unset($verifySimple['configurable_attribute_title']);
        $verifySimple = array_replace($verifySimple,
            array(
                'general_name' => $fillVariation['variation_name'],
                'general_sku' => $fillVariation['variation_sku'],
                'general_weight' => $fillVariation['variation_weight'],
                'inventory_qty' => $fillVariation['variation_qty'],
                'prices_price' => $fillVariation['variation_price']
            )
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->includeAssociatedProduct(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][1])),
            $fillVariation
        );
        $this->clickButton('save');
        $productData['associated_configurable_data'] = $associatedProductData;
        //Verify configurable
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('configurable_attribute_title'));
        $this->navigate('manage_products');
        //Verify simple
        $this->assertEquals('Simple Product', $this->productHelper()->getProductType($verifySimple),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('product_sku' => $verifySimple['general_sku']));
        $this->productHelper()->verifyProductInfo($verifySimple, array('general_visibility'));
    }
    /**
     * <p>Create virtual product via product variation grid in configurable product</p>
     *
     * @param array $defaultData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6521
     */
    public function createVirtualViaVariationGrid($defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $defaultData['attribute1']));
        $fillVariation = $this->loadDataSet('Product', 'product_variation', array('variation_qty' => 12));
        $associatedProductData = $this->loadDataSet('Product', 'associated_configurable_data',
            array(
                'associated_search_sku' => $fillVariation['variation_sku'],
                'associated_product_attribute_value' => $defaultData['matrix'][1][1]
            )
        );
        $verifyVirtual = $productData;
        unset($verifyVirtual['configurable_attribute_title']);
        $verifyVirtual = array_replace($verifyVirtual,
            array(
                'general_name' => $fillVariation['variation_name'],
                'general_sku' => $fillVariation['variation_sku'],
                'prices_price' => $fillVariation['variation_price'],
                'inventory_qty' => $fillVariation['variation_qty']
            )
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->includeAssociatedProduct(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][1])),
            $fillVariation
        );
        $this->clickButton('save');
        $productData['associated_configurable_data'] = $associatedProductData;
        //Verify configurable
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('configurable_attribute_title'));
        //Verify virtual
        $this->navigate('manage_products');
        $this->assertEquals('Virtual Product', $this->productHelper()->getProductType($verifyVirtual),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('product_sku' => $verifyVirtual['general_sku']));
        $this->productHelper()->verifyProductInfo($verifyVirtual, array('general_visibility'));
    }

    /**
     * <p>Verify Manage Stock option for created product via product variation in configurable product</p>
     *
     * @param array $defaultData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6525
     */
    public function verifyManageStock($defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $defaultData['attribute1']));
        $fillInStock = $this->loadDataSet('Product', 'product_variation', array('variation_qty' => 12));
        $fillOutOfStock = $this->loadDataSet('Product', 'product_variation');
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->includeAssociatedProduct(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][1])),
            $fillInStock
        );
        $this->productHelper()->includeAssociatedProduct(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][4][1])),
            $fillOutOfStock
        );
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verify virtual with Manage Stock - Yes
        $this->productHelper()->openProduct(array('product_sku' => $fillInStock['variation_sku']));
        $this->productHelper()->verifyProductInfo(array(
            'inventory_qty' => $fillInStock['variation_qty'], 'inventory_manage_stock' => 'Yes'));
        //Verify virtual with Manage Stock - No
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_sku' => $fillOutOfStock['variation_sku']));
        $this->productHelper()->verifyProductInfo(array('inventory_manage_stock' => 'No'));
    }
    /**
     * <p>Verify required fields validation in variation matrix</p>
     *
     * @param string $emptyField
     * @param array $defaultData
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6523
     */
    public function withRequiredFieldsEmpty($emptyField, $defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $defaultData['attribute1']));
        $associatedProductData = $this->loadDataSet('Product', 'product_variation', array($emptyField => ''));
        preg_match('/\w+\_(\w+)/', $emptyField, $result);
        $field = $result[1];
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->includeAssociatedProduct(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][1])),
            $associatedProductData);
        $this->addParameter('field', $field);
        $this->saveForm('save', false);
        //Verifying
        $this->assertMessagePresent('validation', 'required_field');
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('variation_name'),
            array('variation_price'),
            array('variation_sku')
        );
    }
    /**
     * <p>Verification variation fields in configurable product</p>
     *
     * @param array $defaultData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6522
     */
    public function verificationVariationFields($defaultData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $defaultData['attribute1']));
        $productData['general_weight_and_type_switcher'] = 'no';
        $productData['general_weight'] = 12;
        $verifyData = array(
            'variation_name' => $productData['general_name'] . '-' . $defaultData['matrix'][1][1],
            'variation_price'=> $productData['prices_price'],
            'variation_sku' => $productData['general_sku'] . '-' . $defaultData['matrix'][1][1],
            'variation_weight'=> $productData['general_weight']
        );
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->productHelper()->fillProductInfo($productData, 'configurable');
        $this->productHelper()->fillConfigurableSettings($productData);
        //Verifying
        $this->assertEquals($verifyData['variation_name'],
            $this->getValue($this->_getControlXpath('field', 'variation_name')));
        $this->assertEquals($verifyData['variation_price'],
            $this->getValue($this->_getControlXpath('field', 'variation_price')));
        $this->assertEquals($verifyData['variation_sku'],
            $this->getValue($this->_getControlXpath('field', 'variation_sku')));
        $this->assertEquals($verifyData['variation_weight'],
            $this->getValue($this->_getControlXpath('field', 'variation_weight')));
    }
}
