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
class Core_Mage_Product_SkuAutoGenerationTest extends Mage_Selenium_TestCase
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
        $systemConfig =
            $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks', array('sku_mask' => '{{name}}'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //System attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute('sku', array('default_text_field_value' => ''));
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
        $systemConfig =
            $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks', array('sku_mask' => '{{name}}'));
        //Setup config
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
    }

    /**
     * <p>SKU auto-generation verification</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6062
     */
    public function verificationOfSkuAutogeneration()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->assertSame($productData['general_name'], $this->getControlAttribute('field', 'general_sku', 'value'),
            'SKU is not equal to product name.');
    }

    /**
     * <p>Create product using SKU auto-generation</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6054
     */
    public function saveWithAutogeneratedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $productData['general_name'];
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>Create product with user-defined SKU</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6057
     */
    public function saveWithUserDefinedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        $productSku = $this->generate('string', 15, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $this->fillField('general_sku', $productSku);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $productSku;
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>Create product with long Name</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6064
     */
    public function saveWithLongName()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name' => $this->generate('string', 65, ':alnum:'), 'general_sku' => '%noValue%'));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Create product with already existed SKU</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6061
     */
    public function saveWithExistedSku()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Precondition
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_name']);
        $this->addParameter('productSku', $productData['general_sku']);
        $this->addParameter('productName', $productData['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo(array('general_sku' => $productData['general_sku']));
    }

    /**
     * <p>Creating duplicated simple product</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6079
     */
    public function duplicateSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Precondition
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->assertSame($this->productHelper()->getGeneratedSku($productData['general_name']),
            $this->getControlAttribute('field', 'general_sku', 'value'), 'SKU is not equal to product name.');
    }

    /**
     * <p>Creating product with empty SKU mask</p>
     *
     * @test
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6096
     */
    public function emptySkuMask()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Preconditions
        $systemConfig =
            $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks', array('sku_mask' => ''));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_sku');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>SKU Auto-generation template verification</p>
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
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        //Preconditions
        $systemConfig =
            $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks', array('sku_mask' => $skuMask));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = str_replace('{{name}}', $productData['general_name'], $skuMask);
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->assertSame($productData['general_sku'], $this->getControlAttribute('field', 'general_sku', 'value'),
            'SKU is not equal to product name.');
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
     *
     * @test
     * @TestLinkId TL-MAGE-6073
     */
    public function attributeDefaultValue()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_sku' => '%noValue%'));
        $sku = $this->generate('string', 15, ':alnum:');
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->editAttribute('sku', array('default_text_field_value' => $sku));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $sku));
        $this->assertSame($sku, $this->getControlAttribute('field', 'general_sku', 'value'),
            'SKU is not equal to default SKU value which was set on attribute level.');
    }
}