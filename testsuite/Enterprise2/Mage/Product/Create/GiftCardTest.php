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
class Enterprise2_Mage_Product_Create_GiftCardTest extends Mage_Selenium_TestCase
{
    protected static $existingSku = '';

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
     * <p>Creating Gift Card with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @param array $giftcard_type
     * @return array $productData
     *
     * @TestlinkId TL-MAGE-8
     * @TestlinkId TL-MAGE-9
     * @TestlinkId TL-MAGE-10
     * @test
     * @dataProvider differentGiftCardTypes
     */
    public function onlyRequiredFieldsInGiftCard($giftcard_type)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required');
        $productData['giftcardinfo_card_type'] = $giftcard_type;
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        self::$existingSku = $productData;
    }

    public function differentGiftCardTypes()
    {
        return array(
            array('Virtual'),
            array('Physical'),
            array('Combined'),
        );
    }

    /**
     * <p>Creating Gift Card with all fields</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill all fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
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
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields using exist SKU;</p>
     * <p>5. Click "Save" button;</p>
     * <p>6. Verify error message;</p>
     * <p>Expected result:</p>
     * <p>Error message appears;</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5855
     * @test
     */
    public function existSkuInGiftCard()
    {
        //Steps
        $this->productHelper()->createProduct(self::$existingSku, 'giftcard');
        //Verifying
        $this->assertMessagePresent('validation', 'existing_sku');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating Gift Card with empty required fields</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Leave one required field empty and fill in the rest of fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>6. Verify error message;</p>
     * <p>7. Repeat scenario for all required fields;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @param $emptyField
     * @param $fieldType
     *
     * @dataProvider withRequiredFieldsEmptyDataProvider
     *
     * @depends onlyRequiredFieldsInGiftCard
     * @TestlinkId TL-MAGE-5856
     * @test
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        if ($emptyField == 'general_visibility') {
            $overrideData = array($emptyField => '-- Please Select --');
        } elseif ($emptyField == 'inventory_qty') {
            $overrideData = array($emptyField => '');
        } elseif ($emptyField == 'prices_gift_card_allow_open_amount') {
            $overrideData = array($emptyField => 'No', 'prices_gift_card_amounts' => '%noValue%');
        } elseif ($emptyField == 'general_weight') {
            $overrideData = array($emptyField => '', 'giftcardinfo_card_type' => 'Physical');
        } else {
            $overrideData = array($emptyField => '%noValue%');
        }
        $productData = $this->loadDataSet('Product', 'gift_card_required', $overrideData);
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        if ($emptyField == 'prices_gift_card_allow_open_amount') {
            $this->addParameter('fieldId', 'giftcard_amounts_total');
        } else {
            $this->addFieldIdToMessage($fieldType, $emptyField);
        }
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('general_name', 'field'),
            array('general_description', 'field'),
            array('general_short_description', 'field'),
            array('general_sku', 'field'),
            array('general_weight', 'field'),
            array('general_status', 'dropdown'),
            array('general_visibility', 'dropdown'),
            array('prices_gift_card_allow_open_amount', 'dropdown'),
            array('inventory_qty', 'field')
        );
    }

    /**
     * <p>Creating Gift Card with empty amounts</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Add "Amounts" fields but do not fill them, the rest fields - with normal data;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5857
     * @test
     */
    public function emptyPriceInGiftCard()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('prices_gift_card_amount' => ''));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $rowQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'prices_gift_card_amounts'));
        for ($i = 0; $i < $rowQty; $i++) {
            $this->addParameter('giftCardId', $i);
            $this->addFieldIdToMessage('field', 'prices_gift_card_amount');
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(2), $this->getParsedMessages());
    }

    /**
     * <p>Creating Gift Card with special characters into required fields</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with special symbols ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5858
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_description'       => $this->generate('string', 32, ':punct:'),
                  'general_short_description' => $this->generate('string', 32, ':punct:'),
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with long values ("General" tab), rest - with normal data;
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product created, confirmation message appears</p>
     *
     * @depends onlyRequiredFieldsInGiftCard
     *
     * @TestlinkId TL-MAGE-5859
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_description'       => $this->generate('string', 255, ':alnum:'),
                  'general_short_description' => $this->generate('string', 255, ':alnum:'),
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields, use for sku string with length more than 64 characters</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in "Weight" field with special characters, the rest - with normal data;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
        $productData = $this->loadDataSet('Product', 'gift_card_required',
            array('general_weight' => $this->generate('string', 9, ':punct:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_weight');
        $this->assertMessagePresent('validation', 'enter_valid_number');
    }

    /**
     * <p>Creating Gift Card with invalid price</p>
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in amounts fields with special characters, the rest fields - with normal data;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
            array('prices_gift_card_amount' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
        //Verifying
        $rowQty = $this->getXpathCount($this->_getControlXpath('fieldset', 'prices_gift_card_amounts'));
        for ($i = 0; $i < $rowQty; $i++) {
            $this->addParameter('giftCardId', $i);
            $this->addFieldIdToMessage('field', 'prices_gift_card_amount');
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
     * <p>Steps</p>
     * <p>1. Click "Add Product" button;</p>
     * <p>2. Fill in "Attribute Set", "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields with correct data, "Qty" field - with special characters;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is not created, error message appears;</p>
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
        $productData = $this->loadDataSet('Product', 'gift_card_required', array('inventory_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData, 'giftcard');
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

}