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
     * <p> Create Configurable attributes with special values: xss injection ans special characters</p>
     *
     * @return array
     * @test
     */
    public function createConfigurableAttribute()
    {
        $xssAttribute = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options',
            array('attribute_code' => 'xss_%randomize%',
                'admin_title' => 'XSS',
                'option_1' => array('admin_option_name' => "<script>alert('xss option');</script>")));
        $specialCharacters = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options',
            array('attribute_code' => 'special_characters_%randomize%',
                'admin_title' => str_replace(array(',', '"', "'"), '?', $this->generate('string', 30, ':punct:'))));
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($xssAttribute);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($specialCharacters);
        $this->assertMessagePresent('success', 'success_saved_attribute');

        return array(
            'attribute_xss' => $xssAttribute['admin_title'],
            'attribute_spec' => $specialCharacters['admin_title'],
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
        $productData = $this->loadDataSet('Product', 'configurable_product_visible');
        $productData['configurable_attribute_title'] = $defaultData['attribute1'] . ', ' . $defaultData['attribute2'];
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->productHelper()->fillProductInfo($productData);
        $this->openTab('general');
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')));
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'));
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
        $this->productHelper()->selectConfigurableAttribute($data['attribute1']);
        $this->productHelper()->selectConfigurableAttribute($data['attribute2']);
        $this->productHelper()->changeAttributeSet($data['attributeSet']);
        $this->openTab('general');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')));
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'));
        $this->clickButton('generate_variations');
        //Verifying
        $this->productHelper()->checkGeneratedMatrix($data['matrix']);
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
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'configurable_attribute_title' =>  $data['attribute1'] . ', ' . $data['attribute2'],
            'product_attribute_set' => $data['attributeSet']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->productHelper()->checkGeneratedMatrix($data['matrix']);
        $this->addParameter('attributeTitle', $data['attribute2']);
        $this->fillCheckbox('configurable_attribute_title', 'no');
        $this->clickButton('generate_variations');
        $attributeUnselected = array($data['matrix']['1']['2'], $data['matrix']['2']['2'], $data['matrix']['3']['2']);
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
     * @param $type
     * @param $data
     *
     * @dataProvider attributeTitleFailDataProvider
     * @depends setConfigurableAttributesToNewSet
     *
     * @testLinkId TL-MAGE-6516
     * @test
     */
    public function selectNonExistedInListAttribute($type, $data)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('product_attribute_set' => $data['attributeSet'],
                'configurable_attribute_title' => $data['attribute1']));
        $absentAttributeTitle = array('selected' => $data['attribute1'],
            'non-existed' => $this->generate('string', 255, ':alnum:'));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $configurable['general_sku']));
        $this->fillField('attribute_selector', $absentAttributeTitle[$type]);
        $this->typeKeys($this->_getControlXpath('field', 'attribute_selector'), "\b");
        $this->waitForElementVisible($this->_getControlXpath('field', 'attribute_selector')
            . "[contains(@class, 'ui-autocomplete-loading')]");
        $this->waitForElementVisible($this->_getControlXpath('field', 'attribute_selector')
            . "[not(contains(@class, 'ui-autocomplete-loading'))]");
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
     * @param $attributeTitle
     * @param $data
     *
     * @dataProvider attributeTitleSuccessDataProvider
     * @depends createConfigurableAttribute
     *
     * @test
     * @testLinkId TL-MAGE-6518
     */
    public function selectAttributeSuccessfullySpecialData($attributeTitle, $data)
    {
        $this->markTestIncomplete('Skipped due to bugs MAGETWO-5884 and MAGETWO-6028');
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('configurable_attribute_title' => $data[$attributeTitle]));
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
