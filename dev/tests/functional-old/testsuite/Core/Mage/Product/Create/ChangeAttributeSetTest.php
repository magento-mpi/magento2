<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products creation tests with ability to change attribute set during creation and editing products
 */
class Core_Mage_Product_Create_ChangeAttributeSetTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $productAttributes = $this->loadDataSet('ProductAttribute', 'product_attributes_for_changing_template');
        $productAttributes['assignedAttribute'] = $this->loadDataSet('ProductAttribute',
            'product_attribute_dropdown_with_options');
        $assignedCode = $productAttributes['assignedAttribute']['advanced_attribute_properties']['attribute_code'];
        $groupName = $this->generate('string', 5, ':lower:') . '_test_group';
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $attributeCodes = array();
        //Create attributes
        $this->navigate('manage_attributes');
        foreach ($productAttributes as $name => $attribute) {
            $this->productAttributeHelper()->createAttribute($attribute);
            $this->assertMessagePresent('success', 'success_saved_attribute');
            if ($name == 'assignedAttribute') {
                $attributeSet['associated_attributes']['Product Details'] =
                    $attribute['advanced_attribute_properties']['attribute_code'];
            } else {
                $attributeCodes[] = $attribute['advanced_attribute_properties']['attribute_code'];
            }
        }
        //Create attribute set
        $attributeSet['associated_attributes'][$groupName] = $attributeCodes;
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'attributeSetName' => $attributeSet['set_name'],
            'assignedAttribute' => $assignedCode,
            'tabName' => $groupName,
            'attributeCodes' => $attributeCodes,
        );
    }

    /**
     * Change attribute set during product creation from Default to Custom attribute set
     *
     * @param string $productType
     * @param array $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5881, TL-MAGE-5896, TL-MAGE-5900, TL-MAGE-5901, TL-MAGE-5902
     */
    public function fromDefaultToCustomCreate($productType, $attributeSetData)
    {
        //Data
        $productData = $this->loadDataSet('Product', $productType . '_product_visible');
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        $newAttributeSet = $attributeSetData['attributeSetName'];
        //Steps
        $this->productHelper()->createProduct($productData, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->productHelper()->openProductTab('general');
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue(
            $this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't"
        );
        $productData['product_attribute_set'] = $newAttributeSet;
        $this->productHelper()->verifyProductInfo($productData);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product creating from Custom to Default attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5903, TL-MAGE-5904, TL-MAGE-5905, TL-MAGE-5906, TL-MAGE-5907
     */
    public function fromCustomToDefaultDuringCreation($productType, $attributeSetData)
    {
        //Data
        $productData = $this->loadDataSet('Product', $productType . '_product_visible',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        //Steps
        $this->productHelper()->createProduct($productData, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->productHelper()->openProductTab('general');
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse(
            $this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't"
        );
        $productData['product_attribute_set'] = $newAttributeSet;
        $this->productHelper()->verifyProductInfo($productData);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product editing from Default to Custom attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5884, TL-MAGE-5908, TL-MAGE-5909, TL-MAGE-5910, TL-MAGE-5911
     */
    public function fromDefaultToCustomDuringEditing($productType, $attributeSetData)
    {
        //Data
        $productData = $this->loadDataSet('Product', $productType . '_product_visible');
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        $newAttributeSet = $attributeSetData['attributeSetName'];
        //Steps
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue(
            $this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't"
        );
        $productData['product_attribute_set'] = $newAttributeSet;
        $this->productHelper()->verifyProductInfo($productData);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * Change attribute set during product editing from Custom to Default attribute set
     *
     * @param string $productType
     * @param $attributeSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5912, TL-MAGE-5913, TL-MAGE-5914, TL-MAGE-5915, TL-MAGE-5916
     */
    public function fromCustomToDefaultDuringEditing($productType, $attributeSetData)
    {
        //Data
        $productData = $this->loadDataSet('Product', $productType . '_product_visible',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $attributeSetData['assignedAttribute'];
        //Steps
        $this->productHelper()->createProduct($productData, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse(
            $this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't"
        );
        $productData['product_attribute_set'] = $newAttributeSet;
        $this->productHelper()->verifyProductInfo($productData);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * @return array
     */
    public function productTypeDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('grouped'),
            array('bundle')
        );
    }

    /**
     * Change attribute set for Configurable product during creation
     *
     * @test
     * @testlinkId TL-MAGE-6471
     */
    public function forConfigurableDuringCreation()
    {
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        //Verifying
        $this->assertTrue($this->controlIsVisible('button', 'attribute_set_toggle_disabled'));
    }

    /**
     * Change attribute set to new one with all type of user attribute several times
     *
     * @param array $attributeSetData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6886, TL-MAGE-6887, TL-MAGE-6888, TL-MAGE-6889, TL-MAGE-6891, TL-MAGE-6893, TL-MAGE-6894,
     *             TL-MAGE-6895, TL-MAGE-6902
     */
    public function changeAttributeSetSeveralTimes(array $attributeSetData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $search = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        $initialAttributeSet = $productData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $productData['product_attribute_set'] = $attributeSetData['attributeSetName'];
        for ($i = 0; $i < 3; $i++) {
            $this->productHelper()->changeAttributeSet($attributeSetData['attributeSetName']);
            $this->addParameter('productTabName', $attributeSetData['tabName']);
            $this->productHelper()->openProductTab('user_tab');
            //Verifying
            foreach ($attributeSetData['attributeCodes'] as $code) {
                $this->addParameter('elementId', strstr($code, '_fpt_') ? 'attribute-' . $code . '-container' : $code);
                $this->assertTrue($this->controlIsVisible('pageelement', 'element_by_id'));
            }
            $this->productHelper()->verifyProductInfo($productData);
            $this->productHelper()->changeAttributeSet($initialAttributeSet);
        }
        $this->productHelper()->changeAttributeSet($attributeSetData['attributeSetName']);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals(
            $attributeSetData['attributeSetName'],
            $this->productHelper()->getProductDataFromGrid($search, 'Attribute Set'),
            'Product has been saved with incorrect attribute set.'
        );
    }

    /**
     * Change attribute set with product related attributes
     *
     * @param array $attributeSet
     *
     * @test
     * @depends preconditionsForTests
     */
    public function fromDefaultToCustomWithoutSpecialPrice($attributeSet)
    {
        //Data
        $subCategoryData = $this->loadDataSet('Category', 'sub_category_required');
        $product = $this->loadDataSet('Product', 'simple_product_visible', array(
            'general_categories' => $subCategoryData['parent_category'] . '/' . $subCategoryData['name'],
            'prices_special_price' => '3.99'
        ));
        $verifyPrices = array(
            'general_price' => $product['general_price'],
            'prices_special_price' => $product['prices_special_price']
        );
        //Steps
        $this->navigate('manage_categories', false);
        $this->categoryHelper()->checkCategoriesPage();
        $this->categoryHelper()->createCategory($subCategoryData);
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->flushCache();

        //Verifying
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($subCategoryData['name']);
        $this->addParameter('productName', $product['general_name']);
        $this->assertEquals($verifyPrices, $this->productHelper()->getFrontendProductPrices());
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->assertEquals($verifyPrices, $this->productHelper()->getFrontendProductPrices());
        $this->clickButton('add_to_cart');
        $info = $this->shoppingCartHelper()->getProductInfoInTable();
        $this->assertSame(
            '$' . $product['prices_special_price'],
            $info['product_1']['unit_price'],
            'Special price is not applied'
        );
        //Steps
        $this->loginAdminUser();
        $this->attributeSetHelper()->openAttributeSet($attributeSet['attributeSetName']);
        $this->attributeSetHelper()->unassignAttributeFromSet(array('special_price'));
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_sku' => $product['general_sku']));
        $this->productHelper()->changeAttributeSet($attributeSet['attributeSetName']);
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->flushCache();
        //Verifying
        unset($verifyPrices['prices_special_price']);
        $this->frontend();
        $this->categoryHelper()->frontOpenCategory($subCategoryData['name']);
        $this->addParameter('productName', $product['general_name']);
        $this->assertEquals($verifyPrices, $this->productHelper()->getFrontendProductPrices());
        $this->productHelper()->frontOpenProduct($product['general_name']);
        $this->assertEquals($verifyPrices, $this->productHelper()->getFrontendProductPrices());
        $this->clickButton('add_to_cart');
        $info = $this->shoppingCartHelper()->getProductInfoInTable();
        $this->assertSame(
            '$' . $product['general_price'],
            $info['product_1']['unit_price'],
            'Special price is applied'
        );
    }

    /**
     * Change attribute set with required field
     *
     * @param array $attributeSetData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6900
     */
    public function withRequiredAttribute(array $attributeSetData)
    {
        //Data
        $product = $this->loadDataSet('Product', 'simple_product_required',
            array('product_attribute_set' => $attributeSetData['attributeSetName']));
        $attribute = array('attribute_properties' => array('values_required' => 'Yes'));
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute($attributeSetData['assignedAttribute'], $attribute);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product, 'simple', false);
        $this->productHelper()->openProductTab('general');
        //Verifying
        $this->addParameter('attributeCodeDropdown', $attributeSetData['assignedAttribute']);
        $this->assertTrue(
            $this->controlIsVisible('dropdown', 'general_user_attr_dropdown'),
            'Attribute with code ' . $attributeSetData['assignedAttribute'] . ' is absent'
        );
        $this->productHelper()->changeAttributeSet('Default');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }
}
