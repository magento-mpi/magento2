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
 * Gift Card product creation tests
 *
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @license     {license_link}
 */
class Enterprise_Mage_Product_Create_GiftCardTest extends Mage_Selenium_TestCase
{
    protected static $_existingSku = '';

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
     * Creating Gift Card with required fields only
     *
     * @param array $giftcardType
     * @param array $weight
     * @return array $productData
     *
     * @TestlinkId TL-MAGE-8
     * @TestlinkId TL-MAGE-9
     * @TestlinkId TL-MAGE-10
     * @test
     * @dataProvider differentGiftCardTypes
     */
    public function onlyRequiredFieldsInGiftCard($giftcardType, $weight)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required');
        $productData['general_giftcard_data']['general_card_type'] = $giftcardType;
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard', false);
        if (!$giftcardType == 'Virtual') {
            $this->openTab('general');
            $this->fillField('general_weight', $weight);
        }
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        self::$_existingSku = $productData;
    }

    public function differentGiftCardTypes()
    {
        return array(
            array('Virtual', null),
            array('Physical', '0.15'),
            array('Combined', '0.09')
        );
    }

    /**
     * Creating Gift Card with all fields
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5854
     * @test
     */
    public function allFieldsInGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card');
        $productSearch = $this->loadDataSet('Product', 'product_search',
            array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating Gift Card with existing SKU</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5855
     * @test
     */
    public function existSkuInGiftCard()
    {
        //Steps
        $this->productHelper()->createProduct(self::$_existingSku, 'giftcard', false);
        $this->addParameter('elementTitle', self::$_existingSku['general_name']);
        $this->productHelper()->saveProduct('continueEdit');
        //Verifying
        $newSku = $this->productHelper()->getGeneratedSku(self::$_existingSku['general_sku']);
        $this->addParameter('productSku', $newSku);
        $this->addParameter('productName', self::$_existingSku['general_name']);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->assertMessagePresent('success', 'sku_autoincremented');
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $newSku;
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating Gift Card with empty required fields</p>
     *
     * @param $emptyField
     *
     * @dataProvider withRequiredFieldsEmptyDataProvider
     *
     * @depends onlyRequiredFieldsInGiftCard
     * @TestlinkId TL-MAGE-5856
     * @test
     */
    public function withRequiredFieldsEmpty($emptyField)
    {
        //Data
        $overrideData = array($emptyField => '');
        $productData = $this->loadDataSet('Product', 'gift_card_required', $overrideData);
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('general_name'),
            array('general_sku'),
        );
    }

    /**
     * <p>Creating Gift Card with empty amounts</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5857
     * @test
     */
    public function emptyPriceInGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required');
        $productData['general_giftcard_data']['general_amounts']['general_amount1']['general_giftcard_amount'] = '';
        $productData['general_giftcard_data']['general_amounts']['general_amount2']['general_giftcard_amount'] = '';
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $rowQty = $this->getControlCount('pageelement', 'general_giftcard_amount_line');
        for ($i = 0; $i < $rowQty; $i++) {
            $this->addParameter('giftCardId', $i);
            $this->addFieldIdToMessage('field', 'general_giftcard_amount');
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * <p>Creating Gift Card with special characters into required fields</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5858
     * @test
     */
    public function specialCharactersInBaseFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating Gift Card with long values into required fields</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5859
     * @test
     */
    public function longValuesInBaseFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:'),
                  'general_weight'            => 99999999.9999));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($productSearch);
        //Verifying
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Creating Gift Card with SKU length more than 64 characters.</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5860
     * @test
     */
    public function incorrectSkuLengthInGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Gift Card with invalid weight</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5861
     * @test
     *
     */
    public function invalidWeightInGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required');
        $productData['general_giftcard_data']['general_card_type'] = 'Combined';
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard', false);
        $this->openTab('general');
        $this->fillField('general_weight', $this->generate('string', 9, ':punct:'));
        $this->productHelper()->saveProduct();
        //Verifying
        $this->addParameter('fieldId', 'weight');
        $this->addFieldIdToMessage('field', 'general_weight');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    /**
     * <p>Creating Gift Card with invalid price</p>
     *
     * @param $invalidPrice
     *
     * @dataProvider invalidNumericFieldDataProvider
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5862
     * @test
     */
    public function invalidPriceInGiftCard($invalidPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_giftcard_amount' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $rowQty = $this->getControlCount('pageelement', 'general_giftcard_amount_line');
        for ($i = 0; $i < $rowQty; $i++) {
            $this->addParameter('giftCardId', $i);
            $this->addFieldIdToMessage('field', 'general_giftcard_amount');
            $this->assertMessagePresent('validation', 'enter_zero_or_greater');
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
     * <p>Creating Gift Card with invalid Qty</p>
     *
     * @param $invalidQty
     *
     * @dataProvider invalidQtyDataProvider
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5863
     * @test
     */
    public function invalidQtyInGiftCard($invalidQty)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required', array('general_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_qty');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    public function invalidQtyDataProvider()
    {
        return array(
            array($this->generate('string', 9, ':punct:')),
            array($this->generate('string', 9, ':alpha:')),
            array('g3648GJTest'),
        );
    }
}
