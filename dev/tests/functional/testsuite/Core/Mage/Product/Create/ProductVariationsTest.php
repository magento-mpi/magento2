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
class Core_Mage_Product_Create_ProductVariationsTest extends Mage_Selenium_TestCase
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
        for ($i = 1; $i <= $optionNumber1; $i++) {
            for ($j = 1; $j <= $optionNumber2; $j++) {
                $variations['configurable_' . $variation] = array('associated_attributes' => array(
                    'attribute_1' => array('associated_attribute_name' => $attribute1['admin_title'],
                        'associated_attribute_value' => $attribute1['option_' . $i]['admin_option_name']),
                    'attribute_2' => array('associated_attribute_name' => $attribute2['admin_title'],
                        'associated_attribute_value' => $attribute2['option_' . $j]['admin_option_name'])));
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
            'attribute1' => $attributeThird,
            'attribute2' => $attributeForth,
            'matrix' => $this->_getVariationsForTwoAttributes($attributeThird, $attributeForth)
        );
    }

    /**
     * <p>Create Configurable attributes with special values: xss injection ans special characters</p>
     *
     * @return array
     *
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $xssAttribute = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options',
            array(
                'attribute_code' => 'xss_%randomize%',
                'admin_title' => 'xss_%randomize%',
                'option_1' => array('admin_option_name' => '<script>alert("xss option");</script>')
            )
        );
                $specialCharacters = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options',
                    array(
                        'attribute_code' => 'special_characters_%randomize%',
                        'admin_title' => str_replace(array(
                            ',', '"', "'", '<'), '?', $this->generate('string', 30, ':punct:'
                        ))
                    )
                );
        $associatedAttribute = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => array($xssAttribute['attribute_code'], $specialCharacters['attribute_code'])));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($xssAttribute);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($specialCharacters);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'attribute_xss' => $xssAttribute,
            'attribute_spec' => $specialCharacters,
        );
    }

    /**
     * <p>Configurable Product with Product Variations</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6476
     */
    public function checkGeneratedMatrix($attributeData)
    {
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'));
        $this->assertTrue($this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue'));
        $this->productHelper()->selectConfigurableAttribute($attributeData['attribute1']['admin_title']);
        $this->productHelper()->selectConfigurableAttribute($attributeData['attribute2']['admin_title']);
        $this->clickButton('generate_product_variations');
        $this->waitForControlVisible('pageelement', 'variations_matrix_header');
        //Verifying
        $this->productHelper()->verifyConfigurableVariations($attributeData['matrix'], true);
    }

    /**
     * <p>Create simple product via product variation grid in configurable product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6519
     */
    public function createSimpleViaVariationGrid($attributeData)
    {
        //Data for creation
        $associated = $this->loadDataSet('Product', 'generate_simple_associated', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_1' => $associated),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        //Data for verification
        $verifySimple = $configurable;
        unset($verifySimple['general_configurable_attributes']);
        unset($verifySimple['general_configurable_variations']);
        $verifySimple = array_replace($verifySimple,
            array(
                'general_name'       => $associated['associated_product_name'],
                'general_sku'        => $associated['associated_sku'],
                'general_weight'     => $associated['associated_weight'],
                'inventory_quantity' => $associated['associated_quantity'],
                'general_visibility' => 'Not Visible Individually',
                'general_status'     => 'Enabled'
            )
        );
        $searchConfigurable = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $configurable['general_sku']));
        $searchSimple = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $verifySimple['general_sku']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verify configurable
        $this->productHelper()->openProduct($searchConfigurable);
        $this->productHelper()->verifyProductInfo($configurable,
            array('product_attribute_set', 'general_configurable_attribute_title'));
        $this->navigate('manage_products');
        //Verify simple
        $this->assertEquals('Simple Product', $this->productHelper()->getProductDataFromGrid($searchSimple, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($searchSimple);
        $this->productHelper()->verifyProductInfo($verifySimple);
    }

    /**
     * <p>Create virtual product via product variation grid in configurable product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6521
     */
    public function createVirtualViaVariationGrid($attributeData)
    {
        //Data for creation
        $associated = $this->loadDataSet('Product', 'generate_virtual_associated', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_1' => $associated),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        //Data for verification
        $verifyVirtual = $configurable;
        unset($verifyVirtual['general_configurable_attributes']);
        unset($verifyVirtual['general_configurable_variations']);
        $verifyVirtual = array_replace($verifyVirtual,
            array(
                'general_name'       => $associated['associated_product_name'],
                'general_sku'        => $associated['associated_sku'],
                'inventory_quantity' => $associated['associated_quantity'],
                'general_visibility' => 'Not Visible Individually',
                'general_status'     => 'Enabled'
            )
        );
        $searchConfigurable = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $configurable['general_sku']));
        $searchVirtual = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $verifyVirtual['general_name']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verify configurable
        $this->productHelper()->openProduct($searchConfigurable);
        $this->productHelper()->verifyProductInfo($configurable,
            array('product_attribute_set', 'general_configurable_attribute_title'));
        $this->navigate('manage_products');
        //Verify virtual
        $this->assertEquals('Virtual Product', $this->productHelper()->getProductDataFromGrid($searchVirtual, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($searchVirtual);
        $this->productHelper()->verifyProductInfo($verifyVirtual);
    }

    /**
     * <p>Verify Manage Stock option for created product via product variation in configurable product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6525
     */
    public function verifyManageStock($attributeData)
    {
        //Data
        $fillInStock = $this->loadDataSet('Product', 'product_variation', array('associated_quantity' => 12), array(
            'general_attribute_1' => $attributeData['attribute1']['admin_title'],
            'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
        ));
        $fillOutOfStock = $this->loadDataSet('Product', 'product_variation', array(), array(
            'general_attribute_1' => $attributeData['attribute1']['admin_title'],
            'var1_attr_value1' => $attributeData['attribute1']['option_2']['admin_option_name']
        ));
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array(
                'configurable_1' => $fillInStock,
                'configurable_2' => $fillOutOfStock
            ),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'var1_attr_value2' => $attributeData['attribute1']['option_2']['admin_option_name'],
                'var1_attr_include2' => 'Yes'
            )
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verify virtual with Manage Stock - Yes
        $this->productHelper()->openProduct(array('product_sku' => $fillInStock['associated_sku']));
        $this->productHelper()->verifyProductInfo(array(
            'inventory_qty' => $fillInStock['associated_quantity'],
            'inventory_manage_stock' => 'Yes'
        ));
        //Verify virtual with Manage Stock - No
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_sku' => $fillOutOfStock['associated_sku']));
        $this->productHelper()->verifyProductInfo(array('inventory_manage_stock' => 'No'));
    }

    /**
     * <p>Verify required fields validation in variation matrix</p>
     *
     * @param string $emptyField
     * @param array $attributeData
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6523
     */
    public function withRequiredFieldsEmpty($emptyField, $attributeData)
    {
        //Data
        $associated = $this->loadDataSet('Product', 'product_variation', array($emptyField => ''),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        $productData = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_1'  => $associated),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        preg_match('/\w+\_(\w+)/', $emptyField, $result);
        $field = $result[1];
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->openTab('general');
        $this->addParameter('field', $field);
        $this->saveForm('save', false);
        //Verifying
        $this->assertMessagePresent('validation', 'required_field');
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('associated_product_name'),
            array('associated_sku')
        );
    }

    /**
     * <p>Verification variation fields in configurable product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6522
     */
    public function verificationVariationFields($attributeData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        unset($productData['general_configurable_variations']);
        $productData['general_weight_and_type_switcher'] = 'No';
        $productData['general_weight'] = '12';
        $option = $attributeData['attribute1']['option_1']['admin_option_name'];
        $verifyData = array(
            'associated_product_name' => $productData['general_name'] . '-' . $option,
            'associated_sku' => $productData['general_sku'] . '-' . $option,
            'associated_weight' => $productData['general_weight']
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->openTab('general');
        //Verifying
        $this->addParameter('attributeSearch', "contains(.,$option)");
        $this->assertSame($verifyData['associated_product_name'],
            $this->getControlAttribute('field', 'associated_product_name', 'value'));
        $this->assertSame($verifyData['associated_sku'],
            $this->getControlAttribute('field', 'associated_sku', 'value'));
        $this->assertSame($verifyData['associated_weight'],
            $this->getControlAttribute('field', 'associated_weight', 'value'));
    }

    /**
     * <p>Unselect all values of configurable attribute</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6517
     */
    public function verificationAfterUnselectionAllAttributeValues($attributeData)
    {
        $this->markTestIncomplete('Skipped due to bug MAGETWO-6600, MAGETWO-6640');
        //Data
        $attribute1 = array('general_configurable_attribute_title' => $attributeData['attribute1']['admin_title']);
        $attribute2 = array('general_configurable_attribute_title' => $attributeData['attribute2']['admin_title']);
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->openTab('prices');
        $this->openTab('general');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'));
        $this->assertTrue($this->getControlAttribute('checkbox', 'is_configurable', 'selectedValue'));
        $this->productHelper()->selectConfigurableAttribute($attribute1);
        $this->productHelper()->selectConfigurableAttribute($attribute2);
        $this->productHelper()->unselectConfigurableAttributeOptions(array(),
            $attribute1['general_configurable_attribute_title']);
        $this->clickButton('generate_product_variations');
        $this->waitForControlVisible('pageelement', 'variations_matrix_header');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'));
        //        $this->assertMessagePresent('error')
    }

    /**
     * <p>Search non-existed in suggestion list configurable attribute</p>
     *
     * @param string $type
     * @param array $attributeData
     *
     * @test
     * @dataProvider attributeTitleFailDataProvider
     * @depends setConfigurableAttributesToDefault
     * @testLinkId TL-MAGE-6516
     */
    public function selectNonExistedInListAttribute($type, $attributeData)
    {
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1'    => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        //Data
        $absentAttribute = ($type == 'selected') ? $attributeData['attribute1']['admin_title']
            : $this->generate('string', 255, ':alnum:');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->addParameter('attributeName', $absentAttribute);
        $element = $this->waitForControlEditable('field', 'general_configurable_attribute_title', 10);
        $this->focusOnElement($element);
        $element->value($absentAttribute);
        //Verifying
        $this->assertFalse($this->controlIsVisible('link', 'configurable_attribute_select'));
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
     * @param array $attributeData
     *
     * @test
     * @dataProvider attributeTitleSuccessDataProvider
     * @depends createConfigurableAttribute
     * @testLinkId TL-MAGE-6518
     */
    public function selectAttributeWithSpecialData($attributeTitle, $attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'general_attribute_1' => $attributeData[$attributeTitle]['admin_title'],
                'var1_attr_value1'    => $attributeData[$attributeTitle]['option_1']['admin_option_name']
            )
        );
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    public function attributeTitleSuccessDataProvider()
    {
        return array(
            array('attribute_xss'),
//            array('attribute_spec'), due to bug MAGETWO-6028
        );
    }

    /**
     * <p>Exclude existed configurable attribute’s option from variation matrix</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @testLinkId TL-MAGE-6531
     */
    public function excludeOptionFromMatrix($attributeData)
    {
        //Data for product creation
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'var1_attr_value2' => $attributeData['attribute1']['option_2']['admin_option_name'],
                'var1_attr_include2' => 'Yes',
                'var1_attr_value3' => $attributeData['attribute1']['option_3']['admin_option_name'],
                'var1_attr_include3' => 'No'
            )
        );
        unset($configurable['general_configurable_variations']);
        //Data for verification
        $excludedOption = $attributeData['attribute1']['option_3']['admin_option_name'];
        //Preconditions. Create product
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verifying
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->addParameter('attributeSearch', "contains(.,'$excludedOption')");
        $this->assertFalse($this->controlIsVisible('checkbox', 'include_variation'),
            "Matrix contains unselected attribute value's data, but should not");
    }

    /**
     * <p>Include new value of existed configurable attribute’s while editing created configurable product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @testLinkId TL-MAGE-6534
     */
    public function includeNewOption($attributeData)
    {
        //Data for creation
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'var1_attr_include2' => 'Yes',
                'var1_attr_value2' => $attributeData['attribute1']['option_2']['admin_option_name'],
                'var1_attr_include3' => 'Yes',
                'var1_attr_value3' => $attributeData['attribute1']['option_3']['admin_option_name']
            )
        );
        unset($configurable['general_configurable_variations']);
        //Data for verification
        $newOption = array('option_4' => array('admin_option_name' => 'Option_Admin_'
            . $this->generate('string', 5, ':alnum:')));
        $newOptionTitle = $newOption['option_4']['admin_option_name'];
        //Preconditions. Create product
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps. Add new option to configurable attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute($attributeData['attribute1']['attribute_code'], $newOption);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps.
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->productHelper()->changeAttributeValueSelection($attributeData['attribute1']['admin_title'],
            $newOption['option_4']['admin_option_name']);
        $this->clickButton('generate_product_variations');
        $this->waitForControlVisible('pageelement', 'variations_matrix_header');
        //Verifying
        $this->addParameter('attributeSearch', "contains(.,'$newOptionTitle')");
        $this->fillCheckbox('include_variation', 'Yes');
        $this->assertContains($newOptionTitle, $this->getControlAttribute('pageelement', 'variation_line', 'text'));
    }

    /**
     * <p>Set price rule for value of configurable attribute while create new product</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @testLinkId TL-MAGE-6535, TL-MAGE-6536
     */
    public function setPriceRuleForVariationWhileCreateVariations($attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('general_name' => 'product_%randomize%', 'general_sku' => 'sku_%randomize%'),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name']
            )
        );
        $configurable['general_configurable_attributes']['general_attribute_2'] =
            $this->loadDataSet('Product', 'general_configurable_attribute_with_price', null,
                array(
                    'general_attribute_2' => $attributeData['attribute2']['admin_title'],
                    'var2_attr_value1' => $attributeData['attribute2']['option_1']['admin_option_name'],
                    'var2_attr_value2' => $attributeData['attribute2']['option_2']['admin_option_name'],
                    'var2_attr_value3' => $attributeData['attribute2']['option_3']['admin_option_name']
                )
            );
        unset($configurable['general_configurable_variations']);
        //Data for verification
        $ruleOptionFixed = $attributeData['attribute2']['option_1']['admin_option_name'];
        $endPriceFixed = '20.94';
        $ruleOptionPercentage = $attributeData['attribute2']['option_2']['admin_option_name'];
        $endPricePercentage = '21.7925';
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verification
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->addParameter('attributeSearch', "contains(.,'$ruleOptionFixed')");
        $this->assertEquals($endPriceFixed, $this->getControlAttribute('pageelement', 'variation_price', 'text'));
        $this->addParameter('attributeSearch', "contains(.,'$ruleOptionPercentage')");
        $this->assertEquals($endPricePercentage, $this->getControlAttribute('pageelement', 'variation_price', 'text'));
    }

    /**
     * <p>Set price rule for value of configurable attribute while assign existed product</p>
     *
     * @param string $ruleType
     * @param string $endPrice
     * @param array $attributeData
     *
     * @test
     * @dataProvider priceRuleTypeDataProvider
     * @depends setConfigurableAttributesToDefault
     * @testLinkId TL-MAGE-6537, TL-MAGE-6538
     */
    public function setPriceRuleForVariationWhileEditProduct($ruleType, $endPrice, $attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('general_name' => 'product_%randomize%', 'general_sku' => 'sku_%randomize%'),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'var1_attr_value2' => $attributeData['attribute1']['option_2']['admin_option_name'],
                'var1_attr_include2' => 'Yes',
                'var1_attr_value3' => $attributeData['attribute1']['option_3']['admin_option_name'],
                'var1_attr_include3' => 'Yes',
            )
        );
        $ruleOption = $attributeData['attribute1']['option_1']['admin_option_name'];
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->setVariationPriceRule($attributeData['attribute1']['admin_title'], $ruleOption, $ruleType, '50');
        $this->clickButton('generate_product_variations');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        //Verification. Backend
        $this->addParameter('attributeSearch', "contains(.,'$ruleOption')");
        $this->assertEquals($endPrice, $this->getControlAttribute('pageelement', 'variation_price', 'text'));
    }

    /**
     * DataProvider for price rule types and appropriate variation price.
     * Price of configurable product in dataSet = 18.95 and $endPrice depends on it
     *
     * @return array
     */
    public function priceRuleTypeDataProvider()
    {
        return array(
            array('Fixed', '68.95'),
            array('Percentage', '28.425'),
        );
    }

    /**
     * Set price rule for attribute option
     *
     * @param string $attributeTitle
     * @param string $option
     * @param string $ruleType
     * @param string $price
     */
    protected function setVariationPriceRule($attributeTitle, $option, $ruleType, $price)
    {
        $this->addParameter('attributeTitle', $attributeTitle);
        $this->fillCheckbox('have_price_variation', 'Yes');
        $this->waitForControlVisible('field', 'variation_attribute_price');
        $this->addParameter('attributeOption', $option);
        $this->fillField('variation_attribute_price', $price);
        $this->fillDropdown('variation_attribute_price_type', $ruleType);
    }

    /**
     * <p>Move attribute block with drag and drop</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6540
     */
    public function moveAttributeBlock($attributeData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'general_attribute_1' => $attributeData['attribute1']['admin_title']
            )
        );
        $productData['general_configurable_attributes']['attribute_2'] =
            $this->loadDataSet('Product', 'general_configurable_attribute_without_price', null,
                array(
                    'general_attribute_1' => $attributeData['attribute2']['admin_title'],
                    'var1_attr_value1' => $attributeData['attribute2']['option_1']['admin_option_name']
                )
            );
        $verifyData = array($attributeData['attribute2']['admin_title'], $attributeData['attribute1']['admin_title']);
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        //Swapping attribute blocks
        $this->addParameter('attributeTitle', $attributeData['attribute1']['admin_title']);
        $attributeBlock1 = $this->getControlElement('link', 'move_product_variation_attribute');
        $this->addParameter('attributeTitle', $attributeData['attribute2']['admin_title']);
        $attributeBlock2 = $this->getControlElement('link', 'move_product_variation_attribute');
        $this->moveto($attributeBlock2);
        $this->buttondown();
        $this->moveto($attributeBlock1);
        $this->buttonup();
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend();
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        foreach ($verifyData as $key => $value) {
            $this->addParameter('rowNumber', $key + 1);
            $this->addParameter('title', $value);
            $this->assertTrue($this->controlIsVisible('pageelement', 'product_custom_option_head_order'));
        }
    }

    /**
     * <p>Remove attribute block</p>
     *
     * @param $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6541
     */
    public function removeAttributeBlock($attributeData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'general_attribute_1' => $attributeData['attribute1']['admin_title']
            )
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->addParameter('attributeTitle', $attributeData['attribute1']['admin_title']);
        $this->clickControl('link', 'delete_product_variation_attribute');
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        $this->addParameter('title', $attributeData['attribute1']['admin_title']);
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_custom_option_head'));
    }

        /**
         * <p>Verify editable input for attribute title in attribute block<p>
         *
         * @param string $value
         * @param array $attributeData
         *
         * @test
         * @dataProvider withFilledAttributeLabelDataProvider
         * @depends setConfigurableAttributesToDefault
         * @TestlinkId TL-MAGE-6542, TL-MAGE-6544
         */
        public function withFilledAttributeLabelField($value, $attributeData)
        {
            //Data
            $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
                array(
                    'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                    'general_attribute_1' => $attributeData['attribute1']['admin_title']
                )
            );
            //Steps
            $this->productHelper()->createProduct($productData, 'configurable');
            $this->assertMessagePresent('success', 'success_saved_product');
            $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
            $this->addParameter('attributeTitle', $attributeData['attribute1']['admin_title']);
            $this->fillField('frontend_label', $value);
            $this->saveForm('save');
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_product');
            $this->productHelper()->frontOpenProduct($productData['general_name']);
            $this->addParameter('title', $value);
            $this->assertTrue($this->controlIsVisible('fieldset', 'product_custom_option_head'));
        }

        public function withFilledAttributeLabelDataProvider()
        {
            return array(
                array($this->generate('string', 255, ':alnum:')),
                array('<img src=example.com?nonexistent.jpg onerror=alert("xss")>'),
                array(str_replace(array(',', '"', "'", '<'), '?', $this->generate('string', 255, ':punct:')))
            );
        }

    /**
     * <p>Verify editable input for attribute title in attribute block (empty and Use default checkbox)</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6543
     */
    public function withEmptyAttributeLabelField($attributeData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array(
                'var1_attr_value1' => $attributeData['attribute1']['option_1']['admin_option_name'],
                'general_attribute_1' => $attributeData['attribute1']['admin_title']
            )
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->addParameter('attributeTitle', $attributeData['attribute1']['admin_title']);
        $this->fillField('frontend_label', '');
        $this->clickButton('save', false);
        $this->assertMessagePresent('validation', 'required_attribute_label');
        $this->fillCheckbox('use_default_label', 'Yes');
        $this->saveForm('save');
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->frontOpenProduct($productData['general_name']);
        $this->addParameter('title', $attributeData['attribute1']['store_view_titles']['Default Store View']);
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_custom_option_head'));
    }

    /**
     * <p>Verify assignment of product after second time generation matrix grid</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends setConfigurableAttributesToDefault
     * @TestlinkId TL-MAGE-6569
     */
    public function verifyAssignmentAfterDoubleGeneration($attributeData)
    {
        //Data for creation
        $assignOptionTitle = $attributeData['attribute1']['option_1']['admin_option_name'];
        $newOptionTitle = $attributeData['attribute1']['option_2']['admin_option_name'];
        $associated = $this->loadDataSet('Product', 'simple_product_visible');
        $associated['general_user_attr']['dropdown'][$attributeData['attribute1']['attribute_code']] =
            $attributeData['attribute1']['option_1']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array(
                'associated_product_name' => $associated['general_name'],
                'associated_sku'          => $associated['general_sku']
            ),
            array(
                'general_attribute_1' => $attributeData['attribute1']['admin_title'],
                'var1_attr_value1' => $assignOptionTitle,
            )
        );
        //Preconditions. Create simple product
        $this->productHelper()->createProduct($associated);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->openTab('general');
        $this->productHelper()->changeAttributeValueSelection($attributeData['attribute1']['admin_title'],
            $newOptionTitle);
        $this->clickButton('generate_product_variations');
        $this->addParameter('attributeSearch', "contains(.,'$newOptionTitle')");
        $this->waitForControlVisible('pageelement', 'variation_line');
        $this->assertContains($associated['general_sku'],
            $this->getControlAttribute('pageelement', 'variation_line', 'text'));
        $this->addParameter('attributeSearch', "contains(.,'$assignOptionTitle')");
        $this->addParameter('productSku', $associated['general_sku']);
        $this->assertEquals(true, $this->getControlAttribute('checkbox', 'assigned_product', 'selectedValue'));
    }
}
