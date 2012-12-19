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
     * <p>Data provider for changing product type from simple to virtual/downloadable</p>
     *
     * @return array
     */
    public function fromSimpleToVirtualDataProvider()
    {
        return array(
            array('virtual', 'Virtual Product'),
            array('downloadable', 'Downloadable Product')
        );
    }

    /**
     * <p>Virtual/Downloadable to Simple/Downloadable/Virtual product type switching on product creation</p>
     *
     * @param string $initialProduct
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromVirtualDownloadableDataProvider
     * @TestLinkId TL-MAGE-6428, TL-MAGE-6429, TL-MAGE-6430, TL-MAGE-6431
     */
    public function fromVirtualDownloadableDuringCreation($initialProduct, $changedProduct, $changedType)
    {
        //Data
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->selectTypeProduct($initialProduct);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
            'Weight field is editable or is not visible');
        $this->assertTrue($this->getControlElement('checkbox', 'general_weight_and_type_switcher')->selected(),
            'Weight checkbox is not selected');
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('general_weight_and_type_switcher', 'no');
        }
        $this->productHelper()->fillProductInfo($productData);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($changedType, $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        if ($changedProduct == 'simple') {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight'), 'Weight field is not editable');
            $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        } else {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
                'Weight field is editable or is not visible');
            $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        }
    }

    /**
     * <p>Data provider for changing virtual product type to simple/virtual/downloadable</p>
     *
     * @return array
     */
    public function fromVirtualDownloadableDataProvider()
    {
        return array(
            array('virtual', 'simple', 'Simple Product', 'Virtual Product'),
            array('virtual', 'downloadable', 'Downloadable Product', 'Virtual Product'),
            array('downloadable', 'simple', 'Simple Product', 'Downloadable Product'),
            array('downloadable', 'virtual', 'Virtual Product', 'Downloadable Product')
        );
    }

    /**
     * <p>Simple to Virtual/Downloadable product type switching while product editing</p>
     *
     * @param string $changedProduct
     * @param string $changedType
     *
     * @test
     * @dataProvider fromSimpleToVirtualDataProvider
     * @TestLinkId TL-MAGE-6432, TL-MAGE-6433
     */
    public function fromSimpleToVirtualDuringEditing($changedProduct, $changedType)
    {
        //Data
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $simpleProduct['general_sku']));
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
     * <p>Virtual/Downloadable to Simple/Downloadable/Virtual product type switching while product editing</p>
     *
     * @param string $initialProduct
     * @param string $changedProduct
     * @param string $changedType
     * @param string $initialType
     *
     * @test
     * @dataProvider fromVirtualDownloadableDataProvider
     * @TestLinkId TL-MAGE-6434, TL-MAGE-6435, TL-MAGE-6436, TL-MAGE-6437
     */
    public function fromVirtualDownloadableDuringEditing($initialProduct, $changedProduct, $changedType, $initialType)
    {
        //Data
        $initialProductData = $this->loadDataSet('Product', $initialProduct . '_product_visible');
        $productData = $this->loadDataSet('Product', $changedProduct . '_product_visible');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($initialProductData, $initialProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $initialProductData['general_sku']));
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('general_weight_and_type_switcher', 'no');
        } else {
            if ($changedProduct == 'virtual') {
                $this->productHelper()->deleteDownloadableInformation('sample');
                $this->productHelper()->deleteDownloadableInformation('link');
                $changedType = $initialType;
            }
        }
        $this->productHelper()->fillProductInfo($productData);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals($changedType, $this->productHelper()->getProductDataFromGrid($search, 'Type'),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct($search);
        if ($changedProduct == 'simple') {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight'), 'Weight field is not editable');
            $this->assertFalse($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        } else {
            $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
                'Weight field is editable or is not visible');
            $this->assertTrue($this->controlIsVisible('tab', 'downloadable_information'),
                'Downloadable Information is absent');
        }
    }

    /**
     * <p>Verify, that Weight field and Is Virtual checkbox is absent for Configurable and Grouped products</p>
     * <p>that Is Virtual checkbox isn't selected for Bundle products</p>
     *
     * @param $productType
     * @param $isWeightDisabled
     *
     * @test
     * @dataProvider isWeightDisabledDataProvider
     * @TestLinkId TL-MAGE-6459, TL-MAGE-6460
     */
    public function checkDefaultIsVirtual($productType, $isWeightDisabled)
    {
        //Steps
        $this->productHelper()->selectTypeProduct($productType);
        //Verification grouped product
        if ($isWeightDisabled === null) {
            $this->assertFalse($this->controlIsVisible('field', 'general_weight'));
            $this->assertFalse($this->controlIsVisible('checkbox', 'general_weight_and_type_switcher'));
        }
        //Verification for Bundle product (Dynamic and Fixed)
        else {
            $this->assertEquals($isWeightDisabled,
                $this->getControlElement('checkbox', 'general_weight_and_type_switcher')->selected());
            $this->assertFalse($this->getControlElement('field', 'general_weight')->enabled());
            $this->fillDropdown('general_weight_type', 'Fixed');
            $this->assertTrue($this->getControlElement('field', 'general_weight')->enabled());
        }
    }

    /**
     * <p>Data provider for default values for Is Virtual checkbox according product types</p>
     *
     * @return array
     */
    public function isWeightDisabledDataProvider()
    {
        return array(
            array('grouped', null),
//            array('bundle', false), MAGETWO-6269
        );
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

        return array(
            'attributeName' => $attributeData['admin_title'],
            'productSku' => $simpleProduct['general_sku'],
            'attributeValue' => $attributeData['option_1']['admin_option_name']
        );
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
            array(
                'general_configurable_attribute_title' => $data['attributeName'],
                'general_configurable_data' => $this->loadDataSet('Product', 'general_configurable_data',
                    array(
                        'associated_sku' => $data['productSku'],
                        'associated_attribute_value' => $data['attributeValue']
                    )
                )
            )
        );
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps and Verifying
        $this->productHelper()->selectTypeProduct($initialType);
        if ($initialType == 'simple') {
            $this->fillCheckbox('general_weight_and_type_switcher', 'yes');
        }
        $this->assertTrue($this->getControlElement('checkbox', 'general_weight_and_type_switcher')->selected(),
            'Weight checkbox is not selected');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
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
        $this->fillCheckbox('general_weight_and_type_switcher', 'no');
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
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible',
            array(
                'general_configurable_attribute_title' => $data['attributeName'],
                'general_configurable_data' => $this->loadDataSet('Product', 'general_configurable_data',
                    array(
                        'associated_sku' => $data['productSku'],
                        'associated_attribute_value' => $data['attributeValue']
                    )
                )
            )
        );
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $simple['general_sku']));
        //Steps
        $this->productHelper()->createProduct($configurableProduct, 'configurable', false);
        $this->openTab('general');
        $this->fillCheckbox('is_configurable', 'no');
        $this->assertFalse($this->controlIsVisible('pageelement', 'product_variations_fieldset'),
            'Product variation block is present');
        $this->fillCheckbox('general_weight_and_type_switcher', 'no');
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
            array(
                'general_configurable_attribute_title' => $data['attributeName'],
                'general_configurable_data' => $this->loadDataSet('Product', 'general_configurable_data',
                    array(
                        'associated_sku' => $data['productSku'],
                        'associated_attribute_value' => $data['attributeValue']
                    )
                )
            )
        );
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($initialProduct, $initialType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $initialProduct['general_sku']));
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
            array(
                'general_configurable_attribute_title' => $data['attributeName'],
                'general_configurable_data' => $this->loadDataSet('Product', 'general_configurable_data',
                    array(
                        'associated_sku' => $data['productSku'],
                        'associated_attribute_value' => $data['attributeValue']
                    )
                )
            )
        );
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
