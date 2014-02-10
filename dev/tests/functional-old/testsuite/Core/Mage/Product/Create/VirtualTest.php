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
 * Virtual product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_VirtualTest extends Mage_Selenium_TestCase
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

    /**
     * Creating product with required fields only
     *
     * @return array
     * @test
     * @TestlinkId TL-MAGE-5333
     */
    public function onlyRequiredFieldsInVirtual()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required');
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData;
    }

    /**
     * Creating product with all fields
     *
     * @depends onlyRequiredFieldsInVirtual
     *
     * @TestlinkId TL-MAGE-5334
     * @test
     */
    public function allFieldsInVirtual()
    {
        //Data
        $product = $this->loadDataSet('Product', 'virtual_product');
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $product['general_sku']));
        //Steps
        $this->productHelper()->createProduct($product, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        //Verifying
        $this->productHelper()->verifyProductInfo($product);
    }

    /**
     * Creating product with existing SKU
     *
     * @param array $productData
     *
     * @test
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5336
     */
    public function existSkuInVirtual($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual', false);
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
     * Creating product with empty required fields
     *
     * @param string $emptyField
     * @param string $fieldType
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5337
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        $product = $this->loadDataSet('Product', 'virtual_product_required', $emptyField);
        //Steps
        $this->productHelper()->createProduct($product, 'virtual');
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
     * Creating product with special characters into required fields
     *
     * @depends onlyRequiredFieldsInVirtual
     *
     * @TestlinkId TL-MAGE-5338
     * @test
     */
    public function specialCharactersInTextFields()
    {
        //Data
        $product = $this->loadDataSet('Product', 'virtual_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $search = $this->loadDataSet('Product', 'product_search', array('product_sku' => $product['general_sku']));
        //Steps
        $this->productHelper()->createProduct($product, 'virtual');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct($search);
        //Verifying
        $this->productHelper()->verifyProductInfo($product);
    }

    /**
     * Creating product with long values from required fields
     *
     * @depends onlyRequiredFieldsInVirtual
     * @test
     */
    public function longValuesInTextFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
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
     * @depends onlyRequiredFieldsInVirtual
     *
     * @TestlinkId TL-MAGE-5340
     * @test
     */
    public function incorrectSkuLengthInVirtual()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Creating product with invalid price
     *
     * @param string $invalidPrice
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInVirtual
     */
    public function invalidPriceInVirtual($invalidPrice)
    {
        //Data
        $productData =
            $this->loadDataSet('Product', 'virtual_product_required', array('general_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Creating product with invalid special price
     *
     * @param string $invalidValue
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5342
     */
    public function invalidSpecialPriceInVirtual($invalidValue)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required');
        $productData['prices_special_price'] = $invalidValue;
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * Creating product with empty tier price
     *
     * @param string $emptyTierPrice
     *
     * @test
     * @dataProvider emptyTierPriceFieldsDataProvider
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5343
     */
    public function emptyTierPriceFields($emptyTierPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required');
        $productData['prices_tier_price_data'][] =
            $this->loadDataSet('Product', 'prices_tier_price_1', array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
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
     * @param string $invalidTierData
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5344
     */
    public function invalidTierPriceInVirtual($invalidTierData)
    {
        //Data
        $tierData = array('prices_tier_price_qty'   => $invalidTierData,
                          'prices_tier_price_price' => $invalidTierData);
        $productData = $this->loadDataSet('Product', 'virtual_product_required');
        $productData['prices_tier_price_data'][] = $this->loadDataSet('Product', 'prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
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
     * @param string $invalidQty
     *
     * @test
     * @dataProvider invalidQtyDataProvider
     * @depends onlyRequiredFieldsInVirtual
     * @TestlinkId TL-MAGE-5345
     */
    public function invalidQtyInVirtual($invalidQty)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'virtual_product_required', array('general_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData, 'virtual');
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
