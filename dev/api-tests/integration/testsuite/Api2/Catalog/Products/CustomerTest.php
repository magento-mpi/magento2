<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test product resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_CustomerTest extends Magento_Test_Webservice_Rest_Customer
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
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

        $this->_customerDefaultBillingAddress = $customer->getDefaultBillingAddress();
        $this->_customerDefaultShippingAddress = $customer->getDefaultShippingAddress();

        $this->_addAddressToCustomer($customer);
    }

    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $this->deleteFixture('product_simple_taxes', true);
        if ($this->getFixture('products')) {
            foreach ($this->getFixture('products') as $product) {
                $this->addModelToDelete($product, true);
            }
        }
        if (self::getFixture('tmp_customer_address')) {
            self::deleteFixture('tmp_customer_address', true);
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
     * Test successful product get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testGet()
    {
        self::deleteFixture('tmp_customer_address', true);
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $product->getData();

        // check if all required fields available in response
        $requiredFields = array('type_id', 'sku', 'name', 'description', 'short_description',
            'regular_price_with_tax', 'regular_price_without_tax', 'final_price_with_tax', 'final_price_without_tax',
            'tier_price', 'image_url', 'is_in_stock', 'is_saleable', 'total_reviews_count', 'url', 'buy_now_url',
            'has_custom_options');
        foreach($requiredFields as $field) {
            $this->assertArrayHasKey($field, $responseData, "'$field' field is missing in response");
        }

        $this->_checkGetProductUrls($responseData, $product->getId());
        $this->_checkGetImageUrl($responseData, $product->getId());
        $this->_checkGetTierPrices($responseData, 2);
        $this->_checkGetTotalReviewCount($responseData);

        // check original values with original ones
        $originalData['is_saleable'] = 1;
        $originalData['regular_price_with_tax'] = 99.95;
        $originalData['regular_price_without_tax'] = 99.95;
        $originalData['final_price_with_tax'] = 99.95;
        $originalData['final_price_without_tax'] = 99.95;
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($originalData[$field], $value, "'$field' has invalid value");
            }
        }
    }

    /**
     * Test successful product get with tax applied
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple_taxes.php
     */
    public function testGetWithTaxCalculation()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple_taxes');
        $this->assertEquals(10, $product->getPrice(), 'Product price is expected to be 10 for tax calculation tests');

        // default tax rate
        $taxRate = 0.0825;
        $finalPrice = $product->getPrice();
        $priceIncludesTax = Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX;
        $basedOn = Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON;

        $testConfigurations = array(
            array(
                'config' => array(
                    $priceIncludesTax => 0,
                    $basedOn => 'origin',
                ),
                'expected_prices' => array(
                    'regular_price_with_tax' => $product->getPrice()  * (1 + $taxRate),
                    'regular_price_without_tax' => $product->getPrice(),
                    'final_price_with_tax' => $finalPrice * (1 + $taxRate),
                    'final_price_without_tax' => $finalPrice
                )
            ),
            array(
                'config' => array(
                    $priceIncludesTax => 1,
                    $basedOn => 'origin',
                ),
                'expected_prices' => array(
                    'regular_price_with_tax' => $product->getPrice(),
                    'regular_price_without_tax' => $product->getPrice() / (1 + $taxRate),
                    'final_price_with_tax' => $finalPrice,
                    'final_price_without_tax' =>$finalPrice / (1 + $taxRate)
                )
            ),
        );

        foreach ($testConfigurations as $dataProvider) {
            $this->_checkTaxCalculation($product, $dataProvider['expected_prices'], $dataProvider['config']);
        }
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
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
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
     * Test successful get with filter by attributes
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testGetWithAttributeFilter()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $attributesToGet = array('sku', 'name', 'is_in_stock');
        $params = array('attrs' => implode(',', $attributesToGet));
        $restResponse = $this->callGet($this->_getResourcePath($product->getId()), $params);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertEquals(count($attributesToGet), count($responseData));
        $originalData = $product->getData();
        foreach ($attributesToGet as $attribute) {
            if (!is_array($originalData[$attribute])) {
                $this->assertEquals($originalData[$attribute], $responseData[$attribute]);
            }
        }
    }

    /**
     * Test unsuccessful get with invalid product id
     */
    public function testGetWithInvalidId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful get with disabled status
     */
    public function testGetWithDisabledStatus()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $product->addData($productData)
            ->setStoreId(0)
            ->setStockData(array('use_config_manage_stock' => 1))
            ->setWebsiteIds(array(Mage::app()->getDefaultStoreView()->getWebsiteId()))
            ->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED)->save();
        Magento_Test_Webservice::setFixture('product_simple', $product);

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful get with stock availability 'out of stock'
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testGetOutOfStockProduct()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
        $stockItem = $product->getStockItem();
        $stockItem->setIsInStock(0);
        $stockItem->save();

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
    }

    /**
     * Test product get for store that product is not assigned to
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Catalog/_fixtures/store_on_new_website.php
     */
    public function testGetFilterByStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store_on_new_website');
        $restResponse = $this->callGet($this->_getResourcePath($product->getId(), $store->getCode()));

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test with filter by invalid store
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testGetFilterByInvalidStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getResourcePath($product->getId(), 'INVALID_STORE'));

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful product delete
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful product create
     */
    public function testPost()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test successful product collection get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/products_collection.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testCollectionGet()
    {
        $this->_reindexPrices();
        /** @var $simpleProduct Mage_Catalog_Model_Product */
        $simpleProduct = $this->getFixture('product_simple');
        // quantity of enabled visible products
        $expectedProductsCount = 2;
        $expectedData = array_merge($simpleProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 108.32,
            'regular_price_without_tax' => 99.95,
            'final_price_with_tax' => 108.32,
            'final_price_without_tax' => 99.95
        ));
        $this->_checkProductCollectionGet($expectedProductsCount, $expectedData, 2);
    }

    /**
     * Test successful product collection get with specified store
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/products_collection.php
     */
    public function testCollectionGetFromSpecifiedStore()
    {
        // prepare product with different field values on different stores
        $originalProducts = $this->getFixture('products');
        /** @var $firstProduct Mage_Catalog_Model_Product */
        $firstProduct = reset($originalProducts);
        $firstProductDefaultValues = array_merge($firstProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 15.5,
            'regular_price_without_tax' => 15.5,
            'final_price_with_tax' => 15.2,
            'final_price_without_tax' => 15.2
        ));

        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store_on_new_website');
        $firstProduct->load($firstProduct->getId());
        $firstProduct->setStoreId($store->getId());
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductUpdateData.php';
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        unset($productDataForUpdate['stock_data']);
        $firstProduct->addData($productDataForUpdate);
        $firstProduct->setData('tier_price', array(
            array('website_id' => 0,'cust_group' => 1, 'price_qty' => 5.5, 'price' => 11.054)));
        $firstProduct->save();

        $this->_reindexPrices();
        // test collection get from specific store
        $firstProductDataAfterUpdate = array_merge($firstProduct->getData(), array(
            'is_saleable' => 1,
            'regular_price_with_tax' => 16.8,
            'regular_price_without_tax' => 15.5,
            'final_price_with_tax' => 16.8,
            'final_price_without_tax' => 15.5
        ));
        // quantity of enabled visible products
        $expectedProductsCount = 1;
        $this->_checkProductCollectionGet($expectedProductsCount, $firstProductDataAfterUpdate, 1, $store->getCode());
        // test collection get with default values
        $globalAttributes = array('is_in_stock');
        foreach ($globalAttributes as $globalAttribute) {
            $firstProductDefaultValues[$globalAttribute] = $firstProductDataAfterUpdate[$globalAttribute];
        }
        $this->_checkProductCollectionGet($expectedProductsCount, $firstProductDefaultValues, 1);
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
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
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
     * @param string $productId
     */
    protected function _checkGetProductUrls(&$productData, $productId)
    {
        $this->assertContains($productId, $productData['url'], 'Product url seems to be invalid');
        $this->_testUrlWithCurl($productData, 'url');
        unset($productData['url']);

        $this->assertContains($productId, $productData['buy_now_url'], 'Buy now url seems to be invalid');
        $this->assertContains('checkout/cart/add', $productData['buy_now_url'], 'Buy now url seems to be invalid');
        $this->_testUrlWithCurl($productData, 'buy_now_url', 302);
        unset($productData['buy_now_url']);
    }

    /**
     * Check if product image URL is correct
     *
     * @param array $productData
     * @param string $productId
     */
    protected function _checkGetImageUrl(&$productData)
    {
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
     * Test unsuccessful get using invalid store code
     */
    public function testCollectionGetFromInvalidStore()
    {
        $restResponse = $this->callGet($this->_getResourcePath(null, 'INVALID_STORE'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
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
}
