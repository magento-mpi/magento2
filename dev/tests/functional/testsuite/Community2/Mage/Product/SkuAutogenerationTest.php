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
 * Autogenerated SKU
 */
class Community2_Mage_Product_SkuAutogenerationTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *  <p>1. Log in to admin</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>1. Set default value to for SKU auto-generation = {{name}}</p>
     * <p>2. Reset default value to system attribute SKU</p>
     */
    protected function tearDownAfterTestClass()
    {
        //System settings
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('sku_mask' => '{{name}}'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //System attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute('sku',
            array('default_text_field_value' => ''));
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    /**
     * <p>Preconditions for tests:</p>
     *  <p>1. Setup Mask for SKU auto-generation</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('sku_mask' => '{{name}}'));
        //Setup config
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
    }

    /**
     * <p>SKU auto-generation verification</p>
     * <p>Preconditions:</p>
     *  <p>1. Mask for SKU = {{name}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Enter some text to Name field.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. SKU field is equal to product name.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6062
     */
    public function verificationOfSkuAutogeneration()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, false, 'general_name',
            array('general_sku'));
        //Verifying
        $this->assertEquals($productData['general_name'],
            $this->getValue($this->_getControlXpath('field', 'general_sku')), 'SKU is not equal to product name.');
    }

    /**
     * <p>Create product using SKU auto-generation</p>
     * <p>Preconditions:</p>
     *  <p>1. Mask for SKU = {{name}}</p>
     *  <p>2. Product name length is less than 64 symbols and unique.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU.</p>
     *  <p>5. Save product.</p>
     *  <p>6. Open created product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. SKU field is equal to product name.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6054
     */
    public function saveWithAutogeneratedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $productData['general_name'];
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>Create product with user-defined SKU</p>
     * <p>Preconditions:</p>
     *  <p>1. Mask for SKU = {{name}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU</p>
     *  <p>5. Enter valid value to SKU field.</p>
     *  <p>6. Save product.</p>
     *  <p>7. Open created product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. SKU field is equal to user-defined SKU.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6057
     */
    public function saveWithUserDefinedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productSku = $this->generate('string', 15, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, false, 'general_name',
            array('general_sku'));
        $this->fillField('general_sku', $productSku);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $productSku;
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>Create product with long Name</p>
     * <p>Preconditions:</p>
     *  <p>1. Mask for SKU = {{name}}</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU. To Name field enter value more than 64 characters.</p>
     *  <p>6. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1.  System displays "SKU length should be 64 characters maximum." message under SKU field.</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6064
     */
    public function saveWithLongName()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create product with already existed SKU</p>
     * <p>Preconditions:</p>
     *  <p>1. Mask for SKU = {{name}}</p>
     *  <p>2. Simple product with SKU = "simple_product_sku" is created.</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU</p>
     *  <p>5. Enter "simple_product_sku" to SKU field.</p>
     *  <p>6. Click "Save and Continue Edit" button.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. Product is saved, confirmation message appears.</p>
     *  <p>2. Product SKU is equal to "simple_product_sku-1".</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6061
     */
    public function saveWithExistedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Precondition
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProductWithAutogeneration($productData, false, 'general_name',
            array('general_sku'));
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verifying
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_name']);
        $this->addParameter('productSku', $productData['general_sku']);
        $this->addParameter('productName', $productData['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $this->productHelper()->verifyProductInfo(array('general_sku' => $productData['general_sku']));
    }

    /**
     * <p>Creating duplicated simple product</p>
     * <p>Preconditions:</p>
     *  <p>1. Simple product with SKU = "simple_product_sku" is created.</p>
     *
     * <p>Steps:
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Open existed product.</p>
     *  <p>4. Click "Duplicate" button.</p>
     *
     * <p>Expected result:</p>
     *  <p>1. Product is duplicated, confirmation message appears.</p>
     *  <p>2. Product SKU is equal to "simple_product_sku-1"
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6079
     */
    public function duplicateSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Precondition
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->clickButton('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->productHelper()->verifyProductInfo(array('general_sku' => $productData['general_name'] . '-1'));
    }

    /**
     * <p>Creating product with empty SKU mask</p>
     * <p>Preconditions:</p>
     *  <p>1. "Mask for SKU" is empty</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU.</p>
     *  <p>5. Save product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. System displays message 'This is a required field.' under 'SKU' field. </p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6096
     */
    public function emptySkuMask()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('sku_mask' => ''));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        //Verifying
        $this->addFieldIdToMessage('field', 'general_sku');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>SKU Auto-generation template verification</p>
     * <p>Preconditions:</p>
     *  <p>1. Setup different templates for "Mask for SKU"</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU.</p>
     *  <p>5. Save product.</p>
     *  <p>6. Open created product.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. SKU field is equal to prefix_product_name.</p>
     *
     * @param string $skuMask
     *
     * @test
     * @dataProvider skuMaskDataProvider
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6063
     */
    public function templateVerification($skuMask)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('sku_mask' => $skuMask));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = str_replace('{{name}}', $productData['general_name'], $skuMask);
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>DataProvider for Mask for SKU Auto-Generation</p>
     *
     * @return array
     */
    public function skuMaskDataProvider()
    {
        return array(
            array($this->generate('string', 15, ':alnum:')),
            array($this->generate('string', 15, ':alnum:') . '_{{name}}'),
            array($this->generate('string', 15, ':alnum:') . '_{{name}}_' . $this->generate('string', 15, ':alnum:')),
            array($this->generate('string', 15, ':punct:') . '_{{name}}')
        );
    }

    /**
     * <p>Verification SKU attribute default value</p>
     * <p>Preconditions:</p>
     *  <p>1. Default value for system attribute SKU is set to "sku_default_value"</p>
     *
     * <p>Steps:</p>
     *  <p>1. Log in to Backend.</p>
     *  <p>2. Go Catalog - Manage Products.</p>
     *  <p>3. Start to create new simple product.</p>
     *  <p>4. Fulfill all required fields except SKU.</p>
     *
     * <p>Expected results:</p>
     *  <p>1. SKU field is equal to "sku_default_value".</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6073
     */
    public function attributeDefaultValue()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $sku = $this->generate('string', 15, ':alnum:');
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute('sku',
            array('default_text_field_value' => $sku));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProductWithAutogeneration($productData, true, 'general_name',
            array('general_sku'));
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $sku;
        $this->productHelper()->openProduct(array('product_sku' => $sku));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }
}
