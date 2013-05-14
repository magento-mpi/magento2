<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configurable product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_ConfigurableTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System -> Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    /**
     * Test Realizing precondition for creating configurable product.
     *
     * @return array
     * @test
     */
    public function createConfigurableAttribute()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => $attrData['attribute_code']));
        //Steps
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attrData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttributes);
        $this->saveForm('save_attribute_set');
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attrData;
    }

    /**
     * <p>Preconditions for creating configurable product based on attribute from another attribute set.</p>
     * <p>Create 2 dropdown attributes with 3 options, Global scope and assign it to created Attribute Set</p>
     *
     * @return array
     * @test
     */
    public function createConfigurableAttributeOutOfSet()
    {
        //Data
        $attribute1 = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attribute2 = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $attribute3 = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttribute = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('Product Details' => array($attribute3['attribute_code'])));
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set',
            array('Product Details' => array($attribute1['attribute_code'], $attribute2['attribute_code'])));
        //Steps (attributes)
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()->createAttribute($attribute1);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attribute2);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()->createAttribute($attribute3);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps (attribute set)
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        $this->assertMessagePresent('success', 'success_attribute_set_saved');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array(
            'newSet' => array(
                'attributeSet' => $attributeSet['set_name'],
                'attribute1' => $attribute1,
                'attribute2' => $attribute2
            ),
            'default' => array(
                'attribute1' => $attribute3
            )
        );
    }

    /**
     * <p>Creating product with required fields only</p>
     *
     * @param array $attrData
     *
     * @return array
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3374
     */
    public function onlyRequiredFieldsInConfigurable($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData;
    }

    /**
     * <p>Creating product with all fields</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3362
     */
    public function allFieldsInConfigurable($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with existing SKU</p>
     *
     * @param array $productData
     *
     * @test
     * @depends onlyRequiredFieldsInConfigurable
     * @TestlinkId TL-MAGE-3368
     */
    public function existSkuInConfigurable($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable', false);
        $this->addParameter('elementTitle', $productData['general_name']);
        $this->productHelper()->saveProduct('continueEdit');
        //Verifying
        $newSku = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->addParameter('productSku', $newSku);
        $this->addParameter('productName', $productData['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $productData['general_sku'] = $newSku;
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with empty required fields</p>
     *
     * @param string $emptyField
     * @param string $fieldType
     * @param array $attrData
     *
     * @test
     * @dataProvider emptyRequiredFieldInConfigurableDataProvider
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3366
     */
    public function emptyRequiredFieldInConfigurable($emptyField, $fieldType, $attrData)
    {
        //Data
        $field = key($emptyField);
        $product = $this->loadDataSet('Product', 'configurable_product_required', $emptyField,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        //Steps
        $this->productHelper()->createProduct($product, 'configurable');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyRequiredFieldInConfigurableDataProvider()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_sku' => ''), 'field'),
            array(array('general_price' => '%noValue%'), 'field'),
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3375
     */
    public function specialCharactersInRequiredFields($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with long values from required fields</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3373
     */
    public function longValuesInRequiredFields($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:')),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('product_attribute_set'));
    }

    /**
     * <p>Creating product with SKU length more than 64 characters.</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3369
     */
    public function incorrectSkuLengthInConfigurable($attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid price</p>
     *
     * @param string $invalidPrice
     * @param array $attrData
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3370
     */
    public function invalidPriceInConfigurable($invalidPrice, $attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('general_price' => $invalidPrice),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid special price</p>
     *
     * @param string $invalidValue
     * @param array $attrData
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3371
     */
    public function invalidSpecialPriceInConfigurable($invalidValue, $attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('prices_special_price' => $invalidValue),
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with empty tier price</p>
     *
     * @param string $emptyTierPrice
     * @param array $attrData
     *
     * @test
     * @dataProvider emptyTierPriceFieldsInConfigurableDataProvider
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3367
     */
    public function emptyTierPriceFieldsInConfigurable($emptyTierPrice, $attrData)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productData['prices_tier_price_data'][] =
            $this->loadDataSet('Product', 'prices_tier_price_1', array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyTierPrice);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyTierPriceFieldsInConfigurableDataProvider()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price'),
        );
    }

    /**
     * <p>Creating product with invalid Tier Price Data</p>
     *
     * @param string $invalidTierData
     * @param array $attrData
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3372
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function invalidTierPriceInConfigurable($invalidTierData, $attrData)
    {
        //Data
        $tierData = array('prices_tier_price_qty'   => $invalidTierData,
                          'prices_tier_price_price' => $invalidTierData);
        $productData = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('var1_attr_value1'    => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productData['prices_tier_price_data'][] = $this->loadDataSet('Product', 'prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        foreach ($tierData as $key => $value) {
            $this->addFieldIdToMessage('field', $key);
            $this->assertMessagePresent('validation', 'enter_greater_than_zero');
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    public function invalidNumericFieldDataProvider()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJTest'),
            array('-128')
        );
    }

    /**
     * <p>Creating Configurable product with Simple product</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3364
     */
    public function configurableWithSimpleProduct($attrData)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_required');
        $simple['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_1']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('associated_name' => $simple['general_name'],
                  'associated_sku' => $simple['general_sku']),
            array('var1_attr_value1' => $attrData['option_1']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simple);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($configurable);
    }

    /**
     * <p>Creating Configurable product with Virtual product</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3365
     */
    public function configurableWithVirtualProduct($attrData)
    {
        //Data
        $virtual = $this->loadDataSet('Product', 'virtual_product_required');
        $virtual['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_2']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('associated_name' => $virtual['general_name'],
                  'associated_sku' => $virtual['general_sku']),
            array('var1_attr_value1' => $attrData['option_2']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($virtual, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($configurable);
    }

    /**
     * <p>Creating Configurable product with Downloadable product</p>
     *
     * @param array $attrData
     *
     * @test
     * @depends createConfigurableAttribute
     * @TestlinkId TL-MAGE-3363
     */
    public function configurableWithDownloadableProduct($attrData)
    {
        //Data
        $download = $this->loadDataSet('Product', 'downloadable_product_required',
            array('downloadable_links_purchased_separately' => 'No'));
        $download['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_3']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('associated_name' => $download['general_name'],
                  'associated_sku' => $download['general_sku']),
            array('var1_attr_value1' => $attrData['option_3']['admin_option_name'],
                  'general_attribute_1' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($download, 'downloadable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($configurable);
    }

    /**
     * <p>Add existed attribute from current product template while editing configurable product</p>
     *
     * @param array $defaultAttribute
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttribute
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6512
     */
    public function addConfigurableAttributeWhileEditing(array $defaultAttribute, array $attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_required', null,
            array('general_attribute_1' => $defaultAttribute['admin_title'],
                  'var1_attr_value1'    => $defaultAttribute['option_1']['admin_option_name']));
        $searchConfigurable = $this->loadDataSet('Product', 'product_search',
            array('product_sku'        => $configurable['general_sku'],
                  'product_visibility' => $configurable['autosettings_visibility']));
        //Preconditions
        $this->productHelper()->createProduct($configurable, 'configurable');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('sku' => $configurable['general_sku']));
        $this->productHelper()->selectConfigurableAttribute($attributeData['default']['attribute1']['admin_title']);
        $this->clickButton('generate_product_variations', false);
        $this->waitForControlVisible(self::FIELD_TYPE_PAGEELEMENT, 'attribute_header');
        $this->productHelper()->assignAllConfigurableVariations();
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals(
            'Configurable Product',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Type'),
            'Incorrect product type has been saved.'
        );
        $this->assertEquals(
            'Default',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
    }

    /**
     * <p>Add existed external configurable attribute (affect current product template)</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6513
     */
    public function addExternalAttributeToCurrentTemplate($attributeData)
    {
        //Data
        $associated = $this->loadDataSet('Product', 'generate_virtual_associated');
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_name' => $associated['associated_name'],
                  'associated_sku' => $associated['associated_sku']),
            array('general_attribute_1' => $attributeData['newSet']['attribute1']['admin_title'],
                  'var1_attr_value1' => $attributeData['newSet']['attribute1']['option_1']['admin_option_name']));
        $searchConfigurable =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        $searchVirtual =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $associated['associated_sku']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals(
            'Configurable Product',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Type'),
            'Incorrect product type has been saved.'
        );
        $this->assertEquals(
            'Default',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
        $this->assertEquals(
            'Default',
            $this->productHelper()->getProductDataFromGrid($searchVirtual, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->
            verifyAttributeAssignment(array($attributeData['newSet']['attribute1']['attribute_code']));
    }

    /**
     * <p>Add existed external configurable attribute (save in created inline product template)</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6514
     */
    public function addExternalAttributeToNewTemplate($attributeData)
    {
        //Data
        $associated = $this->loadDataSet('Product', 'generate_virtual_associated');
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_name' => $associated['associated_name'],
                  'associated_sku' => $associated['associated_sku']),
            array('general_attribute_1' => $attributeData['newSet']['attribute2']['admin_title'],
                  'var1_attr_value1' => $attributeData['newSet']['attribute2']['option_1']['admin_option_name']));
        $searchConfigurable =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        $searchVirtual =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $associated['associated_sku']));
        $attributeSetName = $this->generate('string', 30, ':alnum:');
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->productHelper()->saveProduct('close', $attributeSetName);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals(
            'Configurable Product',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Type'),
            'Incorrect product type has been saved.'
        );
        $this->assertEquals(
            'Default',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
        $this->assertEquals(
            $attributeSetName,
            $this->productHelper()->getProductDataFromGrid($searchVirtual, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet($attributeSetName);
        $this->attributeSetHelper()->
            verifyAttributeAssignment(array($attributeData['newSet']['attribute2']['attribute_code']));
    }

    /**
     * <p>Assign associated product from another template</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6528
     */
    public function assignProductFromExternalTemplate($attributeData)
    {
        //Data
        $simpleProduct = $this->loadDataSet('Product', 'simple_product_visible',
            array('product_attribute_set' => $attributeData['newSet']['attributeSet']));
        $simpleProduct['general_user_attr']['dropdown'][$attributeData['newSet']['attribute2']['attribute_code']] =
            $attributeData['newSet']['attribute2']['option_1']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible',
            array('associated_name' => $simpleProduct['general_name'],
                  'associated_sku' => $simpleProduct['general_sku']),
            array('general_attribute_1' => $attributeData['newSet']['attribute2']['admin_title'],
                  'var1_attr_value1' => $attributeData['newSet']['attribute2']['option_1']['admin_option_name']));
        $searchConfigurable =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simpleProduct);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertEquals(
            'Configurable Product',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Type'),
            'Incorrect product type has been saved.'
        );
        $this->assertEquals(
            'Default',
            $this->productHelper()->getProductDataFromGrid($searchConfigurable, 'Attrib. Set Name'),
            'Product was saved with incorrect attribute set.'
        );
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->
            verifyAttributeAssignment(array($attributeData['newSet']['attribute2']['attribute_code']), false);
    }

    /**
     * <p>Add existed external configurable attribute (cancel assigning to product template)</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6515
     */
    public function addExternalAttributeAndCancel($attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array('general_attribute_1' => $attributeData['newSet']['attribute2']['admin_title'],
                  'var1_attr_value1'    => $attributeData['newSet']['attribute2']['option_1']['admin_option_name']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->productHelper()->saveProduct('close', null);
        $this->waitForElementEditable($this->_getControlXpath('radiobutton', 'current_attribute_set'));
        $this->clickButton('cancel');
        //Verifying
        $this->assertEquals('new_product', $this->getCurrentPage(),
            'Pressing the Cancel button is leading to page redirection');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet('Default');
        $this->attributeSetHelper()->
            verifyAttributeAssignment(array($attributeData['newSet']['attribute2']['attribute_code']), false);
    }

    /**
     * <p>Verify attribute set name while creating new attribute set on product save</p>
     *
     * @param array $attributeData
     *
     * @test
     * @depends createConfigurableAttributeOutOfSet
     * @TestlinkId TL-MAGE-6520
     */
    public function attributeSetFieldValidation($attributeData)
    {
        //Data
        $configurable = $this->loadDataSet('Product', 'configurable_product_visible', null,
            array('general_attribute_1' => $attributeData['newSet']['attribute2']['admin_title'],
                  'var1_attr_value1'    => $attributeData['newSet']['attribute2']['option_1']['admin_option_name']));
        //Steps
        $this->productHelper()->createProduct($configurable, 'configurable', false);
        $this->productHelper()->saveProduct('close', null);
        $this->fillRadiobutton('new_attribute_set', 'Yes');
        //Verifying empty attribute set name
        $this->fillField('new_attribute_set_name', '');
        $this->saveForm('confirm', false);
        $this->addFieldIdToMessage('field', 'new_attribute_set_name');
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getMessagesOnPage());
        //Verifying empty attribute set name
        $this->fillField('new_attribute_set_name', "<script>alert('XSS')</script>");
        $this->saveForm('confirm', false);
        $this->assertMessagePresent('validation', 'attribute_set_html');
        $this->assertTrue($this->verifyMessagesCount(), $this->getMessagesOnPage());
        //Verifying entering existing attribute set name
        $this->addParameter('attributeSetName', 'Default');
        $this->fillField('new_attribute_set_name', 'Default');
        $this->saveForm('confirm', false);
        $this->assertMessagePresent('error', 'attribute_set_existed');
        $this->assertTrue($this->verifyMessagesCount(), $this->getMessagesOnPage());
    }
}
