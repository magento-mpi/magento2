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
 * Simple product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_SimpleTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog -> Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
    }

    protected function tearDownAfterTest()
    {
        $this->closeLastWindow();
    }

    /**
     * <p>Creating product with required fields only</p>
     *
     * @return array $productData
     *
     * @TestlinkId TL-MAGE-3422
     * @test
     */
    public function onlyRequiredFieldsInSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData;
    }

    /**
     * <p>Creating product with all fields</p>
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3411
     * @test
     */
    public function allFieldsInSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
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
     * @param $productData
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3414
     * @test
     */
    public function existSkuInSimple($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'simple', false);
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
     * @param $emptyField
     * @param $fieldType
     *
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-12
     * @test
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        $product = $this->loadDataSet('Product', 'simple_product_required', $emptyField);
        //Steps
        $this->productHelper()->createProduct($product);
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_description' => '%noValue%'), 'field'),
            array(array('general_short_description' => '%noValue%'), 'field'),
            array(array('general_sku' => ''), 'field'),
            array(array('general_weight' => '%noValue%'), 'field'),
            array(array('general_status' => '-- Please Select --'), 'dropdown'),
            array(array('general_visibility' => '-- Please Select --'), 'dropdown'),
            array(array('prices_price' => '%noValue%'), 'field'),
            array(array('prices_tax_class' => '-- Please Select --'), 'dropdown'),
            array(array('inventory_qty' => ''), 'field')
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3423
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_description'       => $this->generate('string', 32, ':punct:'),
                  'general_short_description' => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
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
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3421
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_description'       => $this->generate('string', 255, ':alnum:'),
                  'general_short_description' => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:'),
                  'general_weight'            => 99999999.9999));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with SKU length more than 64 characters.</p>
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3415
     * @test
     */
    public function incorrectSkuLengthInSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Fails due to MAGE-5658
     * <p>Creating product with invalid weight</p>
     *
     * @TestlinkId TL-MAGE-3420
     * @test
     */
    public function invalidWeightInSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_weight' => $this->generate('string', 9, ':punct:')));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'general_weight');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    /**
     * <p>Creating product with invalid price</p>
     *
     * @param $invalidPrice
     *
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3416
     * @test
     */
    public function invalidPriceInSimple($invalidPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('prices_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid special price</p>
     *
     * @param $invalidValue
     *
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3418
     * @test
     */
    public function invalidSpecialPriceInSimple($invalidValue)
    {
        //Data
        $productData =
            $this->loadDataSet('Product', 'simple_product_required', array('prices_special_price' => $invalidValue));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with empty tier price</p>
     *
     * @param $emptyTierPrice
     *
     * @dataProvider emptyTierPriceFieldsDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3413
     * @test
     */
    public function emptyTierPriceFields($emptyTierPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['prices_tier_price_data'][] =
            $this->loadDataSet('Product', 'prices_tier_price_1', array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', $emptyTierPrice);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyTierPriceFieldsDataProvider()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price')
        );
    }

    /**
     * <p>Creating product with invalid Tier Price Data</p>
     *
     * @param $invalidTierData
     *
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3419
     * @test
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function invalidTierPriceInSimple($invalidTierData)
    {
        //Data
        $tierData = array('prices_tier_price_qty' => $invalidTierData, 'prices_tier_price_price' => $invalidTierData);
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['prices_tier_price_data'][] = $this->loadDataSet('Product', 'prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        foreach ($tierData as $key => $value) {
            $this->addFieldIdToMessage('field', $key);
            $this->assertMessagePresent('validation', 'enter_greater_than_zero');
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid Qty</p>
     *
     * @param $invalidQty
     *
     * @dataProvider invalidQtyDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3417
     * @test
     */
    public function invalidQtyInSimple($invalidQty)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('inventory_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'inventory_qty');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function invalidQtyDataProvider()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJTest'),
        );
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
     * Quick create
     *
     * depends onlyRequiredFieldsInSimple
     * @return array
     * @test
     */
    public function onConfigurableProductPageQuickCreate()
    {
        //Data
        $attrData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown_with_options');
        $associatedAttributes = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attrData['attribute_code']));
        $configurable = $this->loadDataSet('Product', 'configurable_product_required',
            array('configurable_attribute_title' => $attrData['admin_title']));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $configurable['general_sku']));
        $quickSimple = $this->loadDataSet('Product', 'quick_simple_product',
            array('quick_simple_product_attribute_value' => $attrData['option_1']['admin_option_name']));
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
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($configurable, 'configurable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        $this->addParameter('attributeCode', $attrData['attribute_code']);
        $this->fillForm($quickSimple, 'associated');
        $this->clickButton('quick_create', false);
        $this->pleaseWait();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_product');

        return array('search' => $productSearch, 'attr' => $attrData);
    }

    /**
     * Create Empty
     *
     * @param $data
     *
     * @depends onConfigurableProductPageQuickCreate
     * @test
     */
    public function onConfigurableProductPageCreateEmpty($data)
    {
        //Data
        $searchAttr = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $data['attr']['attribute_code']));
        $simpleEmpty = $this->loadDataSet('Product', 'simple_product_required');
        $simpleEmpty['general_user_attr']['dropdown'][$data['attr']['attribute_code']] =
            $data['attr']['option_2']['admin_option_name'];
        //Steps
        //1.Define attribute ID
        $attrId = $this->productAttributeHelper()->defineAttributeId($searchAttr);
        $this->addParameter('attrId', $attrId);
        //2.Define attribute set ID that used in product
        $this->navigate('manage_products');
        $setId = $this->productHelper()->defineAttributeSetUsedInProduct($data['search']);
        $this->addParameter('setId', $setId);
        //3. Open product and create simple product
        $this->productHelper()->openProduct($data['search']);
        $this->openTab('associated');
        $this->clickButton('create_empty', false);
        $this->selectLastWindow();
        $this->productHelper()->fillProductInfo($simpleEmpty);
        $this->saveForm('save', false);
        $this->window('');
        $this->waitForAjax();
        $xpath = $this->search(array('associated_search_sku' => $simpleEmpty['general_sku']), 'associated');
        $this->assertNotEquals(null, $xpath, 'Product is not found');
    }

    /**
     * Copy From Configurable
     *
     * @param $data
     *
     * @depends onConfigurableProductPageQuickCreate
     * @test
     */
    public function onConfigurableProductPageCopyFromConfigurable($data)
    {
        //Data
        $searchAttr = $this->loadDataSet('ProductAttribute', 'attribute_search_data',
            array('attribute_code' => $data['attr']['attribute_code']));
        $simple = array('general_weight' => '3.21', 'general_sku' => $this->generate('string', 15, ':alnum:'));
        $simple['general_user_attr']['dropdown'][$data['attr']['attribute_code']] =
            $data['attr']['option_3']['admin_option_name'];
        //Steps
        //1.Define attribute ID
        $attrId = $this->productAttributeHelper()->defineAttributeId($searchAttr);
        $this->addParameter('attrId', $attrId);
        //2.Define attribute set ID that used in product
        $this->navigate('manage_products');
        $setId = $this->productHelper()->defineAttributeSetUsedInProduct($data['search']);
        $this->addParameter('setId', $setId);
        //3. Open product and create simple product
        $this->productHelper()->openProduct($data['search']);
        $this->addParameter('productId', $this->getParameter('id'));
        $this->openTab('associated');
        $this->clickButton('create_copy_from_configurable', false);
        $this->selectLastWindow();
        $this->productHelper()->fillProductInfo($simple);
        $this->saveForm('save', false);
        $this->window('');
        $this->waitForAjax();
        $xpath = $this->search(array('associated_search_sku' => $simple['general_sku']), 'associated');
        $this->assertNotEquals(null, $xpath, 'Product is not found');
    }
}