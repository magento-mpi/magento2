<?php
/**
 * Magento
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_AttributeSet
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Products creation tests based on different attribute set
 */
class Core_Mage_AttributeSet_Create_FromProductPageTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
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
     * <p>Create New Attribute Set from Product page with help Create New Attribute button while create new product</p>
     * <p>without saving this product</p>
     *
     * @test
     * @TestlinkId TL-MAGE-5920
     */
    public function withoutSavingProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown');
        $newAttributeSet = $this->generate('string', 10, ':alnum:');
        $baseAttributeSet = $productData['product_attribute_set'];
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $newAttributeSet);
        //Verifying
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet($newAttributeSet);
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeData['attribute_code']));
        $this->attributeSetHelper()->openAttributeSet($baseAttributeSet);
        $this->attributeSetHelper()->verifyAttributeAssignment(array($attributeData['attribute_code']), false);
    }

    /**
     * <p>Create New Attribute Set from Product page with help Create New Attribute button while create new product</p>
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
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->addParameter('productName', 'New Product');
        $this->addParameter('attributeSet', $attributeSetName);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'Attribute set was not changed in product name');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct($productSkuDefault);
        $this->addParameter('attributeCodeField', $attributeData['attribute_code']);
        $this->assertFalse($this->controlIsPresent('field', 'general_user_attr_field'),
            'Created attribute was added to Default attribute set');
    }

    /**
     * <p>Create New Attribute Set from Product page with help Create New Attribute button while editing product</p>
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
        //Preconditions
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->openTab('general');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->addParameter('productName', $productData['general_name']);
        $this->addParameter('attributeSet', $attributeSetName);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'Attribute set was not changed in product name');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct($productSkuDefault);
        $this->addParameter('attributeCodeField', $attributeData['attribute_code']);
        $this->assertFalse($this->controlIsPresent('field', 'general_user_attr_field'),
            'Created attribute was added to Default attribute set');
    }

    /**
     * <p>New Attribute created from Product page to new set, assigned to General tab only</p>
     *
     * @test
     * @TestLinkId TL-MAGE-5917
     */
    public function editProductMetaTab()
    {
        //Data
        $attributeSetName = $this->generate('string', 10, ':alnum:');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield');
        //Preconditions
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->openTab('meta_information');
        $this->productAttributeHelper()->createAttributeOnProductTab($attributeData, $attributeSetName);
        $this->openTab('meta_information');
        //Verifying
        $this->addParameter('productName', $productData['general_name']);
        $this->addParameter('attributeSet', $attributeSetName);
        $this->assertTrue($this->controlIsPresent('pageelement', 'product_name'),
            'Attribute set was not changed in product name');
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
    }
}