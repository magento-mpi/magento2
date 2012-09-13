<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Abstract class for products resource tests
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
abstract class Api2_Catalog_Product_Abstract extends Magento_Test_Webservice_Rest_Abstract
{
    /**
     * Identifier of existent default billing address for test customer for backup purposes
     *
     * @var Mage_Customer_Model_Address
     */
    protected $_customerDefaultBillingAddress;

    /**
     * Identifier of existent default shipping address for test customer for backup purposes
     *
     * @var Mage_Customer_Model_Address
     */
    protected $_customerDefaultShippingAddress;

    /**
     * Sets up the fixture.
     */
    public function setUp()
    {
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

        $this->_customerDefaultBillingAddress = $customer->getDefaultBillingAddress();
        $this->_customerDefaultShippingAddress = $customer->getDefaultShippingAddress();

        $this->_addAddressToCustomer($customer);
    }

    /**
     * Tear down specific fixtures
     */
    protected function tearDown()
    {
        $rule = $this->getFixture('catalog_price_rule');
        if ($rule) {
            $this->deleteFixture('catalog_price_rule', true);
            Mage::getModel('Mage_CatalogRule_Model_Rule')->applyAll();
        }
        if ($this->getFixture('products')) {
            foreach ($this->getFixture('products') as $product) {
                $this->addModelToDelete($product, true);
            }
        }
        if (is_object($this->_customerDefaultBillingAddress)) {
            $this->_customerDefaultBillingAddress->setIsDefaultBilling(true)->save();
        }
        if (is_object($this->_customerDefaultShippingAddress)) {
            $this->_customerDefaultShippingAddress->setIsDefaultShipping(true)->save();
        }
        parent::tearDown();
    }

    /**
     * Delete store fixture
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store_on_new_website', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);

        parent::tearDownAfterClass();
    }

    /**
     * Check if tax is applied correctly to product price. Specific tax config is applied during test
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $expectedPrices
     * @param array $config
     */
    protected function _checkTaxCalculation($product, $expectedPrices, $config)
    {
        foreach ($config as $configPath => $configValue) {
            $this->_updateAppConfig($configPath, $configValue, true, false, true);
        }

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $assertMessage = "Tested configuration: ";
        foreach ($config as $configPath => $configValue) {
            $assertMessage .= " $configPath = $configValue;";
        }

        foreach ($expectedPrices as $key => $value) {
            $this->assertTrue(isset($responseData[$key]), $key . ' not present in response.');
            $this->assertEquals($value, $responseData[$key], $assertMessage . 'key = ' .$key, 0.01);
        }
    }

    /**
     * Add valid address to customer. Address should be valid for default tax rules
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _addAddressToCustomer(Mage_Customer_Model_Customer $customer)
    {
        /** @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('Mage_Customer_Model_Address');
        $address->setData(array(
            'city' => 'New York',
            'country_id' => 'US',
            'fax' => '56-987-987',
            'firstname' => 'Jacklin',
            'lastname' => 'Sparrow',
            'middlename' => 'John',
            'postcode' => '10012',
            'region' => 'New York',
            'region_id' => '43',
            'street' => 'Main Street',
            'telephone' => '718-452-9207',
            'is_default_billing' => true,
            'is_default_shipping' => true
        ));
        $address->setCustomer($customer);
        $address->save();

        Magento_Test_Webservice::setFixture('tmp_customer_address', $address);
    }

    /**
     * Rebuild price index
     */
    protected function _reindexPrices()
    {
        /** @var $indexerPrice Mage_Catalog_Model_Resource_Product_Indexer_Price */
        $indexerPrice = Mage::getResourceModel('Mage_Catalog_Model_Resource_Product_Indexer_Price');
        $indexerPrice->reindexAll();
    }

