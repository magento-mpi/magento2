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
class Community2_Mage_Product_ChangeProductTypeTest extends Mage_Selenium_TestCase
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
        //Steps
        $this->productHelper()->selectTypeProduct('simple');
        $this->fillCheckbox('weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $productData['general_sku']));
        $this->assertEquals($changedType, trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $productData['general_sku']));
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
        //Steps
        $this->productHelper()->selectTypeProduct($initialProduct);
        $this->assertTrue($this->controlIsVisible('field', 'general_weight_disabled'),
            'Weight field is editable or is not visible');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'weight_and_type_switcher')),
            'Weight checkbox is not selected');
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        }
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $productData['general_sku']));
        $this->assertEquals($changedType, trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $productData['general_sku']));
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
        //Steps
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $simpleProduct['general_sku']));
        $this->fillCheckbox('weight_and_type_switcher', 'yes');
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $productData['general_sku']));
        $this->assertEquals($changedType, trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $productData['general_sku']));
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
        //Steps
        $this->productHelper()->createProduct($initialProductData, $initialProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $initialProductData['general_sku']));
        if ($changedProduct == 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        } else {
            if ($changedProduct == 'virtual') {
                $this->productHelper()->deleteDownloadableInformation('sample');
                $this->productHelper()->deleteDownloadableInformation('link');
                $changedType = $initialType;
            }
        }
        $this->productHelper()->fillProductInfo($productData, $changedProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $productData['general_sku']));
        $this->assertEquals($changedType, trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $productData['general_sku']));
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
        $this->clickButton('add_new_product_split_select', false);
        $this->addParameter('productType', $productType);
        $this->clickControl('dropdown', 'add_product_by_type', false);
        $this->waitForPageToLoad($this->_browserTimeoutPeriod);
        $this->addParameter('setId', $this->defineParameterFromUrl('set'));
        $this->validatePage();
        //Verification (grouped and configurable products)
        if ($isWeightDisabled == null) {
            $this->assertFalse($this->isElementPresent('field', 'general_weight'));
            $this->assertFalse($this->isElementPresent('checkbox', 'weight_and_type_switcher'));
        }
        //Verification for Bundle product (Dynamic and Fixed)
        else {
            $this->assertEquals($isWeightDisabled, $this->isChecked(
                $this->_getControlXpath('checkbox', 'weight_and_type_switcher')));
            $this->assertFalse($this->isEditable('field', 'general_weight'));
            $this->fillDropdown('general_weight_type', 'Fixed');
            $this->assertTrue($this->isEditable('field', 'general_weight'));
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
            array('configurable', null),
            array('bundle', false),
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
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible',
            array('product_attribute_set' => $attributeSet['set_name']));
        $simpleProduct['general_user_attr']['dropdown'][$attributeData['attribute_code']] =
            $attributeData['option_1']['admin_option_name'];
        //Create attribute
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->attributeSetHelper()->openAttributeSet($attributeSet['set_name']);
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        //Create simple product for configurable product
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array(
            'attributeSet' => $attributeSet['set_name'],
            'attributeName' => $attributeData['admin_title'],
            'productSku' => $simpleProduct['general_sku']
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
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'product_attribute_set' => $data['attributeSet'],
            'configurable_attribute_title' => $data['attributeName'],
            'associated_configurable_data' => $this->loadDataSet('Product', 'associated_configurable_data',
                array('associated_search_sku' => $data['productSku'])))
        );
        //Steps and Verifying
        $this->productHelper()->selectTypeProduct($initialType);
        $this->productHelper()->changeAttributeSet($data['attributeSet']);
        if ($initialType != 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        }
        $this->assertFalse($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is selected');
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is present');
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is absent');
        $this->productHelper()->fillConfigurableSettings($configurableProduct);
        $this->productHelper()->fillProductInfo($configurableProduct, 'configurable');
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $configurableProduct['general_sku']));
        $this->assertEquals('Configurable Product', trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $configurableProduct['general_sku']));
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('pageelement', 'product_variations'),
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
        $simpleProduct =  $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->productHelper()->selectTypeProduct('configurable');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is absent');
        $this->fillCheckbox('is_configurable', 'no');
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is present');
        $this->productHelper()->fillProductInfo($simpleProduct);
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $simpleProduct['general_sku']));
        $this->assertEquals('Simple Product', trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $simpleProduct['general_sku']));
        $this->assertFalse($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is selected');
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_variations'),
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
        $simpleProduct =  $this->loadDataSet('Product', 'simple_product_visible');
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'product_attribute_set' => $data['attributeSet'],
            'configurable_attribute_title' => $data['attributeName'],
            'associated_configurable_data' => $this->loadDataSet('Product', 'associated_configurable_data',
                array('associated_search_sku' => $data['productSku'])))
        );
        //Steps
        $this->productHelper()->createProduct($configurableProduct, 'configurable', false);
        $this->openTab('general');
        $this->fillCheckbox('is_configurable', 'no');
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is present');
        $this->productHelper()->fillProductInfo($simpleProduct, 'simple');
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $simpleProduct['general_sku']));
        $this->assertEquals('Simple Product', trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $simpleProduct['general_sku']));
        $this->assertFalse($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is selected');
        $this->assertFalse($this->controlIsVisible('fieldset', 'product_variations'),
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
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'product_attribute_set' => $data['attributeSet'],
            'configurable_attribute_title' => $data['attributeName'],
            'associated_configurable_data' => $this->loadDataSet('Product', 'associated_configurable_data',
                array('associated_search_sku' => $data['productSku'])))
        );
        //Steps
        $this->productHelper()->createProduct($initialProduct, $initialType);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $initialProduct['general_sku']));
        if ($initialType != 'simple') {
            $this->fillCheckbox('weight_and_type_switcher', 'no');
        }
        $this->fillCheckbox('is_configurable', 'yes');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is absent');
        $this->productHelper()->fillConfigurableSettings($configurableProduct);
        $this->productHelper()->fillProductInfo($configurableProduct, 'configurable');
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $initialProduct['general_sku']));
        $this->assertEquals('Configurable Product', trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
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
        $configurableProduct = $this->loadDataSet('Product', 'configurable_product_visible', array(
            'product_attribute_set' => $data['attributeSet'],
            'configurable_attribute_title' => $data['attributeName'],
            'associated_configurable_data' => $this->loadDataSet('Product', 'associated_configurable_data',
                array('associated_search_sku' => $data['productSku'])))
        );
        //Steps
        $this->productHelper()->createProduct($configurableProduct, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('sku' => $configurableProduct['general_sku']));
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is absent');
        $this->productHelper()->unassignAllAssociatedProducts();
        $this->saveForm('save');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $column = $this->getColumnIdByName('Type');
        $productLocator = $this->formSearchXpath(array('sku' => $configurableProduct['general_sku']));
        $this->assertEquals('Configurable Product', trim($this->getText($productLocator . "//td[$column]")),
            'Incorrect product type has been created');
        $this->productHelper()->openProduct(array('sku' => $configurableProduct['general_sku']));
        $this->assertTrue($this->isChecked($this->_getControlXpath('checkbox', 'is_configurable')),
            'Product variation checkbox is not checked');
        $this->assertTrue($this->controlIsVisible('fieldset', 'product_variations'),
            'Product variation block is absent');
    }
}
