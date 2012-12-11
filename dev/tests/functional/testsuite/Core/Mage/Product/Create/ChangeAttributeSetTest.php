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
class Core_Mage_Product_Create_ChangeAttributeSetTest extends Mage_Selenium_TestCase
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
     * <p>Preconditions for tests</p>
     * <p>1. Custom attribute set based on "Default" set is created</p>
     * <p>2. A new attribute is created and added to custom attribute set</p>
     *
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $testData = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('General' => $attrData['attribute_code']));
        $setName = $testData['set_name'];
        //Steps
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible',
            array('product_attribute_set' => $attributeSet['set_name']));
        $simpleProduct['general_user_attr']['dropdown'][$attributeData['attribute_code']] =
            $attributeData['option_1']['admin_option_name'];
        //Create attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($testData);
        //Verifying
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->attributeSetHelper()->openAttributeSet($attributeSet['set_name']);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Create simple product for configurable product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'product_attribute_set' => $attributeSet['set_name'],
            'assigned_attribute' => $attributeData['attribute_code'],
            'attributeName' => $attributeData['admin_title'],
            'productSku' => $simpleProduct['general_sku']
        );
    }

    /**
     * <p>Change attribute set during product creation from Default to Custom attribute set </p>
     *
     * @param string $productType
     * @param array $customSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5881, TL-MAGE-5896, TL-MAGE-5900, TL-MAGE-5901, TL-MAGE-5902
     */
    public function fromDefaultToCustomCreate($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required');
        $assignedAttribute = $customSetData['assigned_attribute'];
        $newAttributeSet = $customSetData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Change attribute set during product creating from Custom to Default attribute set </p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5903, TL-MAGE-5904, TL-MAGE-5905, TL-MAGE-5906, TL-MAGE-5907
     */
    public function fromCustomToDefaultDuringCreation($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required',
            array('product_attribute_set' => $customSetData['product_attribute_set']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $customSetData['assigned_attribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType, false);
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial, array($customSetData['assigned_attribute']));
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Change attribute set during product editing from Default to Custom attribute set </p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5884, TL-MAGE-5908, TL-MAGE-5909, TL-MAGE-5910, TL-MAGE-5911
     */
    public function fromDefaultToCustomDuringEditing($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required');
        $assignedAttribute = $customSetData['assigned_attribute'];
        $newAttributeSet = $customSetData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertTrue($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is absent attribute $assignedAttribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Change attribute set during product editing from Custom to Default attribute set</p>
     *
     * @param string $productType
     * @param $customSetData
     *
     * @test
     * @dataProvider productTypeDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5912, TL-MAGE-5913, TL-MAGE-5914, TL-MAGE-5915, TL-MAGE-5916
     */
    public function fromCustomToDefaultDuringEditing($productType, $customSetData)
    {
        //Data
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_required',
            array('product_attribute_set' => $customSetData['product_attribute_set']));
        $newAttributeSet = 'Default';
        $assignedAttribute = $customSetData['assigned_attribute'];
        //Steps
        $this->productHelper()->createProduct($productDataInitial, $productType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productDataInitial['general_sku']));
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        //Verifying
        $this->addParameter('attributeCodeDropdown', $assignedAttribute);
        $this->assertFalse($this->controlIsPresent('dropdown', 'general_user_attr_dropdown'),
            "There is present $assignedAttribute attribute, but shouldn't");
        $this->productHelper()->verifyProductInfo($productDataInitial, array($customSetData['assigned_attribute']));
        $this->saveForm('save');
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
     * <p>Change attribute set for Configurable product during creation</p>
     *
     * @param array $customSetData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-6471
     */
    public function forConfigurableDuringCreation($customSetData)
    {
        //Data
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'product_attribute_set' => $customSetData['product_attribute_set'],
            'configurable_attribute_title' => $customSetData['attributeName'],
            'associated_configurable_data' => $this->loadDataSet('Product', 'associated_configurable_data',
                array('associated_search_sku' => $customSetData['productSku'])))
        );
        $newAttributeSet = $customSetData['product_attribute_set'];
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->productHelper()->changeAttributeSet($newAttributeSet);
        $this->productHelper()->fillConfigurableSettings($configurableProduct);
        $this->productHelper()->fillProductInfo($configurableProduct, 'configurable');
        //Verifying
        $this->assertFalse($this->controlIsVisible('button', 'change_attribute_set'));
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $configurableProduct['general_sku']));
        $this->productHelper()->verifyProductInfo($configurableProduct);
    }
}