    /**
     * Perform collection get with data check
     *
     * @param int $expectedProductsCount
     * @param array $originalData
     * @param int $expectedTierPricesCount
     * @param string $storeCode
     */
    protected function _checkProductCollectionGet($expectedProductsCount, $originalData, $expectedTierPricesCount = 0,
        $storeCode = null)
    {
        $restResponse = $this->callGet($this->_getResourcePath(null, $storeCode));
        $this->assertEquals(Mage_Api2_Controller_Front_Rest::HTTP_OK, $restResponse->getStatus());
        $resultProducts = $restResponse->getBody();
        $this->assertGreaterThanOrEqual($expectedProductsCount, count($resultProducts),
            "Not all products were found in response");

        $isFirstProductFoundInResponse = false;
        // check only first product data
        foreach ($resultProducts as $resultProductData) {
            if ($resultProductData['sku'] == $originalData['sku']) {
                $isFirstProductFoundInResponse = true;

                // check if all required fields are in response
                $requiredFields = array('type_id', 'sku', 'name', 'description', 'short_description',
                    'regular_price_with_tax', 'regular_price_without_tax', 'final_price_with_tax',
                    'final_price_without_tax', 'is_saleable');
                foreach ($requiredFields as $field) {
                    $this->assertArrayHasKey($field, $resultProductData, "'$field' field is missing in response");
                }
                $fieldsMustNotBeSet = array('is_in_stock', 'total_reviews_count', 'url', 'buy_now_url',
                    'tier_price', 'has_custom_options');
                foreach ($fieldsMustNotBeSet as $field) {
                    $this->assertArrayNotHasKey($field, $resultProductData, "'$field' field should not be in response");
                }
                $this->_checkGetImageUrl($resultProductData);
                // check attribute values
                foreach ($resultProductData as $key => $resultProductValue) {
                    if (!is_array($resultProductValue)) {
                        $this->assertEquals($originalData[$key], $resultProductValue, "'$key' is invalid");
                    }
                }
            }
        }
        $this->assertEquals(true, $isFirstProductFoundInResponse,
            "Product with sku={$originalData['sku']} was not found in response. "
                . "It could be missed because of page limit. Sorting by entity_id can't be used as this field "
                . "is inaccessible from non admin area. Try to run tests on clear DB.");
    }

    /**
     * Check if tier prices are correct
     *
     * @param int $expectedPricesCount
     * @param array $responseData
     */
    protected function _checkGetTierPrices($responseData, $expectedPricesCount)
    {
        $this->assertInternalType('array', $responseData['tier_price'], "'tier_price' expected to be an array");
        $this->assertCount($expectedPricesCount, $responseData['tier_price'], "Invalid tier prices quantity");
        $requiredFields = array('qty', 'price_with_tax', 'price_without_tax');
        foreach ($responseData['tier_price'] as $tierPrice) {
            foreach($requiredFields as $field) {
                $this->assertArrayHasKey($field, $tierPrice);
                $this->assertGreaterThanOrEqual(0, $tierPrice[$field], "Tier price seems to be invalid");
            }
        }
    }

    /**
     * Check if product URLs are correct
     *
     * @param array $productData
     */
    protected function _checkGetProductUrls(&$productData)
    {
        $this->_testUrlWithCurl($productData, 'url');
        unset($productData['url']);

        $this->assertContains('checkout/cart/add', $productData['buy_now_url'], 'Buy now url seems to be invalid');
        $this->_testUrlWithCurl($productData, 'buy_now_url', 302);
        unset($productData['buy_now_url']);
    }

    /**
     * Check if product image URL is correct
     *
     * @param array $productData
     */
    protected function _checkGetImageUrl(&$productData)
    {
        // See Mage_Core_Model_Store::_processConfigValue line 437
        // URLs are generated without /pub
        $this->assertNotEmpty($productData['image_url'], 'Image url is not set');
        $this->_testUrlWithCurl($productData, 'image_url');
        unset($productData['image_url']);
    }

    /**
     * Check if product total reviews count is correct
     *
     * @param array $productData
     */
    protected function _checkGetTotalReviewCount(&$productData)
    {
        $this->assertGreaterThanOrEqual(0, $productData['total_reviews_count']);
        unset($productData['total_reviews_count']);
    }

