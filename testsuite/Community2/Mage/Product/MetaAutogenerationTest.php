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
 * Autogenerated Meta Description, Meta Keywords, Meta Title fields
 */
class Community2_Mage_Product_MetaAutogenerationTest extends Mage_Selenium_TestCase
{
    public static $placeholders = array('{{name}}', '{{sku}}', '{{description}}', '{{short_description}}');

    /**
     * <p>Preconditions:</p>
     *  <p>1. Log in to admin</p>
     *  <p>2. Navigate System - Configuration</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>1. Set default values for Meta fields Autogeneration mask.</p>
     * <p>2. Set Meta attributes as non-required and without default values</p>
     */
    protected function tearDownAfterTestClass()
    {
        //System settings
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('meta_title_mask' => '{{name}}', 'meta_description_mask' => '{{name}} {{description}}',
                  'meta_keyword_mask' => '{{name}}, {{sku}}', 'sku_mask'   => '{{name}}'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //System attributes
        $this->productAttributeHelper()->editAttribute('meta_title',
            array('default_text_field_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->editAttribute('meta_description',
            array('default_text_area_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->editAttribute('meta_keyword',
            array('default_text_area_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    /**
     * <p>Meta Tab auto-generation verification</p>
     * <p>Preconditions:</p>
     *  <p>1a. Mask for Meta Title auto-generation = {{name}}</p>
     *  <p>1b. Mask for Meta Keyword auto-generation = {{name}}, {{sku}}</p>
     *  <p>1c. Mask for Meta Description auto-generation = {{name}} {{description}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Enter 'Name' text to Name field.</p>
     *  <p>5. Enter 'Description' text to Description field.</p>
     *  <p>6. Enter 'SKU' to SKU field.</p>
     *
     * <p>Expected results:</p>
     *  <p>1a. Meta Title field is equal to Product Name 'Name'.</p>
     *  <p>1b. Meta Keywords field is equal to Product Name, Product SKU 'Name, SKU'</p>
     *  <p>1c. Meta Description field is equal to Product Name and Product Description 'Name Description'.</p>
     *
     * @test
     * @dataProvider defaultMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6164
     */
    public function verifyDefaultMask($metaCode, $metaField, $metaMask)
    {
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData);
        $testData = $this->productHelper()->formFieldValueFromMask($metaMask, self::$placeholders);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->openTab('meta_information');
        $this->assertEquals($testData, $this->getValue($this->_getControlXpath('field', $metaField)));
    }

    /**
     * <p>DataProvider for verify default mask for Meta Fields Auto-Generation</p>
     *
     * @return array
     */
    public function defaultMetaMaskDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', '{{name}}'),
            array('meta_description', 'meta_information_meta_description', '{{name}} {{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords', '{{name}}, {{sku}}')
        );
    }

    /**
     * <p>Verifying, that autogeneration of meta fields does't work for product duplication</p>
     * <p> Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products</p>
     *  <p>3. Create new simple product with SKU autogeneration</p>
     *  <p>4. Find created product in Manage Products grid and open it</p>
     *  <p>5. Change Product Name to Name #2 and save it</p>
     *  <p>6. Click on Meta Information Tab</p>
     *
     * <p>Expected results:</p>
     *  <p>After Step 3. New product is created and is present in Manage Products grid with SKU = Product#1 Name</p>
     *  <p>After Step 5: Product Name has been changed to Name #2, SKU is 'Product#1 Name-1'</p>
     *  <p>After Step 7: a. Meta Title field is equal to 'Product #1 Name'</p>
     *  <p>b. Meta Keywords field is equal to 'Product #1 Name, Product #1 Name'</p>
     *  <p>c. Meta Description field is equal to 'Product #1 Name Product#1 Description'</p>
     *
     * @test
     * @depends verifyDefaultMask
     * @TestLinkId TL-MAGE-6165
     */
    public function duplicateSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->openTab('meta_information');
        $metaKeywords = $this->getValue($this->_getControlXpath('field', 'meta_information_meta_keywords'));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->fillField('general_name', 'Name#2');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku'] . '-1'));
        $this->productHelper()->verifyProductInfo(array('general_name'=> 'Name#2',
                                                        'general_sku' => $productData['general_sku'] . '-1'));
        $this->openTab('meta_information');
        $this->assertEquals($metaKeywords, $this->getValue(
            $this->_getControlXpath('field', 'meta_information_meta_keywords')));
    }

    /**
     * <p>Meta fields Auto-generation template verification</p>
     * <p>Preconditions:</p>
     *  <p>1. Setup different templates in Product Fields Auto-generation fieldset:</p>
     *  <p> "Mask for Meta Title" / "Mask for Meta Keywords" / "Mask for Meta Description" field</p
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields and don't enter any information on Meta tab</p>
     *  <p>5. Save product.</p>
     *  <p>6. Open created product.</p>
     *
     * <p>Expected results:</p>
     *  <p>Information in Meta field has been autogenerated according defined mask</p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $metaMask
     *
     * @test
     * @dataProvider templateMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6179
     */
    public function verifyMaskTemplates($metaCode, $metaField, $metaMask)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData);
        $testData = $this->productHelper()->formFieldValueFromMask($metaMask, self::$placeholders);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->openTab('meta_information');
        $this->assertEquals($testData, $this->getValue($this->_getControlXpath('field', $metaField)));
    }

    /**
     * <p>DataProvider for verify different masks for Meta Fields Auto-Generation</p>
     *
     * @return array
     */
    public function templateMetaMaskDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', $this->generate('string', 56, ':alnum:') . '{{name}}'),
            array('meta_description', 'meta_information_meta_description',
                  '{{name}}' . $this->generate('string', 41, ':alnum:') . '{{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                  '{{name}}, {{sku}}' . $this->generate('string', 47, ':alnum:')),
            array('meta_title', 'meta_information_meta_title', $this->generate('string', 32, ':punct:') . '{{name}}'),
            array('meta_description', 'meta_information_meta_description',
                  '{{name}}' . $this->generate('string', 32, ':punct:') . '{{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                  '{{name}}, {{sku}}' . $this->generate('string', 32, ':punct:')),
            array('meta_title', 'meta_information_meta_title', 'name' . ' ' . $this->generate('string', 10, ':alpha:')),
            array('meta_description', 'meta_information_meta_description',
                  '{{name}}' . 'description' . '{{short_description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                  'sku' . ' ' . ' name' . ' ' . $this->generate('string', 10, ':alpha:')),
            array('meta_title', 'meta_information_meta_title', '{{weight}} {{name}}'),
            array('meta_description', 'meta_information_meta_description', '{{nonexisted_attribute}}, {{name}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                  '{{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}'));
    }

    /**
     * <p>Verify, that autogeneration of Meta fields and SKU enabled if</p>
     * <p>Attribute set has been changed before enter product data</p>
     *
     * <p>Preconditions:</p>
     *  <p>1. at least 2 Attribute Sets exist - Default and Test Attribute set with Meta attributes -</p>
     *  <p>meta_title, meta_description, meta_keyword</p>
     *  <p>2. Default masks for fields autogeneration:</p>
     *  <p>SKU - {{name}}</p>
     *  <p>Meta Title - {{name}}</p>
     *  <p>Meta Keywords - {{name}}, {{sku}}</p>
     *  <p>Meta Description - {{name}} {{description}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Login to backend</p>
     *  <p>2. Navigate to Catalog - Manage Products</p>
     *  <p>3. Press Add Product button</p>
     *  <p>4. Press Change Attribute Set button, select "Test" Attribute set and apply changes</p>
     *  <p>5. Start fill required fields on New Product(Test) page and enter in Name field - "Name",</p>
     *  <p>   in Description field - "Description"</p>
     *  <p>6. Click on Meta Tab</p>
     *
     * <p>Expected results:</p>
     * <p>After Step 5: SKU field is equal to Name field - "Name"</p>
     * <p> After Step 6: All Meta fields on Meta Information tab are prepopulated according to masks:</p>
     * <p> Meta Title - "Name"</p>
     * <p> Meta Keywords - "Name, Name"</p>
     * <p> Meta Description - "Name Description"</p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $metaMask
     *
     * @test
     * @dataProvider templateMetaMaskDataProvider
     * @dataProvider defaultMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6214
     * @author Maryna_Ilnytska
     */
    public function afterChangeAttributeSet($metaCode, $metaField, $metaMask)
    {
        //Data
        $testData = $this->loadDataSet('AttributeSet', 'attribute_set');
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask, 'sku_mask' => '{{name}}'));
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('product_attribute_set' => $testData['set_name']));
        unset ($productData['general_sku']);
        //Preconditions
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($testData);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->openTab('general');
        $testData = $this->productHelper()->formFieldValueFromMask($metaMask, self::$placeholders);
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->assertEquals($productData['general_name'],
            $this->getValue($this->_getControlXpath('field', 'general_sku')));
        $this->openTab('meta_information');
        $this->assertEquals($testData, $this->getValue($this->_getControlXpath('field', $metaField)));
    }

    /**
     * <p>Meta Fields auto-generation is disabled if default value for meta attribute has been defined</p>
     * <p>Preconditions:</p>
     *  <p>1. Setup default templates in Product Fields Auto-generation fieldset:</p>
     *  <p>1a. Mask for Meta Title auto-generation = {{name}}</p>
     *  <p>1b. Mask for Meta Keyword auto-generation = {{name}}, {{sku}}</p>
     *  <p>1c. Mask for Meta Description auto-generation = {{name}} {{description}}</p>
     *  <p>2a. Set valid default values for meta_title, meta_description, meta_keyword attributes</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Enter 'Name' text to Name field.</p>
     *  <p>5. Enter 'Description' text to Description field.</p>
     *  <p>6. Enter 'SKU' to SKU field.</p>
     *  <p>7. Open Meta Information Tab.</p>
     *
     * <p>Expected results:</p>
     *  <p>1a. Meta Title field is equal to default value for meta_title attribute.</p>
     *  <p>1b. Meta Keywords field is equal to default value for meta_keyword attribute</p>
     *  <p>1c. Meta Description field is equal to default value for meta_description atrribute.</p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $mask
     * @param $fieldType
     *
     * @test
     * @dataProvider metaFieldsDataProvider
     * @TestLinkId TL-MAGE-6193
     */
    public function textAttributeDefaultValue($metaCode, $metaField, $fieldType, $mask)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $editedElement = $this->generate('string', 15, ':alnum:');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $mask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute($metaCode,
            array('default_' . $fieldType . '_value' => $editedElement));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->openTab('meta_information');
        $this->assertEquals($editedElement, $this->getValue($this->_getControlXpath('field', $metaField)));
    }

    public function metaFieldsDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', 'text_field', '{{name}}'),
            array('meta_description', 'meta_information_meta_description', 'text_area', '{{name}} {{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords', 'text_area', '{{name}}, {{sku}}'));
    }

    /**
     * <p>Create product with user-defined values for Meta Tags</p>
     * <p>Preconditions:</p>
     *  <p>1. Setup default templates in Product Fields Auto-generation fieldset:</p>
     *  <p>1a. Mask for Meta Title auto-generation = {{name}}</p>
     *  <p>1b. Mask for Meta Keyword auto-generation = {{name}}, {{sku}}</p>
     *  <p>1c. Mask for Meta Description auto-generation = {{name}} {{description}}</p>
     *  <p>2a. Set empty default values for meta_title, meta_description, meta_keyword attributes</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Enter 'Name' text to Name field.</p>
     *  <p>5. Enter 'Description' text to Description field.</p>
     *  <p>6. Enter 'SKU' to SKU field.</p>
     *  <p>7. Open Meta Information Tab.</p>
     *  <p>8. Enter new valid values to Meta Title, Meta Keywords, Meta Description fields
     *  <p>9. Save product and open it
     *
     * <p>Expected results:</p>
     *  <p>7. Fields on Meta Information tab is displayed information generated according masks.</p>
     *  <p>9. Fields on Meta Information tab is displayed information entered by user</p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $fieldType
     *
     * @test
     * @dataProvider defaultMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6194
     */
    public function saveWithUserDefinedValues($metaCode, $metaField, $fieldType)
    {
        //Preconditions
        $this->productAttributeHelper()->editAttribute($metaCode,
            array('values_required' => 'No', 'default_' . $fieldType . '_value' => ''));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $metaMask = $this->generate('string', 255, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData);
        $this->openTab('meta_information');
        $this->fillField($metaField, $metaMask);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->productHelper()->verifyProductInfo($productData, array($metaCode => $metaMask));
    }

    /**
     * <p>Verify that product with Meta fields autogeneration has been created without verification errors </p>
     * <p>when meta attributes set as required</p>
     *
     * <p>Preconditions:</p>
     *  <p>1. Setup meta attributes as required</p>
     *  <p>2a. Mask for Meta Title auto-generation = {{name}}</p>
     *  <p>2b. Mask for Meta Keyword auto-generation = {{name}}, {{sku}}</p>
     *  <p>2c. Mask for Meta Description auto-generation = {{name}} {{description}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields and don't enter any information on Meta tab</p>
     *  <p>5. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p> Success message appears, no verification errors for meta fields </p>
     *
     * @test
     * @TestLinkId TL-MAGE-6192
     */
    public function setMetaTabRequired()
    {
        //Data
        $metaAttributes = array('meta_title', 'meta_keyword', 'meta_description');
        $editedElement = array('values_required' => 'Yes',);
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_attributes');
        foreach ($metaAttributes as $value) {
            $this->productAttributeHelper()->editAttribute($value, $editedElement);
            $this->assertMessagePresent('success', 'success_saved_attribute');
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true);
        //Verifications
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Meta fields Auto-generation is disabled if Autogeneration mask field is empty </p>
     * <p>Preconditions:</p>
     *  <p>1. Setup empty mask in Product Fields Auto-generation fieldset:</p>
     *  <p> "Mask for Meta Title" / "Mask for Meta Keywords" / "Mask for Meta Description" field</p
     *  <p>2. Setup empty default value for meta_title, meta_description, meta_keyword product attributes </p>
     *  <p>3. Setup Values Required - Yes for meta_title, meta_description, meta_keyword product attributes </p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields and don't enter any information on Meta tab</p>
     *  <p>5. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p> Verify that meta fields are empty and messages "This is a required field." appear under all meta fields </p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $fieldType
     *
     * @test
     * @dataProvider metaFieldsDataProvider
     * @TestLinkId TL-MAGE-6191
     */
    public function emptyMetaMask($metaCode, $metaField, $fieldType)
    {
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask'   => ''));
        $this->systemConfigurationHelper()->configure($systemConfig);
        $this->productAttributeHelper()->editAttribute($metaCode,
            array('values_required' => 'Yes', 'default_' . $fieldType . '_value' => ''));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true);
        //Verifying
        $this->openTab('meta_information');
        $this->addFieldIdToMessage('field', $metaField);
        $this->assertMessagePresent('validation', 'empty_required_field');
    }
}
