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
            array(array('general_sku_type' => '-- Select --'), 'dropdown'),
            array(array('general_sku' => ''), 'field'),
            array(array('general_weight_type' => '-- Select --'), 'dropdown'),
            array(array('general_weight' => '%noValue%'), 'field'),
            array(array('general_price_type' => '-- Select --'), 'dropdown'),
            array(array('general_price' => '%noValue%'), 'field'),
        );
    }

    /**
     * <p>Creating product with special characters into required fields</p>
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3361
     */
    public function specialCharactersInBaseFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required',
            array(
                 'general_name' => $this->generate('string', 32, ':punct:'),
                 'general_sku' => $this->generate('string', 32, ':punct:')
            )
        );
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
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3358
     */
    public function longValuesInBaseFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required',
            array(
                 'general_name' => $this->generate('string', 255, ':alnum:'),
                 'general_sku' => $this->generate('string', 64, ':alnum:'),
                 'general_weight' => 99999999.9999,
            )
        );
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
     *
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
     * <p>Creating product with invalid weight</p>
     *
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
        $productData = $this->loadDataSet('Product', 'fixed_bundle_required', array('general_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid special price</p>
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
     *
     * @test
     * @depends requiredFieldsForDynamicSmoke
     * @TestlinkId TL-MAGE-3348
     */
    public function emptyBundleItemsTitle()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'dynamic_bundle_required');
        $productData['general_bundle_items']['item_1'] = $this->loadDataSet('Product', 'bundle_item_1',
            array('bundle_items_default_title' => '%noValue%', 'bundle_items_position' => '%noValue%')
        );
        //Steps
        $this->productHelper()->createProduct($productData, 'bundle');
        //Verifying
        $this->addFieldIdToMessage('field', 'bundle_items_default_title');
        $this->assertMessagePresent('success', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Bundle product with Simple product</p>
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
            array('associated_search_sku' => $simpleData['general_sku']));
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
            array('associated_search_sku' => $virtualData['general_sku']));
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
