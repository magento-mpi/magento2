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
 * Product creation tests
 */
class Core_Mage_Product_Create_AddSaveButtonsTest extends Mage_Selenium_TestCase
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
     * Creating new required attribute and adding it to new attribute set
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $attribute = $this->loadDataSet('ProductAttribute', 'product_attribute_textfield',
            array('values_required' => 'Yes'));
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('Product Details' => array($attribute['advanced_attribute_properties']['attribute_code'])));
        //Creating attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attribute);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Creating attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'attributeSet' => $attributeSet['set_name'],
            'attributeCode' => $attribute['advanced_attribute_properties']['attribute_code']
        );
    }

    /**
     * <p>Creating simple product with required fields using "Add Product" split button</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6084
     */
    public function defaultActionOfAddProductButton()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->clickButton('add_new_product_split', false);
        $this->waitForPageToLoad();
        $this->addParameter('productType', $this->defineParameterFromUrl('type'));
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        $this->productHelper()->fillProductInfo($productData);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku'],
            'product_type' => 'Simple Product'));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Create new product via 'Save & New' action in 'Save' split-button</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6610
     */
    public function saveAndNewAction(array $data)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required',
            array('product_attribute_set' => $data['attributeSet']));
        $productData['general_user_attr']['field'][$data['attributeCode']] = $this->generate('string', 10, ':alnum:');
        //Steps
        $this->productHelper()->selectTypeProduct('virtual');
        $this->productHelper()->changeAttributeSet($productData['product_attribute_set']);
        $this->productHelper()->fillProductInfo($productData);
        $this->productHelper()->saveProduct('new');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('new_product', $this->getCurrentPage());

        $this->assertContains($productData['product_attribute_set'],
            $this->getControlElement('pageelement', 'product_attribute_set')->text(),
            'Incorrect attribute set.');
        $this->assertEquals('virtual', $this->defineParameterFromUrl('type'), 'Incorrect product type');
    }

    /**
     * <p>Verification saving if user defined attribute is required<p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-6611
     */
    public function verifySaveButtonAvailability(array $data)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('product_attribute_set' => $data['attributeSet']));
        $userAttribute['general_user_attr']['field'][$data['attributeCode']] = $this->generate('string', 10, ':alnum:');
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->productHelper()->changeAttributeSet($productData['product_attribute_set']);
        $this->productHelper()->fillProductInfo($productData);
        //Verifying
        $this->productHelper()->saveProduct('continueEdit');
        $this->addParameter('attributeCodeField', $data['attributeCode']);
        $this->addFieldIdToMessage('field', 'general_user_attr_field');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
        $this->productHelper()->openProductTab('general');
        $this->productHelper()->fillUserAttributesOnTab($userAttribute, 'general');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
    }
}