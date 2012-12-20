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
class Core_Mage_Product_Create_ConfigurableWithVariations extends Mage_Selenium_TestCase
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
     * Create matrix of attribute value combinations for two attributes
     *
     * @param array $attribute1
     * @param array $attribute2
     *
     * @return array
     */
    protected function _getVariationsForTwoAttributes(array $attribute1, array $attribute2)
    {
        $variations = array();
        $variation = 1;
        $optionNumber1 = count(preg_grep("/option_\N/", array_keys($attribute1)));
        $optionNumber2 = count(preg_grep("/option_\N/", array_keys($attribute2)));
        for($i = 1; $i <= $optionNumber1; $i++) {
            for($j = 1; $j <= $optionNumber2; $j++) {
                $variations[$variation] = array(
                    '6' => $attribute1['option_' . $i]['admin_option_name'],
                    '7' => $attribute2['option_' . $j]['admin_option_name']
                );
                $variation++;
            }
        }
        return $variations;
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
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('General' => array($attributeFirst['attribute_code'], $attributeSecond['attribute_code'])));
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

        return array(
            'attribute'    => array($attributeFirst['admin_title'], $attributeSecond['admin_title']),
            'attributeSet' => $attributeSet['set_name'],
            'matrix' => $this->_getVariationsForTwoAttributes($attributeFirst, $attributeSecond),
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
        $associatedAttribute = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => array($attributeThird['attribute_code'], $attributeForth['attribute_code'])));
        //Steps (attributes)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeThird);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attributeForth);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'attribute' => array($attributeThird['admin_title'], $attributeForth['admin_title']),
            'matrix' => $this->_getVariationsForTwoAttributes($attributeThird, $attributeForth)
        );
    }

    /**
     * <p>Configurable Product with Product Variations</p>
     *
     * @param array $data
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6476
     */
    public function checkGeneratedMatrix($data)
    {
        //Data
        $this->productHelper()->selectTypeProduct('configurable');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'));
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue'));
        $this->productHelper()->fillConfigurableSettings($data['attribute']);
        //Verifying
        $this->productHelper()->verifyConfigurableVariations($data['matrix'], true);
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
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'));
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue'));
        $this->productHelper()->selectConfigurableAttribute($data['attribute'][0]);
        $this->productHelper()->selectConfigurableAttribute($data['attribute'][1]);
        $this->productHelper()->changeAttributeSet($data['attributeSet']);
        $this->assertTrue($this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue'));
        //Verifying
        $this->assertTrue($this->controlIsVisible('fieldset', 'variations_matrix_grid'));
        $this->productHelper()->verifyConfigurableVariations($data['matrix'], true);
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
            array('general_configurable_attribute_title' => $defaultData['attribute'][0]));
        $fillVariation = $this->loadDataSet('Product', 'product_variation',
            array('variation_qty' => 12, 'variation_weight' => 12));
        $associatedProductData = $this->loadDataSet('Product', 'general_configurable_data',
            array(
                'associated_search_sku' => $fillVariation['variation_sku'],
                'associated_product_attribute_value' => $defaultData['matrix'][1][6]
            )
        );
        $verifySimple = $productData;
        unset($verifySimple['general_configurable_attribute_title']);
        $verifySimple = array_replace($verifySimple,
            array(
                'general_name' => $fillVariation['variation_name'],
                'general_sku' => $fillVariation['variation_sku'],
                'general_weight' => $fillVariation['variation_weight'],
                'inventory_qty' => $fillVariation['variation_qty'],
                'prices_price' => $fillVariation['variation_price'],
                'general_visibility' => 'Not Visible Individually'
            )
        );
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->assignConfigurableVariations(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][6])),
            $fillVariation
        );
        $this->clickButton('save');
        $productData['general_configurable_data'] = $associatedProductData;
        //Verify configurable
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('general_configurable_attribute_title'));
        $this->navigate('manage_products');
        //Verify simple
        $this->assertEquals('Simple Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('product_sku' => $verifySimple['general_sku']));
        $this->productHelper()->verifyProductInfo($verifySimple);
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
            array('general_configurable_attribute_title' => $defaultData['attribute'][0]));
        $fillVariation = $this->loadDataSet('Product', 'product_variation', array('variation_qty' => 12));
        $associatedProductData = $this->loadDataSet('Product', 'general_configurable_data',
            array(
                'associated_search_sku' => $fillVariation['variation_sku'],
                'associated_product_attribute_value' => $defaultData['matrix'][1][6]
            )
        );
        $verifyVirtual = $productData;
        unset($verifyVirtual['general_configurable_attribute_title']);
        $verifyVirtual = array_replace($verifyVirtual,
            array(
                'general_name' => $fillVariation['variation_name'],
                'general_sku' => $fillVariation['variation_sku'],
                'prices_price' => $fillVariation['variation_price'],
                'inventory_qty' => $fillVariation['variation_qty'],
                'general_visibility' => 'Not Visible Individually'
            )
        );
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->assignConfigurableVariations(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][6])),
            $fillVariation
        );
        $this->clickButton('save');
        $productData['general_configurable_data'] = $associatedProductData;
        //Verify configurable
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('general_configurable_attribute_title'));
        //Verify virtual
        $this->navigate('manage_products');
        $this->assertEquals('Virtual Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('product_sku' => $verifyVirtual['general_sku']));
        $this->productHelper()->verifyProductInfo($verifyVirtual);
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
            array('general_configurable_attribute_title' => $defaultData['attribute'][0]));
        $fillInStock = $this->loadDataSet('Product', 'product_variation', array('variation_qty' => 12));
        $fillOutOfStock = $this->loadDataSet('Product', 'product_variation');
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->assignConfigurableVariations(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][6])),
            $fillInStock
        );
        $this->productHelper()->assignConfigurableVariations(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][4][6])),
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
            array('general_configurable_attribute_title' => $defaultData['attribute'][0]));
        $associatedProductData = $this->loadDataSet('Product', 'product_variation', array($emptyField => ''));
        preg_match('/\w+\_(\w+)/', $emptyField, $result);
        $field = $result[1];
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->productHelper()->assignConfigurableVariations(
            array('product_1' => array('associated_product_attribute_value' => $defaultData['matrix'][1][6])),
            $associatedProductData
        );
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
            array('general_configurable_attribute_title' => $defaultData['attribute'][0]));
        $productData['general_weight_and_type_switcher'] = 'no';
        $productData['general_weight'] = 12;
        $verifyData = array(
            'variation_name' => $productData['general_name'] . '-' . $defaultData['matrix'][1][6],
            'variation_price'=> $productData['prices_price'],
            'variation_sku' => $productData['general_sku'] . '-' . $defaultData['matrix'][1][6],
            'variation_weight'=> $productData['general_weight']
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->openTab('general');
        $this->clickButton('generate_product_variations');
        //Verifying
        $this->assertSame($verifyData['variation_name'],
            $this->getControlAttribute('field', 'variation_name', 'value'));
        $this->assertSame($verifyData['variation_price'],
            $this->getControlAttribute('field', 'variation_price', 'value'));
        $this->assertSame($verifyData['variation_sku'],
            $this->getControlAttribute('field', 'variation_sku', 'value'));
        $this->assertSame($verifyData['variation_weight'],
            $this->getControlAttribute('field', 'variation_weight', 'value'));
    }

    /**
     * <p>Unselect configurable attribute while editing configurable product</p>
     *
     * @param array $data
     *
     * @test
     * @depends setConfigurableAttributesToNewSet
     * @TestlinkId TL-MAGE-6517
     */
    public function checkGeneratedMatrixAfterUnselectionAttribute($data)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array(
                'general_configurable_attribute_title' =>  $data['attribute'][0] . ', ' . $data['attribute'][1],
                'product_attribute_set' => $data['attributeSet']
            )
        );
        $attributeUnselected = array($data['matrix'][1][7], $data['matrix'][2][7], $data['matrix'][3][7]);
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->productHelper()->verifyConfigurableVariations($data['matrix'], true);
        $this->addParameter('attributeTitle', $data['attribute'][1]);
        $this->fillCheckbox('general_configurable_attribute_title', 'no');
        $this->clickButton('generate_variations');
        //Verifying
        foreach ($attributeUnselected as $value) {
            $this->addParameter('attributeSearch', "contains(.,'$value')");
            $this->assertFalse($this->controlIsPresent('checkbox', 'associated_product_select'),
                "Matrix contains unselected attribute's data, but should not");
        }
    }

    /**
     * <p>Search non-existed in suggestion list configurable attribute</p>
     *
     * @param string $type
     * @param array $data
     *
     * @test
     * @dataProvider attributeTitleFailDataProvider
     * @depends setConfigurableAttributesToNewSet
     * @testLinkId TL-MAGE-6516
     */
    public function selectNonExistedInListAttribute($type, $data)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array(
                'product_attribute_set' => $data['attributeSet'],
                'general_configurable_attribute_title' => $data['attribute'][0]
            )
        );
        $absentAttribute = ($type == 'selected') ? $data['attribute'][0] : $this->generate('string', 255, ':alnum:');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->productHelper()->fillConfigurableSettings($absentAttribute);
        //Verifying
        $this->assertFalse($this->controlIsVisible('pageelement', 'suggested_attribute_list'));
    }

    public function attributeTitleFailDataProvider()
    {
        return array(
            array('selected'),
            array('non-existed'),
        );
    }

    /**
     * <p>Verify search results (special characters, xss injection)</p>
     *
     * @param string $attributeTitle
     * @param array $data
     *
     * @test
     * @dataProvider attributeTitleSuccessDataProvider
     * @depends createConfigurableAttribute
     * @testLinkId TL-MAGE-6518
     */
    public function selectAttributeWithSpecialData($attributeTitle, $data)
    {
        $this->markTestIncomplete('Skipped due to bugs MAGETWO-5884 and MAGETWO-6028');
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('general_configurable_attribute_title' => $data[$attributeTitle]));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    public function attributeTitleSuccessDataProvider()
    {
        return array(
            array('attribute_xss'),
            array('attribute_spec'),
        );
    }
}
