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
 * Downloadable product creation tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Create_DownloadableTest extends Mage_Selenium_TestCase
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
     * @test
     * @return array
     * @TestlinkId TL-MAGE-3398
     */
    public function requiredFieldsInDownloadable()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData;
    }

    /**
     * <p>Creating product with all fields</p>
     *
     * @depends requiredFieldsInDownloadable
     *
     * @TestlinkId TL-MAGE-3385
     * @test
     */
    public function allFieldsInDownloadable()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
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
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3390
     */
    public function existSkuInDownloadable($productData)
    {
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable', false);
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
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3482
     */
    public function withRequiredFieldsEmpty($emptyField, $fieldType)
    {
        //Data
        $field = key($emptyField);
        $product = $this->loadDataSet('Product', 'downloadable_product_required', $emptyField);
        //Steps
        $this->productHelper()->createProduct($product, 'downloadable');
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
     * <p>Creating product with special characters into required fields</p>
     *
     * @depends requiredFieldsInDownloadable
     *
     * @TestlinkId TL-MAGE-3399
     * @test
     */
    public function specialCharactersInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required',
            array('general_name'              => $this->generate('string', 32, ':punct:'),
                  'general_sku'               => $this->generate('string', 32, ':punct:')));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
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
     * @depends requiredFieldsInDownloadable
     *
     * @TestlinkId TL-MAGE-3397
     * @test
     */
    public function longValuesInRequiredFields()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required',
            array('general_name'              => $this->generate('string', 255, ':alnum:'),
                  'general_sku'               => $this->generate('string', 64, ':alnum:'),));
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
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
     * @depends requiredFieldsInDownloadable
     *
     * @TestlinkId TL-MAGE-3391
     * @test
     */
    public function incorrectSkuLengthInDownloadable()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required',
            array('general_sku' => $this->generate('string', 65, ':alnum:')));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->assertMessagePresent('validation', 'incorrect_sku_length');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid price</p>
     *
     * @param string $invalidPrice
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3393
     */
    public function invalidPriceInDownloadable($invalidPrice)
    {
        //Data
        $productData =
            $this->loadDataSet('Product', 'downloadable_product_required', array('general_price' => $invalidPrice));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', 'general_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with invalid special price</p>
     *
     * @param string $invalidValue
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3395
     */
    public function invalidSpecialPriceInDownloadable($invalidValue)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required',
            array('prices_special_price' => $invalidValue));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', 'prices_special_price');
        $this->assertMessagePresent('validation', 'enter_zero_or_greater');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    /**
     * <p>Creating product with empty tier price</p>
     *
     * @param string $emptyTierPrice
     *
     * @test
     * @dataProvider emptyTierPriceFieldsDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3389
     */
    public function emptyTierPriceFields($emptyTierPrice)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        $productData['prices_tier_price_data'][] =
            $this->loadDataSet('Product', 'prices_tier_price_1', array($emptyTierPrice => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyTierPrice);
        $this->assertMessagePresent('validation', 'empty_required_field');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyTierPriceFieldsDataProvider()
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
     *
     * @test
     * @dataProvider invalidNumericFieldDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3396
     */
    public function invalidTierPriceInDownloadable($invalidTierData)
    {
        //Data
        $tierData = array('prices_tier_price_qty'   => $invalidTierData,
                          'prices_tier_price_price' => $invalidTierData);
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        $productData['prices_tier_price_data'][] = $this->loadDataSet('Product', 'prices_tier_price_1', $tierData);
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
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
     * @param string $invalidQty
     *
     * @test
     * @dataProvider invalidQtyDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3394
     */
    public function invalidQtyInDownloadable($invalidQty)
    {
        //Data
        $productData =
            $this->loadDataSet('Product', 'downloadable_product_required', array('general_qty' => $invalidQty));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
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

    /**
     * <p>Creating product with empty fields - Samples</p>
     *
     * @param string $emptyField
     *
     * @test
     * @dataProvider emptyFieldForSamplesDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3387
     */
    public function emptyFieldForSamples($emptyField)
    {
        if ($emptyField == 'downloadable_sample_row_url') {
            $this->markTestIncomplete('MAGETWO-6990');
        }
        // Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        $productData['downloadable_information_data']['downloadable_sample_1'] =
            $this->loadDataSet('Product', 'downloadable_samples', array($emptyField => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        if ($emptyField == 'downloadable_sample_row_title') {
            $this->assertMessagePresent('validation', 'empty_required_field');
        } else {
            $this->assertMessagePresent('validation', 'specify_url');
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyFieldForSamplesDataProvider()
    {
        return array(
            array('downloadable_sample_row_title'),
            array('downloadable_sample_row_url')
        );
    }

    /**
     * <p>Creating product with empty fields - Links</p>
     *
     *
     * @param string $emptyField
     *
     * @test
     * @depends requiredFieldsInDownloadable
     * @dataProvider emptyFieldForLinksDataProvider
     * @TestlinkId TL-MAGE-3386
     */
    public function emptyFieldForLinks($emptyField)
    {
        if ($emptyField == 'downloadable_link_row_file_url') {
            $this->markTestIncomplete('MAGETWO-6990');
        }
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        $productData['downloadable_information_data']['downloadable_link_1'] =
            $this->loadDataSet('Product', 'downloadable_links', array($emptyField => '%noValue%'));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', $emptyField);
        if ($emptyField == 'downloadable_link_row_title') {
            $this->assertMessagePresent('validation', 'empty_required_field');
        } else {
            $this->assertMessagePresent('validation', 'specify_url');
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
    }

    public function emptyFieldForLinksDataProvider()
    {
        return array(
            array('downloadable_link_row_title'),
            array('downloadable_link_row_file_url')
        );
    }

    /**
     * <p>Creating product with invalid price for Links</p>
     *
     * @param string $invalidValue
     *
     * @test
     * @dataProvider invalidQtyDataProvider
     * @depends requiredFieldsInDownloadable
     * @TestlinkId TL-MAGE-3392
     */
    public function invalidLinksPriceInDownloadable($invalidValue)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'downloadable_product_required');
        $productData['downloadable_information_data']['downloadable_link_1'] =
            $this->loadDataSet('Product', 'downloadable_links', array('downloadable_link_row_price' => $invalidValue));
        //Steps
        $this->productHelper()->createProduct($productData, 'downloadable');
        //Verifying
        $this->addFieldIdToMessage('field', 'downloadable_link_row_price');
        $this->assertMessagePresent('validation', 'enter_valid_number');
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());
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