    /**
     * Check configurable attributes data in configurable product single GET response
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $responseData
     * @param array $fieldsMap
     */
    protected function _checkConfigurableAttributesInGet($configurable, $responseData, $fieldsMap)
    {
        $this->_checkUnnecessaryFields($configurable, $responseData);
        $this->assertArrayHasKey('configurable_attributes', $responseData, "The 'configurable_attributes' field must "
            . "be present in the single configurable product GET response.");
        $attributesDataFromResponse = $responseData['configurable_attributes'];
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        foreach ($configurableType->getConfigurableAttributesAsArray($configurable) as $attributeData) {
            $attributeCode = $attributeData['attribute_code'];
            $isAttributeFoundInResponse = false;
            foreach ($attributesDataFromResponse as $attributeDataFromResponse) {
                if ($attributeDataFromResponse['attribute_code'] == $attributeCode) {
                    // check configurable attribute data
                    foreach ($fieldsMap as $fieldInResponse => $field) {
                        $this->assertArrayHasKey($fieldInResponse, $attributeDataFromResponse,
                            "The '$fieldInResponse' field must be defined for the attribute '$attributeCode'.");
                        $this->assertEquals($attributeData[$field], $attributeDataFromResponse[$fieldInResponse],
                            "The '$fieldInResponse' field has invalid value for the attribute '$attributeCode'.");
                    }
                    // check prices for configurable options
                    $this->assertArrayHasKey('prices', $attributeDataFromResponse,
                        "The 'prices' array must be defined for the attribute '$attributeCode'.");
                    $pricesInResponse = $attributeDataFromResponse['prices'];
                    $this->assertInternalType('array', $pricesInResponse,
                        "The 'prices' array must be defined for the attribute '$attributeCode'.");
                    $this->_checkOptionPrices($attributeData['values'], $pricesInResponse, $configurable,
                        $attributeCode);
                    $isAttributeFoundInResponse = true;
                    break;
                }
            }
            $this->assertTrue($isAttributeFoundInResponse, "The information about configurable attribute with code "
                . "'$attributeCode' not found in the 'configurable_attributes' array.");
        }
    }

    /**
     * Check if configurable option prices in the response are correct
     *
     * @param array $prices
     * @param array $pricesInResponse
     * @param Mage_Catalog_Model_Product $configurable
     * @param string $attributeCode
     */
    protected function _checkOptionPrices($prices, $pricesInResponse, $configurable, $attributeCode)
    {
        foreach ($prices as $price) {
            $isPriceValueFoundInResponse = false;
            $optionValue = $price['value_index'];
            foreach ($pricesInResponse as $priceInResponse) {
                if ($priceInResponse['option_value'] == $optionValue) {
                    $requiredFields = array('option_label', 'regular_price_with_tax',
                        'regular_price_without_tax', 'final_price_with_tax','final_price_without_tax');
                    foreach ($requiredFields as $requiredField) {
                        $this->assertArrayHasKey($requiredField, $priceInResponse,
                            "The '$requiredField' field must be defined for the '$optionValue' option "
                                . "related to the '$attributeCode' attribute.");
                    }
                    $this->assertEquals($price['label'], $priceInResponse['option_label'],
                        "The 'option_label' field has invalid value for the '$optionValue' option "
                            . "related to the '$attributeCode' attribute.");
                    $pricePrecisionDelta = 0.01;
                    $this->assertEquals($this->_getOptionPrice($configurable, $price, true, false),
                        $priceInResponse['regular_price_with_tax'],
                        "The 'regular_price_with_tax' field has invalid value for the '$optionValue' option "
                            . "related to the '$attributeCode' attribute.", $pricePrecisionDelta);
                    $this->assertEquals($this->_getOptionPrice($configurable, $price, false, false),
                        $priceInResponse['regular_price_without_tax'], "The 'regular_price_without_tax' "
                            . "field has invalid value for the '$optionValue' option related "
                            . "to the '$attributeCode' attribute.", $pricePrecisionDelta);
                    $this->assertEquals($this->_getOptionPrice($configurable, $price, true, true),
                        $priceInResponse['final_price_with_tax'],
                        "The 'final_price_with_tax' field has invalid value for the '$optionValue' option "
                            . "related to the '$attributeCode' attribute.", $pricePrecisionDelta);
                    $this->assertEquals($this->_getOptionPrice($configurable, $price, false, true),
                        $priceInResponse['final_price_without_tax'], "The 'final_price_without_tax' "
                            . "field has invalid value for the '$optionValue' option related "
                            . "to the '$attributeCode' attribute.", $pricePrecisionDelta);
                    $isPriceValueFoundInResponse = true;
                    break;
                }
            }
            $this->assertTrue($isPriceValueFoundInResponse, "The information about '$optionValue' option "
                . "for the configurable attribute with code "
                . "'$attributeCode' not found in the 'configurable_attributes' array.");
        }
    }

