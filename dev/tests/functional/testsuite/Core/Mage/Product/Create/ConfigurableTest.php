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
            array('General' => $attrData['attribute_code']));
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
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('configurable_attribute_title' => $attrData['admin_title']));
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
        $productData = $this->loadDataSet('Product', 'configurable_product',
            array('configurable_attribute_title' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('configurable_attribute_title'));
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
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
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
        $emptyField['configurable_attribute_title'] = $attrData['admin_title'];
        $product = $this->loadDataSet('Product', 'configurable_product_required', $emptyField);
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
            array(array('general_description' => '%noValue%'), 'field'),
            array(array('general_short_description' => '%noValue%'), 'field'),
            array(array('general_sku' => ''), 'field'),
            array(array('general_status' => '-- Please Select --'), 'dropdown'),
            array(array('general_visibility' => '-- Please Select --'), 'dropdown'),
            array(array('prices_price' => '%noValue%'), 'field'),
            array(array('prices_tax_class' => '-- Please Select --'), 'dropdown'),
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
            array('configurable_attribute_title' => $attrData['admin_title'],
                  'general_name'                 => $this->generate('string', 32, ':punct:'),
                  'general_description'          => $this->generate('string', 32, ':punct:'),
                  'general_short_description'    => $this->generate('string', 32, ':punct:'),
                  'general_sku'                  => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('configurable_attribute_title'));
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
            array('configurable_attribute_title' => $attrData['admin_title'],
                  'general_name'                 => $this->generate('string', 255, ':alnum:'),
                  'general_description'          => $this->generate('string', 255, ':alnum:'),
                  'general_short_description'    => $this->generate('string', 255, ':alnum:'),
                  'general_sku'                  => $this->generate('string', 64, ':alnum:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData, array('configurable_attribute_title'));
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
            array('configurable_attribute_title'  => $attrData['admin_title'],
                  'general_sku'                   => $this->generate('string', 65, ':alnum:')));
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
            array('configurable_attribute_title' => $attrData['admin_title'],
                  'prices_price'                 => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'configurable');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_price');
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
            array('configurable_attribute_title' => $attrData['admin_title'],
                  'prices_special_price'         => $invalidValue));
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
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('configurable_attribute_title' => $attrData['admin_title']));
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
        $productData = $this->loadDataSet('Product', 'configurable_product_required',
            array('configurable_attribute_title' => $attrData['admin_title']));
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
            array('configurable_attribute_title' => $attrData['admin_title']));
        $configurable['associated_configurable_data'] = $this->loadDataSet('Product', 'associated_configurable_data',
            array('associated_search_sku' => $simple['general_sku']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
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
        $this->productHelper()->verifyProductInfo($configurable, array('configurable_attribute_title'));
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
            array('configurable_attribute_title' => $attrData['admin_title']));
        $configurable['associated_configurable_data'] = $this->loadDataSet('Product', 'associated_configurable_data',
            array('associated_search_sku' => $virtual['general_sku']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
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
        $this->productHelper()->verifyProductInfo($configurable, array('configurable_attribute_title'));
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
        $download = $this->loadDataSet('Product', 'downloadable_product_required');
        $download['general_user_attr']['dropdown'][$attrData['attribute_code']] =
            $attrData['option_3']['admin_option_name'];
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('configurable_attribute_title' => $attrData['admin_title']));
        $configurable['associated_configurable_data'] = $this->loadDataSet('Product', 'associated_configurable_data',
            array('associated_search_sku' => $download['general_sku']));
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
        $this->productHelper()->verifyProductInfo($configurable, array('configurable_attribute_title'));
    }
}