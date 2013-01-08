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
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('General' => $attributeData['attribute_code']));
        //Create attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'product_attribute_set' => $attributeSet['set_name'],
            'assigned_attribute'    => $attributeData['attribute_code']
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
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible');
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
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible',
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
        $this->productHelper()->verifyProductInfo($productDataInitial);
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
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible');
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
        $productDataInitial = $this->loadDataSet('Product', $productType . '_product_visible',
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
        $this->productHelper()->verifyProductInfo($productDataInitial);
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
//            array('grouped'), MAGETWO-6340
//            array('bundle') MAGETWO-6340
        );
    }

    /**
     * <p>Change attribute set for Configurable product during creation</p>
     *
     * @test
     * @testlinkId TL-MAGE-6471
     */
    public function forConfigurableDuringCreation()
    {
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        //Verifying
        $this->assertTrue($this->controlIsVisible('button', 'change_attribute_set_disabled'));
    }
}
