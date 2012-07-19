<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_Product
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#
/**
 * Importing custom option functionality
 */
class Community2_Mage_Product_ImportCustomOptionsTest extends Mage_Selenium_TestCase
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
        $associatedAttributes =
            $this->loadDataSet('AttributeSet', 'associated_attributes', array('General' => $attrCode));
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
     *  <p>1. Product of correspond type with custom options is created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go to Catalog - Manage Products.</p>
     *  <p>3. Click the "Add Product" button.</p>
     *  <p>4. Select "Default" attribute set and correspond product type.</p>
     *  <p>5. Click the "Continue" button.</p>
     *  <p>6. Fill in all required fields.</p>
     *  <p>7. Open "Custom Options" tab.</p>
     *  <p>8. Click the "Import Options" button.</p>
     *  <p>9. Select previously created product.</p>
     *  <p>10. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom options from selected product were fully copied and added to creating product.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     *  <p>Steps:</p>
     *  <p>11. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved.</p>
     *  <p>2. System displays message "The product has been saved."</p>
     *  <p>3. Imported custom options were saved.</p>
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
        if ($type != 'configurable'){
            $productDataCustomOption = $this->loadDataSet('Product', $type . '_product_required');
            $productData = $this->loadDataSet('Product', $type . '_product_required');
        }else{
            $productDataCustomOption = $this->loadDataSet('Product', $type . '_product_required',
                array('configurable_attribute_title' => $attrData['admin_title']));
            $productData = $this->loadDataSet('Product', $type . '_product_required',
                array('configurable_attribute_title' => $attrData['admin_title']));
        }
        $productDataCustomOption['custom_options_data'] = $this->loadDataSet('Product', 'custom_options_data');
        //Preconditions
        $this->productHelper()->createProduct($productDataCustomOption, $type);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProductWithoutSave($productData, $type);
        $this->productHelper()->importCustomOptions($productDataCustomOption['general_sku']);
        $this->saveForm('save');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        //Verifying
        $this->productHelper()->verifyCustomOption($productDataCustomOption['custom_options_data']);
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
     *  <p>1. Simple product with custom option is created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go to Catalog - Manage Products.</p>
     *  <p>3. Click the "Add Product" button.</p>
     *  <p>4. Select "Default" attribute set and simple product type.</p>
     *  <p>5. Click the "Continue" button.</p>
     *  <p>6. Fill in all required fields.</p>
     *  <p>7. Open "Custom Options" tab.</p>
     *  <p>8. Click the "Import Options" button.</p>
     *  <p>9. Select previously created simple product.</p>
     *  <p>10. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom option from selected product was fully copied and added to creating product.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>11. Click the "Import Options" button.</p>
     *  <p>12. Select previously created simple product.</p>
     *  <p>13. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom option from selected product was fully copied and added to existent custom option.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>14. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved.</p>
     *  <p>2. System displays message "The product has been saved."</p>
     *  <p>3. Imported custom options were saved.</p>
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
        //Steps
        $this->productHelper()->createProductWithoutSave($productData);
        $this->productHelper()->importCustomOptions($simpleField['general_sku']);
        $this->productHelper()->importCustomOptions($simpleField['general_sku']);
        $this->saveForm('save');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        //Verifying
        $custom_options_data ['custom_options_field_1'] = $simpleField['custom_options_data']['custom_options_field'];
        $custom_options_data ['custom_options_field_2'] = $simpleField['custom_options_data']['custom_options_field'];
        $this->productHelper()->verifyCustomOption($custom_options_data);
    }

    /**
     * <p>Import custom options from several products</p>
     * <p>Preconditions:</p>
     *  <p>1. Two simple product with different custom options are created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go to Catalog - Manage Products.</p>
     *  <p>3. Click the "Add Product" button.</p>
     *  <p>4. Select "Default" attribute set and simple product type.</p>
     *  <p>5. Click the "Continue" button.</p>
     *  <p>6. Fill in all required fields.</p>
     *  <p>7. Open "Custom Options" tab.</p>
     *  <p>8. Click the "Import Options" button.</p>
     *  <p>9. Select previously created first simple product.</p>
     *  <p>10. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom option from selected product was fully copied and added to creating product.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>11. Click the "Import Options" button.</p>
     *  <p>12. Select previously created second simple product.</p>
     *  <p>13. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom option from selected product was fully copied and added to existent custom option.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>14. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved.</p>
     *  <p>2. System displays message "The product has been saved."</p>
     *  <p>3. Imported custom options were saved.</p>
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
        //Steps
        $this->productHelper()->createProductWithoutSave($productData);
        $this->productHelper()->importCustomOptions(array($simpleField['general_sku'], $simpleDate['general_sku']));
        $this->saveForm('save');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        //Verifying
        $custom_options_data ['custom_options_field'] = $simpleField['custom_options_data']['custom_options_field'];
        $custom_options_data ['custom_options_date'] = $simpleDate['custom_options_data']['custom_options_date'];
        $this->productHelper()->verifyCustomOption($custom_options_data);
    }

    /**
     * <p>Delete imported custom option</p>
     * <p>Preconditions:</p>
     *  <p>1. Simple product with custom options is created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go to Catalog - Manage Products.</p>
     *  <p>3. Click the "Add Product" button.</p>
     *  <p>4. Select "Default" attribute set and simple product type.</p>
     *  <p>5. Click the "Continue" button.</p>
     *  <p>6. Fill in all required fields.</p>
     *  <p>7. Open "Custom Options" tab.</p>
     *  <p>8. Click the "Import Options" button.</p>
     *  <p>9. Select previously created simple product.</p>
     *  <p>10. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom options from selected product were fully copied and added to creating product.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>11. Delete all custom options.</p>
     *  <p>12. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved.</p>
     *  <p>2. System displays message "The product has been saved."</p>
     *  <p>3. Product saved without custom options.</p>
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
        //Steps
        $this->productHelper()->createProductWithoutSave($productData);
        $this->productHelper()->importCustomOptions(array('sku' => $simpleField['general_sku']));
        $this->saveForm('save');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->deleteAllCustomOptions($simpleField['custom_options_data']);
        //Verifying
        $this->assertEquals(0, $this->getXpathCount($this->_getControlXpath('fieldset', 'custom_option_set')),
            'Not all custom options were deleted');
    }

    /**
     * <p>Import custom options to product with custom options</p>
     * <p>Preconditions:</p>
     *  <p>1. Simple product with custom options is created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go to Catalog - Manage Products.</p>
     *  <p>3. Click the "Add Product" button.</p>
     *  <p>4. Select "Default" attribute set and simple product type.</p>
     *  <p>5. Click the "Continue" button.</p>
     *  <p>6. Fill in all required fields.</p>
     *  <p>7. Open "Custom Options" tab.</p>
     *  <p>8. Add custom options.</p>
     *  <p>9. Save product.</p>
     *  <p>10. Open saved product.</p>
     *  <p>11. Open "Custom Options" tab.</p>
     *  <p>12. Click the "Import Options" button.</p>
     *  <p>13. Select previously created simple product.</p>
     *  <p>14. Click the "Import" button on the grid.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Custom options from selected product were fully copied and added to existent custom options.</p>
     *  <p>2. Grid with products was closed.</p>
     *
     * <p>Steps:</p>
     *  <p>15. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product successfully saved.</p>
     *  <p>2. System displays message "The product has been saved."</p>
     *  <p>3. Imported custom options were saved.</p>
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
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $simpleField['general_sku']));
        $this->productHelper()->importCustomOptions($simpleDate['general_sku']);
        $this->saveForm('save');
        $this->productHelper()->openProduct(array('product_sku' => $simpleField['general_sku']));
        //Verifying
        $custom_options_data ['custom_options_field'] = $simpleField['custom_options_data']['custom_options_field'];
        $custom_options_data ['custom_options_date'] = $simpleDate['custom_options_data']['custom_options_date'];
        $this->productHelper()->verifyCustomOption($custom_options_data);
    }
}
