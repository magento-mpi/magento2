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
 * Product type changing while product creation/editing
 */
class Core_Mage_Product_ChangeProductTypeTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * <p>Simple to Virtual/Downloadable product type switching on product creation</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6426, TL-MAGE-6427
     */
    public function fromSimpleToVirtualDuringCreation($changedProduct, $changedType)
    {
        //Data
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->fillCheckbox('general_weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($changedType, $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
            'Weight field is editable or is not visible');
        $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
            'Downloadable Information is absent');
    }

    /**
     * <p>Create attribute, attribute set and simple product to use while creating configurable product</p>
     *
     * @return array
     *
     * @test
     */
    public function prepareConfigurableData()
    {
        //Data
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible');
        $simpleProduct['general_user_attr']['dropdown'][$attributeData['attribute_code']] =
            $attributeData['option_1']['admin_option_name'];
        //Create attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Create simple product for configurable product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array('attributeName'  => $attributeData['admin_title'], 'productSku' => $simpleProduct['general_sku'],
                     'attributeValue' => $attributeData['option_1']['admin_option_name'],
                     'productName'    => $simpleProduct['general_name']);
    }

    /**
     * <p>Configurable Product from Simple/Virtual/Downloadable Product During Creation</p>
     *
     * @param string $initialType
     * @param array $data
     *
     * @test
     * @dataProvider toConfigurableDataProvider
     * @depends prepareConfigurableData
     * @TestlinkId TL-MAGE-6462, TL-MAGE-6463, TL-MAGE-6464
     */
    public function toConfigurableDuringCreation($initialType, $data)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_product_name' => $data['productName'], 'associated_sku' => $data['productSku']),
            array('var1_attr_value1' => $data['attributeValue'], 'general_attribute_1' => $data['attributeName']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps and Verifying
        $this->productHelper()->selectTypeProduct($initialType);
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
        $this->fillCheckbox('general_weight_and_type_switcher', 'No');
        $this->waitForControlEditable('field', 'general_weight');
        $this->productHelper()->fillProductInfo($configurable);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('Configurable Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is absent');
    }

    public function toConfigurableDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable')
        );
    }

    /**
     * <p>Simple Product from Configurable Product During Creation</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6465
     */
    public function fromConfigurableToSimpleDuringCreation()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $simple['general_sku']));
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->assertTrue($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is absent');
        $this->fillCheckbox('is_configurable', 'no');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
        $this->productHelper()->fillProductInfo($simple);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('Simple Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertFalse($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is selected');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
    }

    /**
     * <p>Simple Product from Configurable Product with Selecting Configurable Attribute During Creation</p>
     *
     * @param array $data
     *
     * @test
     * @depends prepareConfigurableData
     * @TestlinkId TL-MAGE-6466
     */
    public function fromConfigurableWithAttributesToSimpleDuringCreation($data)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_product_name' => $data['productName'], 'associated_sku' => $data['productSku']),
            array('var1_attr_value1' => $data['attributeValue'], 'general_attribute_1' => $data['attributeName']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $simple['general_sku']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->openTab('general');
        $this->fillCheckbox('is_configurable', 'no');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
        $this->productHelper()->fillProductInfo($simple);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('Simple Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertFalse($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is selected');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
    }

    /**
     * <p>Configurable Product from Simple/Virtual/Downloadable Product During Editing</p>
     *
     * @param string $initialType
     * @param array $data
     *
     * @test
     * @dataProvider toConfigurableDataProvider
     * @depends prepareConfigurableData
     * @TestlinkId TL-MAGE-6467, TL-MAGE-6468, TL-MAGE-6469
     */
    public function toConfigurableDuringEditing($initialType, $data)
    {
        //Data
        $initialProduct = $this->loadDataSet('Product', $initialType . '_product_visible');
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_product_name' => $data['productName'], 'associated_sku' => $data['productSku']),
            array('var1_attr_value1' => $data['attributeValue'], 'general_attribute_1' => $data['attributeName']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($initialProduct, $initialType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $initialProduct['general_sku']));
        $this->fillCheckbox('general_weight_and_type_switcher', 'No');
        $this->waitForControlEditable('field', 'general_weight');
        $this->productHelper()->fillProductInfo($configurable);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('Configurable Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is absent');
    }

    /**
     * <p>Simple Product from Configurable Product During Editing</p>
     *
     * @param array $data
     *
     * @test
     * @depends prepareConfigurableData
     * @TestlinkId TL-MAGE-6470
     */
    public function editingConfigurable($data)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_product_name' => $data['productName'], 'associated_sku' => $data['productSku']),
            array('var1_attr_value1' => $data['attributeValue'], 'general_attribute_1' => $data['attributeName']));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $configurable['general_sku']));
        $this->assertTrue($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is absent');
        $this->productHelper()->unassignAllConfigurableVariations();
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals('Configurable Product', $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        $this->assertTrue($this->getControlElement('checkbox', 'is_configurable')->selected(),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is absent');
    }
}
