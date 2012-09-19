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
 * Products creation tests based on Minimal Attribute Set
 */
class Community2_Mage_Product_CreateOnMinimalAttributeSetTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Test Realizing precondition for creating configurable product.
     *
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attrData['attribute_code']));
        $productData = $this->loadDataSet('Product', 'simple_product_minimal');
        $productData['general_user_attr_dropdown'] = $attrData['option_1']['admin_option_name'];
        //Steps (attribute)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Minimal');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Steps for creating simple product for configurable
        $this->navigate('manage_products');
        $this->addParameter('attributeCodeDropdown', $attrData['attribute_code']);
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');


        return array('simple_sku' => $productData['general_sku'], 'attribute' => $attrData['admin_title']);
    }

    // @codingStandardsIgnoreStart
    /**
     * <p>Create products based on minimal attribute set</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Choose "Attribute Set" - "Minimal";</p>
     * <p>3. Choose "Product Type" - "Simple Product with custom options", "Downloadable Product", "Configurable Product", "Bundle (Fixed) Product", "Bundle (Dynamic) Product", "Grouped Product";</p>
     * <p>4. Click "Continue" button;</p>
     * <p>5. On "General" tab: Fill Fields: Name, Description, Short Description, SKU, Price, Status, Visibility, Tax Class, Weight (depends on product type);</p>
     * <p>6. On "Website" tab: select created website.;</p>
     * <p>7. On "Categories" tab: select created category;</p>
     * <p>8. Click "Save" button.;</p>
     *
     * <p>Expected result:</p>
     * <p> Product is created. System displays successful message "The product has been saved.";</p>
     *
     * @param string $productType
     * @param array $testData
     *
     * @test
     * @dataProvider productTypesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5700, TL-MAGE-5701, TL-MAGE-5702, TL-MAGE-5703, TL-MAGE-5706, TL-MAGE-5707
     */
    // @codingStandardsIgnoreEnd
    public function createAllProducts($productType, $testData)
    {
        //Data
        switch ($productType) {
            case 'simple_custom':
                $productType = 'simple';
                $productData = $this->loadDataSet('Product', $productType . '_product_minimal',
                    array('custom_options_field' => $this->loadDataSet('Product', 'custom_options_field')));
                break;
            case 'downloadable':
                $productData = $this->loadDataSet('Product', $productType . '_product_minimal',
                    array('downloadable_link_1' => $this->loadDataSet('Product', 'downloadable_links')));
                break;
            case 'configurable':
                $productData = $this->loadDataSet('Product', $productType . '_product_minimal',
                    array('configurable_attribute_title' => $testData['attribute'],
                          'associated_search_sku'        => $testData['simple_sku']));
                break;
            case 'fixed_bundle':
            case 'dynamic_bundle':
                $productData = $this->loadDataSet('Product', $productType . '_minimal',
                    array('item_1' => $this->loadDataSet('Product', 'bundle_item_2')));
                $productData['bundle_items_data']['item_1']['add_product_1']['bundle_items_search_sku'] =
                    $testData['simple_sku'];
                $productType = 'bundle';
                break;
            case 'grouped':
                $productData = $this->loadDataSet('Product', $productType . '_product_minimal', null,
                    array('associated_1' => $testData['simple_sku']));
                break;
            default:
                $productData = $this->loadDataSet('Product', $productType . '_product_minimal');
                break;
        }
        //Steps
        $this->productHelper()->createProduct($productData, $productType);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }


    public function productTypesDataProvider()
    {
        return array(
            array('simple_custom'),
            array('virtual'),
            array('downloadable'),
            array('configurable'),
            array('fixed_bundle'),
            array('dynamic_bundle'),
            array('grouped'));
    }

    /**
     * <p>Creating product with empty required fields</p>
     *
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Choose "Attribute Set" - "Minimal";</p>
     * <p>3. Choose "Product Type" - "Simple";</p>
     * <p>4. Click "Continue" button;</p>
     * <p>5. On "General" tab: set  "Visibility " to "-- Please Select --";</p>
     * <p>6. Click "Save" button.;</p>
     *
     * <p>Expected result:</p>
     * <p>1. Simple product is not created. System displays successful message "The product has been saved.";</p>
     * <p>2. Received error message "This is a required field." for fields:</p>
     * <p>Name, Description, Short Description, SKU, Price, Status, Visibility, Tax Class, Weight;</p>
     *
     * @param $emptyField
     * @param $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @TestlinkId TL-MAGE-5710
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data

        if ($fieldType == 'dropdown') {
            $overrideData = array($emptyField => '-- Please Select --');
        } else {
            $overrideData = array($emptyField => '%noValue%');
        }
        $productData = $this->loadDataSet('Product', 'simple_product_minimal', $overrideData);
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Data Provider for verification Required Fields
     */
    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('general_name', 'field'),
            array('general_description', 'field'),
            array('general_short_description', 'field'),
            array('general_sku', 'field'),
            array('general_weight', 'field'),
            array('general_status', 'dropdown'),
            array('general_visibility', 'dropdown'),
            array('general_min_price', 'field'),
            array('general_min_tax_class', 'dropdown'));
    }
}