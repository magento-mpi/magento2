<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test product resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Product_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        parent::tearDown();
    }

    /**
     * Delete store fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('store_on_new_website', true);
        Magento_TestCase::deleteFixture('store_group', true);
        Magento_TestCase::deleteFixture('website', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test successful product get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testGet()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $product->getData();

        // check if all possible fields are in request
        $requiredFields = array('type', 'sku', 'name', 'description', 'short_description',
            'regular_price', 'final_price', 'final_price_with_tax', 'final_price_without_tax', 'tier_price',
            'image_url', 'is_in_stock', 'is_saleable', 'total_reviews_count', 'url', 'buy_now_url',
            'has_custom_options');
        foreach($requiredFields as $field) {
            $this->assertArrayHasKey($field, $responseData, "'$field' field is missing in response");
        }

        $this->_checkGetUrls($responseData, $product);
        $this->_checkGetTierPrices($responseData);

        // check original values with original ones
        $originalData['is_saleable'] = 1;
        $originalData['regular_price'] = 99.95;
        $originalData['final_price'] = 99.95;
        $originalData['final_price_with_tax'] = 99.95;
        $originalData['final_price_without_tax'] = 99.95;
        $fieldsMap = array('type' => 'type_id');
        foreach ($responseData as $field => $value) {
            if (isset($fieldsMap[$field])) {
                $field = $fieldsMap[$field];
            }
            if (!is_array($value)) {
                $this->assertEquals($originalData[$field], $value, "'$field' has invalid value");
            }
        }
    }

    /**
     * Check if tier prices are correct
     *
     * @param array $responseData
     */
    protected function _checkGetTierPrices($responseData)
    {
        $this->assertInternalType('array', $responseData['tier_price'], "'tier_price' expected to be an array");
        $this->assertCount(2, $responseData['tier_price']);
        $requiredFields = array('qty', 'price', 'price_with_tax', 'price_without_tax');
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
     * @param array $responseData
     * @param Mage_Catalog_Model_Product $product
     */
    protected function _checkGetUrls(&$responseData, $product)
    {
        $this->assertNotEmpty($responseData['image_url'], 'Image url is not set');
//        $this->_testUrlWithCurl($responseData, 'image_url');
        unset($responseData['image_url']);

        $this->assertContains($product->getId(), $responseData['url'], 'Product url seems to be invalid');
        $this->_testUrlWithCurl($responseData, 'url');
        unset($responseData['url']);

        $this->assertContains($product->getId(), $responseData['buy_now_url'], 'Buy now url seems to be invalid');
        $this->assertContains('checkout/cart/add', $responseData['buy_now_url'], 'Buy now url seems to be invalid');
        $this->_testUrlWithCurl($responseData, 'buy_now_url', 302);
        unset($responseData['buy_now_url']);

        $this->assertGreaterThanOrEqual(0, $responseData['total_reviews_count']);
        unset($responseData['total_reviews_count']);
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
        // TODO: uncomment line below after fix of URLs generation in core
        //$this->assertEquals($expectedResponseCode, $responseCode, "'$urlField' is not accessible with cURL");
    }


    /**
     * Test successful product get with tax applied
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testGetWithTaxCalculation()
    {
        $this->markTestIncomplete('Test need to be rewritten after changes in product get');
        // assure that customer has appropriate billing and shipping addresses
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);
        if (!$customer->getDefaultBillingAddress() || !$customer->getDefaultShippingAddress()) {
            $this->_addAddressToCustomer($customer);
        }

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $taxableGoodsTaxClassId = 2;
        $product->setTaxClassId($taxableGoodsTaxClassId)->save();
        $this->assertEquals(10, $product->getPrice(), 'Product price is expected to be 10 for tax calculation tests');

        $basedOn = Mage_Tax_Model_Config::CONFIG_XML_PATH_BASED_ON;
        $priceIncludesTax = Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX;
        $priceDisplayType = Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_DISPLAY_TYPE;
        $testConfigurations = array(
            array(
                'config' => array(
                    $basedOn => 'origin',
                    $priceIncludesTax => 0,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX,
                ),
                'expected_price' => 10.84,
            ),
            array(
                'config' => array(
                    $basedOn => 'billing',
                    $priceIncludesTax => 0,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX,
                ),
                'expected_price' => 10.84,
            ),
            array(
                'config' => array(
                    $basedOn => 'origin',
                    $priceIncludesTax => 0,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX,
                ),
                'expected_price' => 10.83,
            ),
            array(
                'config' => array(
                    $basedOn => 'shipping',
                    $priceIncludesTax => 1,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_INCLUDING_TAX,
                ),
                'expected_price' => 10.01,
            ),
            array(
                'config' => array(
                    $basedOn => 'shipping',
                    $priceIncludesTax => 1,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX,
                ),
                'expected_price' => 9.24,
            ),
            array(
                'config' => array(
                    $basedOn => 'shipping',
                    $priceIncludesTax => 0,
                    $priceDisplayType => Mage_Tax_Model_Config::DISPLAY_TYPE_EXCLUDING_TAX,
                ),
                'expected_price' => 10,
            ),
        );
        foreach ($testConfigurations as $dataProvider) {
            $this->_checkTaxCalculation($product, $dataProvider['expected_price'], $dataProvider['config']);
        }
    }

    /**
     * Check if tax is applied correctly to product price. Specific tax config is applied during test
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $expectedPrice
     * @param array $config
     */
    protected function _checkTaxCalculation($product, $expectedPrice, $config)
    {
        foreach ($config as $configPath => $configValue) {
            $this->_updateAppConfig($configPath, $configValue);
        }

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $assertMessage = "Tested configuration: ";
        foreach ($config as $configPath => $configValue) {
            $assertMessage .= " $configPath = $configValue;";
        }
        $this->assertEquals($expectedPrice, $responseData['price'], $assertMessage, 0.01);
    }

    /**
     * Add valid address to customer. Address should be valid for default tax rules
     *
     * @param Mage_Customer_Model_Customer $customer
     */
    protected function _addAddressToCustomer(Mage_Customer_Model_Customer $customer)
    {
        /** @var $address Mage_Customer_Model_Address */
        $address = Mage::getModel('customer/address');
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
    }

    /**
     * Test successful get with filter by attributes
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testGetWithDisabledStatus()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_DISABLED);
        $product->save();

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful get with stock availability 'out of stock'
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Create path to resource
     *
     * @param string $id
     * @param string $storeId
     * @return string
     */
    protected function _getResourcePath($id, $storeId = null)
    {
        $path = "products/$id";
        if ($storeId) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
