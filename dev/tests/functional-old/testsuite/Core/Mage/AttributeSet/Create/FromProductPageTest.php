<?php
/**
 * Magento
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Products creation tests based on different attribute set
 */
class Core_Mage_AttributeSet_Create_FromProductPageTest extends Mage_Selenium_TestCase
{
    /**
     * Preconditions:
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Creating simple product based on Default attribute set
     *
     * @test
     *
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('product_sku' => $productData['general_sku']);
    }

    /**
     * Create New Attribute Set from Product page with help Create New Attribute button while create new product
     * without saving this product
     *
     * @test
     * @TestlinkId TL-MAGE-5920
     */
    public function withoutSavingProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown');
        $attributeCode = $attributeData['advanced_attribute_properties']['attribute_code'];
        $newAttributeSet = $this->generate('string', 10, ':alnum:');
        $baseAttributeSet = $productData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $newAttributeSet);
        //Verifying
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet($newAttributeSet);
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode));
        $this->attributeSetHelper()->openAttributeSet($baseAttributeSet);
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeCode), false);
    }

    /**
     * Create New Attribute Set from Product page with help Create New Attribute button while create new product
     *
     * @param array $productSkuDefault
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5918
     */
    public function saveProduct($productSkuDefault)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        $attributeSetName = $this->generate('string', 10, ':alnum:');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($attributeSetName, $this->productHelper()->getProductDataFromGrid($search,
            'Attribute Set'), 'Attribute Set of product has not been changed');
        $this->productHelper()->openProduct($productSkuDefault);
        $this->addParameter('attributeCodeField', $attributeData['advanced_attribute_properties']['attribute_code']);
        $this->assertFalse($this->controlIsPresent('field', 'general_user_attr_field'),
            'Created attribute was added to Default attribute set');
    }

    /**
     * Create New Attribute Set from Product page with help Create New Attribute button while editing product
     *
     * @param array $productSkuDefault
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5919
     */
    public function editProduct($productSkuDefault)
    {
        //Data
        $attributeSetName = $this->generate('string', 10, ':alnum:');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Preconditions
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->openProductTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($attributeSetName, $this->productHelper()->getProductDataFromGrid($search,
            'Attribute Set'), 'Attribute Set of product has not been changed');
        $this->productHelper()->openProduct($productSkuDefault);
        $this->addParameter('attributeCodeField', $attributeData['advanced_attribute_properties']['attribute_code']);
        $this->assertFalse($this->controlIsPresent('field', 'general_user_attr_field'),
            'Created attribute was added to Default attribute set');
    }

    /**
     * New Attribute created from Product page to new set from Search Engine Optimization tab
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-5917
     */
    public function editProductMetaTab($productSkuDefault)
    {
        //Data
        $attributeSetName = $this->generate('string', 10, ':alnum:');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Preconditions
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->openProductTab('meta_information');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($attributeSetName, $this->productHelper()->getProductDataFromGrid($search,
            'Attribute Set'), 'Attribute Set of product has not been changed');
        $this->productHelper()->openProduct($productSkuDefault);
        $this->productHelper()->openProductTab('meta_information');
        $this->addParameter('attributeCodeField', $attributeData['advanced_attribute_properties']['attribute_code']);
        $this->assertFalse($this->controlIsPresent('field', 'meta_information_user_attr_field'),
            'Created attribute was added to Default attribute set');
    }
}
