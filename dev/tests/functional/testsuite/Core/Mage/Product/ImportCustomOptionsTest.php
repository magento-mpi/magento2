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
 * Importing custom option functionality
 */
class Core_Mage_Product_ImportCustomOptionsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System - Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Creating new attribute for configurable products and adding it to default attribute set</p>
     *
     * @test
     *
     * @return array
     */
    public function createAttribute()
    {
        $this->navigate('manage_attributes');
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attrCode = $attrData['attribute_code'];
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrCode));
        $this->productAttributeHelper()->createAttribute($attrData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * Creating two simple products with custom options
     *
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $productDataField = $this->loadDataSet('Product', 'simple_product_visible');
        $productDataField['custom_options_data']['custom_options_field'] =
            $this->loadDataSet('Product', 'custom_options_field');
        $productDataDate = $this->loadDataSet('Product', 'simple_product_visible');
        $productDataDate['custom_options_data']['custom_options_date'] =
            $this->loadDataSet('Product', 'custom_options_date');
        //Steps
        $this->productHelper()->createProduct($productDataField);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($productDataDate);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('simpleField' => array('general_sku'         => $productDataField['general_sku'],
                                            'custom_options_data' => $productDataField['custom_options_data']),
                     'simpleDate'  => array('general_sku'         => $productDataDate['general_sku'],
                                            'custom_options_data' => $productDataDate['custom_options_data']));
    }

    /**
     * <p>Import custom options of all types from different product types</p>
     * <p>Preconditions:</p>
     * <p>1. Product of correspond type with custom options is created.</p>
     *
     * @param string $type
     * @param array $attrData
     *
     * @test
     * @dataProvider productTypesDataProvider
     * @depends createAttribute
     * @TestLinkId TL-MAGE-5878, TL-MAGE-5887, TL-MAGE-5888, TL-MAGE-5889, TL-MAGE-5890, TL-MAGE-5891
     */
    public function withDifferentProductTypes($type, $attrData)
    {
        //Data
        $override = ($type === 'configurable')
            ? array('var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                'general_attribute_1' => $attrData['admin_title'])
            : null;
        $productWithOptions = $this->loadDataSet('Product', $type . '_product_required',
            array('custom_options_data' => $this->loadDataSet('Product', 'custom_options_data')), $override);
        $productData = $this->loadDataSet('Product', $type . '_product_required', null, $override);
        $selectProduct = array('product_sku' => $productWithOptions['general_sku']);
        //Preconditions
        $this->productHelper()->createProduct($productWithOptions, $type);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($productData, $type, false);
        $this->productHelper()->importCustomOptions(array($selectProduct));
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyCustomOptions($productWithOptions['custom_options_data']);
    }

    /**
     * <p>DataProvider with product type list</p>
     *
     * @return array
     */
    public function productTypesDataProvider()
    {
        return array(
            array('simple'),
            array('configurable'),
            array('virtual'),
            array('bundle'),
            array('downloadable')
        );
    }

    /**
     * <p>Import one custom option several times</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product with custom option is created.</p>
     *
     * @param array $simpleProducts
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-5892
     */
    public function oneCustomOptionMultipleTimes($simpleProducts)
    {
        //Data
        $simpleField = $simpleProducts['simpleField'];
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $selectProduct = array('product_sku' => $simpleField['general_sku']);
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->importCustomOptions(array($selectProduct));
        $this->productHelper()->importCustomOptions(array($selectProduct));
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $customOptionsData['custom_options_field_1'] = $simpleField['custom_options_data']['custom_options_field'];
        $customOptionsData['custom_options_field_2'] = $simpleField['custom_options_data']['custom_options_field'];
        $this->productHelper()->verifyCustomOptions($customOptionsData);
    }

    /**
     * <p>Import custom options from several products</p>
     * <p>Preconditions:</p>
     * <p>1. Two simple product with different custom options are created.</p>
     *
     * @param array $simpleProducts
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-5893
     */
    public function importFromSeveralProducts($simpleProducts)
    {
        //Data
        $simpleField = $simpleProducts['simpleField'];
        $simpleDate = $simpleProducts['simpleDate'];
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $selectProductField = array('product_sku' => $simpleField['general_sku']);
        $selectProductDate = array('product_sku' => $simpleDate['general_sku']);
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->importCustomOptions(array($selectProductField, $selectProductDate));
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $customOptionsData['custom_options_field'] = $simpleField['custom_options_data']['custom_options_field'];
        $customOptionsData['custom_options_date'] = $simpleDate['custom_options_data']['custom_options_date'];
        $this->productHelper()->verifyCustomOptions($customOptionsData);
    }

    /**
     * <p>Delete imported custom option</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product with custom options is created.</p>
     *
     * @param array $simpleProducts
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-5894
     */
    public function deleteImportedCustomOption($simpleProducts)
    {
        //Data
        $simpleField = $simpleProducts['simpleField'];
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $selectProduct = array('product_sku' => $simpleField['general_sku']);
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->importCustomOptions(array($selectProduct));
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->deleteAllCustomOptions();
        //Verifying
        $this->assertFalse($this->controlIsVisible('fieldset', 'custom_option_set'),
            'Not all custom options were deleted');
    }

    /**
     * <p>Import custom options to product with custom options</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product with custom options is created.</p>
     *
     * @param array $simpleProducts
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-5895
     */
    public function importToProductWithCustomOptions($simpleProducts)
    {
        //Data
        $simpleField = $simpleProducts['simpleField'];
        $simpleDate = $simpleProducts['simpleDate'];
        $selectProduct = array('product_sku' => $simpleDate['general_sku']);
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $simpleField['general_sku']));
        $this->productHelper()->importCustomOptions(array($selectProduct));
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $simpleField['general_sku']));
        $customOptionsData['custom_options_field'] = $simpleField['custom_options_data']['custom_options_field'];
        $customOptionsData['custom_options_date'] = $simpleDate['custom_options_data']['custom_options_date'];
        $this->productHelper()->verifyCustomOptions($customOptionsData);
    }
}