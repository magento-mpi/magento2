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
 * Bundle Dynamic product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_BundleTest extends Mage_Selenium_TestCase
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

    /**
     * <p>Creating product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @return array $productData
     * @test
     * @TestlinkId TL-MAGE-3359
     */
    public function requiredFieldsForDynamicSmoke()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Creating product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     * @TestlinkId TL-MAGE-3360
     */
    public function requiredFieldsForFixedSmoke()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required');
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Creating product with all fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3344
     */
    public function allFieldsForDynamic()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with all fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3345
     */
    public function allFieldsForFixed()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with existing SKU</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields using exist SKU;</p>
     * <p>5. Click 'Save and Continue Edit' button;</p>
     * <p>Expected result:</p>
     * <p>1. Product is saved, confirmation message appears;</p>
     * <p>2. Auto-increment is added to SKU;</p>
     *
     * @param $productData
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3351
     */
    public function existSkuInBundle($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle', false);
        $this->addParameter('productName', $productData['general_name']);
        $this->saveAndContinueEdit('button', 'save_and_continue_edit');
        //Verifying
        $this->addParameter('productSku',  $this->productHelper()->getGeneratedSku($productData['general_sku']));
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $this->productHelper()->verifyProductInfo(array('general_sku' => $this->productHelper()->getGeneratedSku(
            $productData['general_sku'])));
    }

    /**
     * <p>Creating product with empty required fields</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Leave one required field empty and fill in the rest of fields;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>6. Verify error message;</p>
     * <p>7. Repeat scenario for all required fields for both tabs;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $emptyField
     * @param $fieldType
     *
     * @test
     * @dataProvider emptyRequiredFieldInBundleDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3349
     */
    public function emptyRequiredFieldInBundle($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required', $emptyField);
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyRequiredFieldInBundleDataProvider()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_description' => '%noValue%'), 'field'),
            array(array('general_short_description' => '%noValue%'), 'field'),
            array(array('general_sku_type' => '-- Select --'), 'dropdown'),
            array(array('general_sku' => ' '), 'field'),
            array(array('general_weight_type' => '-- Select --'), 'dropdown'),
            array(array('general_weight' => '%noValue%'), 'field'),
            array(array('general_status' => '%noValue%'), 'dropdown'),
            array(array('general_visibility' => '-- Please Select --'), 'dropdown'),
            array(array('prices_price_type' => '-- Select --'), 'dropdown'),
            array(array('prices_price' => '%noValue%'), 'field'),
            array(array('prices_tax_class' => '-- Please Select --'), 'dropdown')
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with special symbols ('General' tab), rest - with normal data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3361
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_description'       => $this->generate('string', 32, ':punct:'),
                  'general_short_description' => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with long values from required fields</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with long values ('General' tab), rest - with normal data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3358
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_description'       => $this->generate('string', 255, ':alnum:'),
                  'general_short_description' => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:'),
                  'general_weight'            => 99999999.9999));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating product with SKU length more than 64 characters.</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields, use for sku string with length more than 64 characters</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3352
     */
    public function incorrectSkuLengthInBundle()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Fails due to MAGE-5658
     * <p>Creating product with invalid weight</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in 'Weight' field with special characters, the rest - with normal data;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears, Weight=0;</p>
     * @test
     * @TestlinkId TL-MAGE-3357
     */
    public function invalidWeightInBundle()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required',
            array('general_weight' => $this->generate('string', 9, ':punct:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_weight');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    /**
     * <p>Creating product with invalid price</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in 'Price' field with special characters, the rest fields - with normal data;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $invalidPrice
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3354
     */
    public function invalidPriceInBundle($invalidPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required', array('prices_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid special price</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in field 'Special Price' with invalid data, the rest fields - with correct data;
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:<p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $invalidValue
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3355
     */
    public function invalidSpecialPriceInBundle($invalidValue)
    {
        //Data
        $productData =
            $this->loadDataSet('Product', 'dynamic_bundle_required', array('prices_special_price' => $invalidValue));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with empty tier price</p>
     * <p>Steps<p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click 'Add Tier' button and leave fields in current fieldset empty;</p>
     * <p>6. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $emptyTierPrice
     *
     * @test
     * @dataProvider emptyTierPriceFieldsInBundleDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3350
     */
    public function emptyTierPriceFieldsInBundle($emptyTierPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        $productData['prices_tier_price_data'][] =
            $this->loadDataSet('Product', 'prices_tier_price_1', array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyTierPrice);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyTierPriceFieldsInBundleDataProvider()
    {
        return array(
            array('prices_tier_price_qty'),
            array('prices_tier_price_price'),
        );
    }

    /**
     * <p>Creating product with invalid Tier Price Data</p>
     * <p>Steps</p>
     * <p>1. Click 'Add Product' button;</p>
     * <p>2. Fill in 'Attribute Set', 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields with correct data;</p>
     * <p>5. Click 'Add Tier' button and fill in fields in current fieldset with incorrect data;</p>
     * <p>6. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $invalidTierData
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3356
     */
    public function invalidTierPriceInBundle($invalidTierData)
    {
        //Data
        $tierData = array('prices_tier_price_qty'   => $invalidTierData,
                          'prices_tier_price_price' => $invalidTierData);
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        $productData['prices_tier_price_data'][] = $this->loadDataSet('Product', 'prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        foreach ($tierData as $key => $value) {
            $this->addFieldIdToMessage('field', $key);
            $this->assertMessagePresent('validation', 'enter_greater_than_zero');
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with empty Bundle Items Default Title</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Add Bundle Items Option;</p>
     * <p>6. Leave Default Title field empty and fill in the rest of fields;</p>
     * <p>7. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3348
     */
    public function emptyBundleItemsTitle()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        $productData['bundle_items_data']['item_1'] =
            $this->loadDataSet('Product', 'bundle_item_1', array('bundle_items_default_title' => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'bundle_items_default_title');
        $this->assertMessagePresent('success', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with Bundle Items invalid "Position"</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Add Bundle Items Option;</p>
     * <p>6. Enter invalid data into "Position" field and fill in the rest of fields;</p>
     * <p>7. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $invalidPosition
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3353
     */
    public function invalidPositionForBundleItems($invalidPosition)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        $productData['bundle_items_data']['item_1'] =
            $this->loadDataSet('Product', 'bundle_item_1', array('bundle_items_position' => $invalidPosition));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'bundle_items_position');
        $this->assertMessagePresent('success', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Bundle product with Simple product</p>
     * <p>Preconditions</p>
     * <p>Physical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Simple product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @param $dataBundleType
     *
     * @test
     * @dataProvider bundleTypeDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3346
     */
    public function bundleWithSimpleProduct($dataBundleType)
    {
        //Data
        $simpleData = $this->loadDataSet('Product', 'simple_product_required');
        $option = $this->loadDataSet('Product', 'bundle_item_2',
            array('bundle_items_search_sku' => $simpleData['general_sku']));
        $bundleData = $this->loadDataSet('Product', $dataBundleType, array('item_1'=> $option));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $bundleData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($simpleData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($bundleData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($bundleData);
    }

    /**
     * <p>Creating Bundle product with Virtual product</p>
     * <p>Preconditions</p>
     * <p>Physical Simple product created</p>
     * <p>Steps:</p>
     * <p>1. Click 'Add product' button;</p>
     * <p>2. Fill in 'Attribute Set' and 'Product Type' fields;</p>
     * <p>3. Click 'Continue' button;</p>
     * <p>4. Fill in all fields;</p>
     * <p>5. Goto "Associated products" tab;</p>
     * <p>6. Select created Virtual product;</p>
     * <p>5. Click 'Save' button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @param $dataBundleType
     *
     * @test
     * @dataProvider bundleTypeDataProvider
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3347
     */
    public function bundleWithVirtualProduct($dataBundleType)
    {
        //Data
        $virtualData = $this->loadDataSet('Product', 'virtual_product_required');
        $option = $this->loadDataSet('Product', 'bundle_item_2',
            array('bundle_items_search_sku' => $virtualData['general_sku']));
        $bundleData = $this->loadDataSet('Product', $dataBundleType, array('item_1'=> $option));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $bundleData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($virtualData, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->createProduct($bundleData, 'bundle');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($bundleData);
    }

    public function bundleTypeDataProvider()
    {
        return array(
            array('fixed_bundle_required'),
            array('dynamic_bundle_required')
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
}