    /**
     * Calculate configurable option price
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $optionPriceData
     * @param bool $withTax
     * @param bool $isFinal
     * @return float
     * @throws DomainException
     * @throws LogicException
     */
    protected function _getOptionPrice(Mage_Catalog_Model_Product $product, $optionPriceData, $withTax, $isFinal)
    {
        /** @var $rule Mage_CatalogRule_Model_Rule */
        $rule = $this->getFixture('catalog_price_rule');
        $this->assertNotEmpty($rule->getId(), "Catalog price fixture is invalid.");
        $this->assertEquals('by_fixed', $rule->getSimpleAction(), "Catalog price fixture is invalid.");
        $productPrice = $isFinal ? $product->getPrice() - $rule->getDiscountAmount() : $product->getPrice();
        // calculate option price
        $priceType = $optionPriceData['is_percent'] ? 'percent' : 'fixed';
        switch ($priceType) {
            case 'percent':
                $optionPrice = $productPrice * ($optionPriceData['pricing_value'] / 100);
                break;
            case 'fixed':
                $optionPrice = $optionPriceData['pricing_value'];
                break;
            default:
                throw new DomainException("Invalid price type.");
                break;
        }
        // apply catalog price rules to the option price
        if ($isFinal) {
            $this->assertEquals('by_fixed', $rule->getSubSimpleAction(), "Catalog price fixture is invalid.");
            $optionPrice -= $rule->getSubDiscountAmount();
        }
        // apply taxes
        $taxCalculationBasedOn = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON);
        if ($taxCalculationBasedOn != 'origin') {
            throw new LogicException("Tax calculation 'based on' option must be set to 'origin' during this test.");
        }
        $defaultTax = 0.0825;
        $priceIncludesTax = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX);
        if ($withTax) {
            $optionPrice = $priceIncludesTax ? $optionPrice : $optionPrice * (1 + $defaultTax);
        } else {
            $optionPrice = $priceIncludesTax ? $optionPrice / (1 + $defaultTax) : $optionPrice;
        }
        return $optionPrice;
    }

    /**
     * Make sure that unnecessary fields are not present in the response result for the configurable product.
     *
     * @param Mage_Catalog_Model_Product $configurable
     * @param array $responseData
     */
    protected function _checkUnnecessaryFields(Mage_Catalog_Model_Product $configurable, $responseData)
    {
        $this->assertArrayNotHasKey('weight', $responseData, "The 'weight' field must not be returned in "
            . "the configurable product GET response.");
        /** @var $configurableType Mage_Catalog_Model_Product_Type_Configurable */
        $configurableType = $configurable->getTypeInstance();
        foreach ($configurableType->getConfigurableAttributesAsArray($configurable) as $attributeData) {
            $attributeCode = $attributeData['attribute_code'];
            $this->assertArrayNotHasKey($attributeCode, $responseData,
                "The '$attributeCode' field must not be returned for the configurable product based on this field.");
        }
    }

    /**
     * Check if url is accessible with cURL
     *
     * @param array $responseData
     * @param string $urlField
     * @param int $expectedResponseCode
     */
    protected function _testUrlWithCurl($responseData, $urlField, $expectedResponseCode = 200)
    {
        $channel = curl_init();
        curl_setopt($channel, CURLOPT_URL, $responseData[$urlField]);
        curl_setopt($channel, CURLOPT_NOBODY, true);
        curl_exec($channel);
        $responseCode = curl_getinfo($channel, CURLINFO_HTTP_CODE);
        $this->assertEquals($expectedResponseCode, $responseCode, "'$urlField' is not accessible with cURL");
    }

    /**
     * Create path to resource
     *
     * @param string $id
     * @param string $storeId
     * @return string
     */
    protected function _getResourcePath($id = null, $storeId = null)
    {
        $path = "products";
        if (!is_null($id)) {
            $path .= "/$id";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }

    /**
     * Load simple product fixture data
     *
     * @param string $fixtureName
     * @return array
     */
    protected function _loadSimpleProductFixtureData($fixtureName)
    {
        return require TEST_FIXTURE_DIR . "/_data/Catalog/Product/Simple/{$fixtureName}.php";
    }
}
