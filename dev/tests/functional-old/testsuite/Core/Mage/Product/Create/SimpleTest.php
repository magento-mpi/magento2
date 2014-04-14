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
     * Preconditions:
     * Navigate to Catalog -> Manage Products
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
     * Creating product with required fields only
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
     * Creating product with all fields
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
     * Creating product with existing SKU
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
        $this->productHelper()->saveProduct('continueEdit');
        //Verifying
        $newSku = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->addParameter('productSku', $newSku);
        $this->addParameter('productName', $productData['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Verifying
        $productData['general_sku'] = $newSku;
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * Creating product with empty required fields
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
        $this->productHelper()->createProduct($product, 'simple');
        //Verifying
        $this->addFieldIdToMessage($fieldType, $field);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array(array('general_name' => '%noValue%'), 'field'),
            array(array('general_sku' => ''), 'field'),
            array(array('general_price' => '%noValue%'), 'field'),
        );
    }

    /**
     * Creating product with special characters in text fields
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3423
     * @test
     */
    public function specialCharactersInTextFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
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
     * Creating product with long values in fields
     *
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3421
     * @test
     */
    public function longValuesInFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
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
     * Creating product with SKU length more than 64 characters.
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
     * Creating product with invalid weight
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
     * Creating product with invalid price
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
        $productData = $this->loadDataSet('Product', 'simple_product_required',
            array('general_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'general_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Creating product with invalid special price
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
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $productData['prices_special_price'] = $invalidValue;
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Creating product with empty tier price
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
        $this->productHelper()->createProduct($productData, 'simple');
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
     * Creating product with invalid Tier Price Data
     *
     * @param $invalidTierData
     *
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInSimple
     *
     * @TestlinkId TL-MAGE-3419
     * @test
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
     * Creating product with invalid Qty
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
        $productData = $this->loadDataSet('Product', 'simple_product_required', array('general_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->addFieldIdToMessage('field', 'general_qty');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
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
}
