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
class Community2_Mage_AttributeSet_Create_FromProductPageTest extends Mage_Selenium_TestCase
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
     * <p>Steps:</p>
     *  <p>1. Go to Catalog - Manage Products</p>
     *  <p>2. Press the "Add Product" button </p>
     *  <p>3. Select "Default" in Attribute Set dropdown list Select "Simple Product" in Product Type dropdown list.</p>
     *  <p>4. Press the "Continue" button</p>
     *  <p>5. Press the "Create New Attribute" button in General tab</p>
     *  <p>6. Fill attribute information and press the "Save in New Attribute Set" button</p>
     *  <p>7. Enter name for new Attribute Set in this field and press "OK"</p>
     *  <p>8. Go to Catalog - Attributes - Manage Attribute Sets without save this product</p>
     *  <p>9. Click on created Attribute Set in grid</p>
     *  <p>10. Go to Catalog - Attributes - Manage Attribute Sets and click on Default Attribute set</p>
     *
     * <p>Expected result:</p>
     *  <p>After Step 7. Created Attribute Set is present in grid </p>
     *  <p>After Step 8. Edit Attribute Set '%setName%' page opens, created attribute is present in General group</p>
     *  <p>After Step 9. Edit Attribute Set 'Default' page opens, created attribute is present in</p>
     *  <p>Unassigned Attributes group</p>
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
        $this->productAttributeHelper()
            ->createAttributeOnProductPage($attributeData, 'general', $newAttributeSet, true);
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
     * <p>Steps:</p>
     *  <p>1. Go to Catalog->Manage Products</p>
     *  <p>2. Click on "Add Product" button</p>
     *  <p>3. Select "Default" in Attribute Set dropdown list</p>
     *  <p>4. Select "Simple Product" in Product Type dropdown list and press the "Continue" button</p>
     *  <p>5. Fill all required fields for create new product</p>
     *  <p>6. Press the "Create New Attribute" button</p>
     *  <p>7. Fill attribute information and press the "Save in New Attribute Set" button</p>
     *  <p>8. Enter name for new Attribute Set in this field and press "OK"</p>
     *
     * <p>Expected result:</p>
     *  <p>2. New Product Page with Create Product Settings fieldset opens</p>
     *  <p>3. New Product (Default) page opens</p>
     *  <p>6. New Product Attribute pop-up window appears</p>
     *  <p>7.Confirmation window with "Enter Name for New Attribute Set" field appears</p>
     *  <p>8.1.After closing pop-up window Product page title has been changed - New Product (New Attribute Set)</p>
     *  <p>8.2. Created attribute is present on Product page in General tab </p>
     *  <p>8.3. Entered Product information is saved</p>
     *  <p>8.4. Created attribute is absent in Default Attribute set</p>
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
        $this->productAttributeHelper()
            ->createAttributeOnProductPage($attributeData, 'general', $attributeSetName, true);
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
     * <p>Preconditions:</p>
     *  <p> 1. 2 simple product, based on Default Attribute Set have been created</p>
     *
     * <p>Steps:</p>
     *  <p>1. Go to Catalog - Manage Products</p>
     *  <p>2. Find one of created products in the grid and click on it</p>
     *  <p>3. Press the Create New Attribute button</p>
     *  <p>4. Fill attribute information and press the Save in New Attribute Set button</p>
     *  <p>5. Enter name for new Attribute Set in this field and press "OK"</p>
     *
     * <p>Expected result:</p>
     *  <p>1. After closing pop-up window Product page title has been changed - %ProductName% (New Attribute Set)</p>
     *  <p>2. Created attribute is present on Product page in General tab </p>
     *  <p>3. Entered Product information is saved</p>
     *  <p>4. Created attribute is absent in General tab on Product #2(Default) page</p>
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
        $this->productAttributeHelper()
            ->createAttributeOnProductPage($attributeData, 'general', $attributeSetName, true);
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
     * <p>Preconditions:</p>
     *  <p>1. Product, based on Default Attribute set is created</p>
     *
     * <p>Steps:</p>
     *  <p>1. Go to Catalog - Manage Products</p>
     *  <p>2. Find created Product in grid and open it</p>
     *  <p>3. Press the Create New Attribute button on Product Page, from Meta Information tab</p>
     *  <p>4. Fill attribute data and press the "Save in New Attribute Set" button.</p>
     *  <p>5. Fill name for new Attribute Set and press "OK"</p>
     *
     * <p>Expected result:</p>
     *  <p>1. Created Attribute is present in Meta Information tab.</p>
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
        $this->productAttributeHelper()
            ->createAttributeOnProductPage($attributeData, 'meta_information', $attributeSetName, true);
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
