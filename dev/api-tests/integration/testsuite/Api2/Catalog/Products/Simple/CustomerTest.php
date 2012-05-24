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
 * Test simple product resource as customer role
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Products_Simple_CustomerTest extends Api2_Catalog_Products_CustomerAbstract
{
    /**
     * Test successful product get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @resourceOperation product::get
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

        $this->_checkGetProductUrls($responseData);
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
     * @resourceOperation product::get
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
     * Test successful get with filter by attributes
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @resourceOperation product::get
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
     *
     * @resourceOperation product::get
     */
    public function testGetWithInvalidId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful get with disabled status
     *
     * @resourceOperation product::get
     */
    public function testGetWithDisabledStatus()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $productData = require dirname(__FILE__) . '/../../_fixtures/Backend/SimpleProductData.php';
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
     * @resourceOperation product::get
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
     * @resourceOperation product::get
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
     * @resourceOperation product::get
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
     * @resourceOperation product::delete
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
     *
     * @resourceOperation product::create
     */
    public function testPost()
    {
        $productData = require dirname(__FILE__) . '/../../_fixtures/Backend/SimpleProductData.php';
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Test successful product collection get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/products_collection.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @resourceOperation product::multiget
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
     * @resourceOperation product::multiget
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
        $productDataForUpdate = require dirname(__FILE__) . '/../../_fixtures/Backend/SimpleProductUpdateData.php';
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
     * Test unsuccessful get using invalid store code
     *
     * @resourceOperation product::multiget
     */
    public function testCollectionGetFromInvalidStore()
    {
        $restResponse = $this->callGet($this->_getResourcePath(null, 'INVALID_STORE'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
    }
}
