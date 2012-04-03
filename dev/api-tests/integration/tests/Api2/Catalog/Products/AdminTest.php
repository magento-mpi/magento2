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
 * @package     Mage_Core
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

class Api2_Catalog_Products_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $this->deleteFixture('product_simple_all_fields', true);
        if ($this->getFixture('products')) {
            foreach ($this->getFixture('products') as $product) {
                $this->addModelToDelete($product, true);
            }
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
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
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
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($originalData[$field], $value);
            }
        }
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
        $attributesToGet = array('sku', 'name', 'visibility', 'status', 'price');
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
     * Test product resource post
     */
    public function testPostSimpleRequiredFieldsOnly()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);
        foreach ($productData as $attribute => $value) {
            $this->assertEquals($product->getData($attribute), $value);
        }
    }

    /**
     * Test product resource post with all fields
     *
     * @param array $productData
     * @dataProvider dataProviderTestPostSimpleAllFieldsValid()
     */
    public function testPostSimpleAllFieldsValid($productData)
    {
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);

        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $this->assertEquals(strtotime($productData[$attribute]), strtotime($product->getData($attribute)));
        }

        $exclude = array_merge($dateAttributes, array('group_price', 'tier_price', 'stock_data',
            'url_key', 'url_key_create_redirect'));
        // Validate URL Key - all special chars should be replaced with dash sign
        $this->assertEquals('123-abc', $product->getUrlKey());
        $productAttributes = array_diff_key($productData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($value, $product->getData($attribute));
        }

        if (isset($productData['stock_data'])) {
            $stockItem = $product->getStockItem();
            foreach ($productData['stock_data'] as $attribute => $value) {
                $this->assertEquals($value, $stockItem->getData($attribute));
            }
        }
    }

    /**
     * Data provider for testPostSimpleAllFieldsValid
     *
     * @return array
     */
    public function dataProviderTestPostSimpleAllFieldsValid()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsData.php';
        $productDataSpecialChars = require dirname(__FILE__)
            . '/../_fixtures/Backend/SimpleProductSpecialCharsData.php';

        return array(
            array($productDataSpecialChars),
            array($productData),
        );
    }

    /**
     * Test product resource post with all invalid fields
     * Negative test.
     */
    public function testPostSimpleAllFieldsInvalid()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsInvalidData.php';
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $errorsPlain = array();
        foreach ($errors as $error) {
            $errorsPlain[] = $error['message'];
        }
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'SKU length should be 64 characters maximum.',
            'Invalid "cust_group" value in the "group_price:0" set',
            'Please enter a number 0 or greater in the "price" field in the "group_price:1" set.',
            'Invalid "website_id" value in the "group_price:2" set.',
            'Invalid "website_id" value in the "group_price:3" set.',
            'The "cust_group" value in the "group_price:3" set is a required field.',
            'The "website_id" value in the "group_price:4" set is a required field.',
            'Invalid "website_id" value in the "group_price:5" set.',
            'The "price" value in the "group_price:5" set is a required field.',
            'Invalid "cust_group" value in the "tier_price:0" set',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:1" set.',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:2" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:3" set.',
            'Invalid "website_id" value in the "tier_price:4" set.',
            'Invalid "website_id" value in the "tier_price:5" set.',
            'The "price_qty" value in the "tier_price:7" set is a required field.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:7" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:8" set.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.',
            'Please enter a valid number in the "notify_stock_qty" field in the "stock_data" set.',
            'Please enter a number 0 or greater in the "min_qty" field in the "stock_data" set.',
            'Invalid "is_decimal_divided" value in the "stock_data" set.',
            'Please use numbers only in the "min_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "max_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "qty_increments" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Invalid "backorders" value in the "stock_data" set.',
            'Invalid "is_in_stock" value in the "stock_data" set.',
            'Please enter a number 0 or greater in the "gift_wrapping_price" field.',
            'Invalid "cust_group" value in the "group_price:4" set',
            'Invalid "cust_group" value in the "tier_price:6" set',
            'Resource data pre-validation error.',
        );
        $invalidValueAttributes = array('status', 'visibility', 'msrp_enabled', 'msrp_display_actual_price_type',
            'enable_googlecheckout', 'tax_class_id', 'custom_design', 'page_layout', 'options_container',
            'gift_message_available', 'gift_wrapping_available');
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value "%s" for attribute "%s".', $productData[$attribute], $attribute);
        }
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid date in the "%s" field.', $attribute);
        }
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        foreach ($positiveNumberAttributes as $attribute) {
            $expectedErrors[] = sprintf('Please enter a number 0 or greater in the "%s" field.', $attribute);
        }

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product create resource with invalid qty uses decimals value
     * Negative test.
     */
    public function testPostInvalidQtyUsesDecimals()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductInvalidQtyUsesDecimals.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Invalid "is_qty_decimal" value in the "stock_data" set.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product create resource with invalid manage stock value
     * Negative test.
     */
    public function testPostInvalidManageStock()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductInvalidManageStock.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Invalid "manage_stock" value in the "stock_data" set.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product create resource with invalid weight value
     * Negative test.
     */
    public function testPostWeightOutOfRange()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductWeightOutOfRange.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource data pre-validation error.',
            'The "weight" value is not within the specified range.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product create resource with not unique sku value
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostNotUniqueSku()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $productData['sku'] = $product->getSku();

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Invalid attribute "sku": The value of attribute "SKU" must be unique'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product create resource with empty required fields
     * Negative test.
     */
    public function testPostEmptyRequiredFields()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductEmptyRequired.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        unset($productData['type_id']);
        unset($productData['attribute_set_id']);
        unset($productData['stock_data']);
        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.'
        );
        foreach ($productData as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product resource post using config values in inventory
     */
    public function testPostInventoryUseConfigValues()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductInventoryUseConfig.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $fields = array('use_config_min_qty', 'use_config_min_sale_qty', 'use_config_max_sale_qty',
            'use_config_backorders', 'use_config_notify_stock_qty', 'use_config_enable_qty_increments');
        foreach ($fields as $field) {
            $this->assertEquals(1, $stockItem->getData($field), $field . ' is not set to 1');
        }
    }

    /**
     * Test product resource post using config values in inventory manage stock field
     */
    public function testPostInventoryManageStockUseConfig()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductManageStockUseConfig.php';

        $this->_updateAppConfig('cataloginventory/item_options/manage_stock', 0, true, true);

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $this->assertEquals(0, $stockItem->getManageStock());
    }

    /**
     * Test product resource post when manage_stock set to no and inventory data is sent in request
     */
    public function testPostInventoryManageStockNo()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductManageStockNo.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);

        $stockItem = $product->getStockItem();
        $this->assertNotNull($stockItem);
        $this->assertEquals(0, $stockItem->getManageStock());
        $this->assertEquals(0, $stockItem->getQty());
    }

    /**
     * Test product resource post using config values in gift options
     */
    public function testPostGiftOptionsUseConfigValues()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductGiftOptionsUseConfig.php';

        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);
    }

    /**
     * Test successful product delete
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // check if product was really deleted
        $deletedProduct = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertEmpty($deletedProduct->getId());
    }

    /**
     * Test unsuccessful delete with invalid product id
     */
    public function testDeleteWithInvalidId()
    {
        $restResponse = $this->callDelete($this->_getResourcePath('INVALID_ID'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test tier and group prices update
     *
     * @dataProvider dataProviderTestUpdateGroupPrice
     * @magentoDataFixture Api2/Catalog/_fixtures/store_on_new_website.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple_all_fields.php
     * @param string $priceField
     * @param int $websiteId
     * @param int $priceScope
     * @param int $expectedResponseCode
     * @param bool $checkData
     */
    public function testUpdateGroupPrice($priceField, $websiteId, $priceScope, $expectedResponseCode, $checkData)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple_all_fields');
        // set price scope to required value
        /** @var $catalogHelper Mage_Catalog_Helper_Data */
        $catalogHelper = Mage::helper('catalog');
        if ($catalogHelper->getPriceScope() != $priceScope) {
            $this->_updateAppConfig(Mage_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE, $priceScope, false, false, true);
        }
        // test update with existing website when price scope is website
        $price = array('website_id' => $websiteId, 'cust_group' => 1, 'price' => 333.5);
        if ($priceField == 'tier_price') {
            $price['price_qty'] = 88;
        }
        $dataForUpdate = array($priceField => array($price));
        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $dataForUpdate);
        $this->assertEquals($expectedResponseCode, $restResponse->getStatus());
        if ($checkData) {
            // check if group price was really updated
            /** @var $updatedProduct Mage_Catalog_Model_Product */
            $updatedProduct = Mage::getModel('catalog/product')->load($product->getId());
            $tierPricesAfterUpdate = $updatedProduct->getData($priceField);
            $this->assertCount(1, $tierPricesAfterUpdate, "Invalid tier price count after update");
            $updatedTierPrice = reset($tierPricesAfterUpdate);
            foreach (reset($dataForUpdate[$priceField]) as $field => $value) {
                $this->assertEquals($value, $updatedTierPrice[$field]);
            }
        }
    }

    /**
     * Data provider for testUpdateGroupPrice
     *
     * @return array
     */
    public function dataProviderTestUpdateGroupPrice()
    {
        $defaultWebsiteId = 1;
        $allWebsitesId = 0;
        $invalidWebsiteId = 9999;
        $priceScope = array('global' => Mage_Catalog_Helper_Data::PRICE_SCOPE_GLOBAL,
            'website' => Mage_Catalog_Helper_Data::PRICE_SCOPE_WEBSITE);
        $priceDataSets = array(
            array($allWebsitesId, $priceScope['global'], Mage_Api2_Model_Server::HTTP_OK, true,
                'Data set: All websites, global scope'),
            array($defaultWebsiteId, $priceScope['global'], Mage_Api2_Model_Server::HTTP_BAD_REQUEST, false,
                'Data set: Default website, global scope'),
            array($invalidWebsiteId, $priceScope['global'], Mage_Api2_Model_Server::HTTP_BAD_REQUEST, false,
                'Data set: Invalid website, global scope'),
            array($allWebsitesId, $priceScope['website'], Mage_Api2_Model_Server::HTTP_OK, true,
                'Data set: All websites, website scope'),
            // we can not check data as more than only Mage_Catalog_Helper_Data::XML_PATH_PRICE_SCOPE should be changed
            array($defaultWebsiteId, $priceScope['website'], Mage_Api2_Model_Server::HTTP_OK, false,
                'Data set: Default website, website scope'),
        );
        $data = array();
        foreach ($priceDataSets as $dataSet) {
            $data[] = array_merge(array('tier_price'), $dataSet);
            $data[] = array_merge(array('group_price'), $dataSet);
        }
        return $data;
    }

    /**
     * Test update attributes with souces. Basicly, check type casting during data validation
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @dataProvider dataProviderTestUpdateAttributeWithSource
     * @param string $attributeName
     * @param mixed $attributeValue
     * @param int $expectedResponseCode
     */
    public function testUpdateAttributeWithSource($attributeName, $attributeValue, $expectedResponseCode)
    {
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = array($attributeName => $attributeValue);
        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals($expectedResponseCode, $restResponse->getStatus());
        if ($expectedResponseCode == Mage_Api2_Model_Server::HTTP_OK) {
            /** @var $updatedProduct Mage_Catalog_Model_Product */
            $updatedProduct = Mage::getModel('catalog/product')->load($product->getId());
            $this->assertEquals($attributeValue, $updatedProduct->getData($attributeName),
                "'$attributeName' attribute update failed");
        }
    }

    /**
     * Prepare attribute values for update casted to different types
     *
     * @return array
     */
    public function dataProviderTestUpdateAttributeWithSource()
    {
        $statuses = array('bad_request' => Mage_Api2_Model_Server::HTTP_BAD_REQUEST,
            'ok' => Mage_Api2_Model_Server::HTTP_OK);
        return array(
            array('visibility', (int)1, $statuses['ok']),
            array('visibility', (string)1, $statuses['ok']),
            array('visibility', true, $statuses['bad_request']),
            array('visibility', "abc",  $statuses['bad_request']),
            array('visibility', "",  $statuses['bad_request']),
            array('enable_googlecheckout', (int)1, $statuses['ok']),
            array('enable_googlecheckout', (string)1, $statuses['ok']),
            array('enable_googlecheckout', (int)0, $statuses['ok']),
            array('enable_googlecheckout', (string)0, $statuses['ok']),
            array('enable_googlecheckout', true, $statuses['bad_request']),
            array('enable_googlecheckout', false, $statuses['bad_request']),
            array('enable_googlecheckout', "abc",  $statuses['bad_request']),
            array('enable_googlecheckout', "",  $statuses['bad_request']),
        );
    }

    /**
     * Test gift options update with "use_config_..." attributes
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @dataProvider dataProviderTestUpdateGiftOptionsUseConfig
     * @param string $attributeName
     * @param int $attributeValue
     * @param bool $useConfig
     * @param int $expectedValue
     */
    public function testUpdateGiftOptionsUseConfig($attributeName, $attributeValue, $useConfig, $expectedValue)
    {
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = array("use_config_$attributeName" => $useConfig);
        if (!is_null($attributeValue)) {
            $productDataForUpdate[$attributeName] = $attributeValue;
        }
        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        /** @var $updatedProduct Mage_Catalog_Model_Product */
        $updatedProduct = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertTrue($expectedValue === $updatedProduct->getData($attributeName),
            "'$attributeName' attribute update failed");
    }

    /**
     * Data provider for gift options
     *
     * @return array
     */
    public function dataProviderTestUpdateGiftOptionsUseConfig()
    {
        $giftMessageAvailable =
            (int) Mage::getStoreConfig(Mage_GiftMessage_Helper_Message::XPATH_CONFIG_GIFT_MESSAGE_ALLOW_ITEMS);
        $giftWrappingAvailable =
            (int) Mage::getStoreConfig(Enterprise_GiftWrapping_Helper_Data::XML_PATH_ALLOWED_FOR_ITEMS);
        return array(
            array('gift_message_available', null, 0, "$giftMessageAvailable"),
            array('gift_wrapping_available', null, 0, "$giftWrappingAvailable"),
            array('gift_message_available', 1, 0, '1'),
            array('gift_message_available', 1, 1, null),
            array('gift_message_available', 0, 0, '0'),
            array('gift_message_available', 0, 1, null),
            array('gift_wrapping_available', 1, 0, '1'),
            array('gift_wrapping_available', 1, 1, null),
            array('gift_wrapping_available', 0, 0, '0'),
            array('gift_wrapping_available', 0, 1, null),
        );
    }

    /**
     * Test successful product update
     *
     * @param array $productDataForUpdate
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @dataProvider dataProviderTestUpdateSuccessful()
     */
    public function testUpdateSuccessful($productDataForUpdate)
    {
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        /** @var $updatedProduct Mage_Catalog_Model_Product */
        $updatedProduct = Mage::getModel('catalog/product')
            ->load($product->getId())
            ->clearInstance()
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->load($product->getId());
        if (isset($productDataForUpdate['url_key'])) {
            // Validate URL Key - all special chars should be replaced with dash sign
            $productDataForUpdate['url_key'] = '123-abc';
        }
        unset($productDataForUpdate['url_key_create_redirect']);
        $this->_checkProductData($updatedProduct, $productDataForUpdate);
    }

    /**
     * Data provider for testPostSimpleAllFieldsValid
     *
     * @return array
     */
    public function dataProviderTestUpdateSuccessful()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsData.php';
        $productDataSpecialChars = require dirname(__FILE__)
            . '/../_fixtures/Backend/SimpleProductSpecialCharsData.php';
        $productDataZeroValidValuesAsStrings = array(
            'tax_class_id' => '0',
            'sku' => '0',
            'weight' => '0',
            'price' => '0',
            'stock_data' => array('qty' => '0'),
        );
        $productDataZeroValidValuesAsIntegers = array(
            'tax_class_id' => 0,
            'sku' => 0,
            'weight' => 0,
            'price' => 0,
            'stock_data' => array('qty' => 0),
        );
        $validNumericValues = array(
            'attribute_set_id' => 4,
            'msrp_enabled' => 2,
            'msrp_display_actual_price_type' => 4,
            'price' => 100.50,
            'special_price' => 50.50,
            'weight' => 3,
            'msrp' => 50.00,
            'gift_wrapping_price' => 5,
            'status' => 1,
            'visibility' => 4,
            'enable_googlecheckout' => 1,
            'tax_class_id' => 4,
            'group_price' => array(array('website_id' => 0, 'cust_group' => 1, 'price' => 20.0000)),
            'tier_price' => array(array('website_id' => 0, 'cust_group' => 1, 'price' => 10.0000,
                'price_qty' => 5.0000)),
            'stock_data' => array(
                'qty' => 654.0000,
                'min_qty' => 0.0000,
                'use_config_min_qty' => 1,
                'is_qty_decimal' => 0,
                'backorders' => 0,
                'use_config_backorders' => 1,
                'min_sale_qty' => 1.0000,
                'use_config_min_sale_qty' => 1,
                'max_sale_qty' => 0.0000,
                'use_config_max_sale_qty' => 1,
                'is_in_stock' => 1,
                'notify_stock_qty' => 5,
                'use_config_notify_stock_qty' => 0,
                'manage_stock' => 1,
                'use_config_manage_stock' => 0,
                'use_config_qty_increments' => 1,
                'qty_increments' => 0.0000,
                'use_config_enable_qty_inc' => 1,
                'enable_qty_increments' => 0,
                'is_decimal_divided' => 0
            )
        );
        $validNumericValuesAsStrings = array(
            'attribute_set_id' => '4',
            'msrp_enabled' => '2',
            'msrp_display_actual_price_type' => '4',
            'price' => '100.50',
            'special_price' => '50.50',
            'weight' => '3',
            'msrp' => '50.00',
            'gift_wrapping_price' => '5',
            'status' => '1',
            'visibility' => '4',
            'enable_googlecheckout' => '1',
            'tax_class_id' => '4',
            'group_price' => array(array('website_id' => '0', 'cust_group' => '1', 'price' => '20.0000')),
            'tier_price' => array(array('website_id' => '0', 'cust_group' => '1', 'price' => '10.0000',
                'price_qty' => '5.0000')),
            'stock_data' => array(
                'use_config_manage_stock' => '0',
                'manage_stock' => '1',
                'qty' => '654.0000',
                'min_qty' => '0.0000',
                'use_config_min_qty' => '0',
                'is_qty_decimal' => '0',
                'backorders' => '0',
                'use_config_backorders' => '0',
                'min_sale_qty' => '1.0000',
                'use_config_min_sale_qty' => '0',
                'max_sale_qty' => '0.0000',
                'use_config_max_sale_qty' => '0',
                'is_in_stock' => '1',
                'notify_stock_qty' => '5',
                'use_config_notify_stock_qty' => '0',
                'use_config_qty_increments' => '1',
                'qty_increments' => '0.0000',
                'use_config_enable_qty_inc' => '0',
                'enable_qty_increments' => '0',
                'is_decimal_divided' => '0'
            )
        );

        return array(
            array($productDataSpecialChars),
            array($productData),
            array($productDataZeroValidValuesAsStrings),
            array($productDataZeroValidValuesAsIntegers),
            array($validNumericValues),
            array($validNumericValuesAsStrings),
        );
    }

    /**
     * Test successful product update on specified store
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/store_on_new_website.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple_all_fields.php
     */
    public function testUpdateOnSpecifiedStoreSuccessful()
    {
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductUpdateData.php';
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple_all_fields');
        $testStore = $this->getFixture('store_on_new_website');
        $restResponse = $this->callPut($this->_getResourcePath($product->getId(), $testStore->getCode()),
            $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // Check if product data was updated on specified store
        /** @var $updatedProduct Mage_Catalog_Model_Product */
        $updatedProduct = Mage::getModel('catalog/product')
            ->load($product->getId())
            ->clearInstance()
            ->setStoreId($testStore->getId())
            ->load($product->getId());
        // Validate URL Key - all special chars should be replaced with dash sign
        $this->_checkProductData($updatedProduct, $productDataForUpdate);

        // Check if product Store View/Website scope attributes data is untouched on default store
        $origProductData = array();
        foreach ($productDataForUpdate as $attribute => $value) {
            $origProductData[$attribute] = $product->getData($attribute);
        }
        $origProductData['stock_data'] = $productDataForUpdate['stock_data'];
        $globalAttributes = array('sku', 'weight', 'price', 'special_price', 'msrp', 'enable_googlecheckout',
            'gift_wrapping_price');
        foreach ($globalAttributes as $attribute) {
            $origProductData[$attribute] = $updatedProduct->getData($attribute);
        }

        /** @var $origProduct Mage_Catalog_Model_Product */
        $origProduct = Mage::getModel('catalog/product')
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->load($product->getId());
        $this->_checkProductData($origProduct, $origProductData);
    }

    /**
     * Test product update resource with not unique sku value
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple_all_fields.php
     */
    public function testUpdateNotUniqueSku()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        /** @var $updateProduct Mage_Catalog_Model_Product */
        $updateProduct = $this->getFixture('product_simple_all_fields');
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $productDataForUpdate['sku'] = $product->getSku();

        $restResponse = $this->callPut($this->_getResourcePath($updateProduct->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Invalid attribute "sku": The value of attribute "SKU" must be unique'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test update with invalid store
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testUpdateWithInvalidStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductAllFieldsData.php';

        $restResponse = $this->callPut($this->_getResourcePath($product->getId(), 'INVALID_STORE'),
            $productDataForUpdate);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
    }

    /**
     * Test product update with empty required fields
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testUpdateEmptyRequiredFields()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductEmptyRequired.php';

        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        unset($productDataForUpdate['stock_data']);
        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.'
        );
        foreach ($productDataForUpdate as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product resource post with all invalid fields
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testUpdateAllFieldsInvalid()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = require dirname(__FILE__)
            . '/../_fixtures/Backend/SimpleProductAllFieldsInvalidData.php';

        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $errorsPlain = array();
        foreach ($errors as $error) {
            $errorsPlain[] = $error['message'];
        }
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'SKU length should be 64 characters maximum.',
            'Invalid "cust_group" value in the "group_price:0" set',
            'Please enter a number 0 or greater in the "price" field in the "group_price:1" set.',
            'Invalid "website_id" value in the "group_price:2" set.',
            'Invalid "website_id" value in the "group_price:3" set.',
            'The "cust_group" value in the "group_price:3" set is a required field.',
            'The "website_id" value in the "group_price:4" set is a required field.',
            'Invalid "website_id" value in the "group_price:5" set.',
            'The "price" value in the "group_price:5" set is a required field.',
            'Invalid "cust_group" value in the "tier_price:0" set',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:1" set.',
            'Please enter a number greater than 0 in the "price_qty" field in the "tier_price:2" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:3" set.',
            'Invalid "website_id" value in the "tier_price:4" set.',
            'Invalid "website_id" value in the "tier_price:5" set.',
            'The "price_qty" value in the "tier_price:7" set is a required field.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:7" set.',
            'Please enter a number greater than 0 in the "price" field in the "tier_price:8" set.',
            'Please enter a valid number in the "qty" field in the "stock_data" set.',
            'Please enter a valid number in the "notify_stock_qty" field in the "stock_data" set.',
            'Please enter a number 0 or greater in the "min_qty" field in the "stock_data" set.',
            'Invalid "is_decimal_divided" value in the "stock_data" set.',
            'Please use numbers only in the "min_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "max_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Please use numbers only in the "qty_increments" field in the "stock_data" set. '
            . 'Please avoid spaces or other non numeric characters.',
            'Invalid "backorders" value in the "stock_data" set.',
            'Invalid "is_in_stock" value in the "stock_data" set.',
            'Please enter a number 0 or greater in the "gift_wrapping_price" field.',
            'Invalid "cust_group" value in the "group_price:4" set',
            'Invalid "cust_group" value in the "tier_price:6" set',
            'Resource data pre-validation error.',
        );
        $invalidValueAttributes = array('status', 'visibility', 'msrp_enabled', 'msrp_display_actual_price_type',
            'enable_googlecheckout', 'tax_class_id', 'custom_design', 'page_layout', 'options_container',
            'gift_message_available', 'gift_wrapping_available');
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value "%s" for attribute "%s".',
                $productDataForUpdate[$attribute], $attribute);
        }
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid date in the "%s" field.', $attribute);
        }
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        foreach ($positiveNumberAttributes as $attribute) {
            $expectedErrors[] = sprintf('Please enter a number 0 or greater in the "%s" field.', $attribute);
        }

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product update resource with invalid manage stock value
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testUpdateInvalidManageStock()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = require dirname(__FILE__)
            . '/../_fixtures/Backend/SimpleProductInvalidManageStock.php';

        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource data pre-validation error.',
            'Invalid "manage_stock" value in the "stock_data" set.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product update resource with invalid weight value
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     */
    public function testUpdateWeightOutOfRange()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $productDataForUpdate = require dirname(__FILE__)
            . '/../_fixtures/Backend/SimpleProductWeightOutOfRange.php';

        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Resource data pre-validation error.',
            'The "weight" value is not within the specified range.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Check if product data equals expected data
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $expectedData
     */
    protected function _checkProductData($product, $expectedData)
    {
        $this->assertNotNull($product->getId());
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');
        foreach ($dateAttributes as $attribute) {
            if (isset($expectedData[$attribute])) {
                $this->assertEquals(strtotime($expectedData[$attribute]), strtotime($product->getData($attribute)),
                    $attribute . ' is not equal.');
            }
        }
        $exclude = array_merge($dateAttributes, array('group_price', 'tier_price', 'stock_data'));
        $productAttributes = array_diff_key($expectedData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($value, $product->getData($attribute), $attribute . ' is not equal.');
        }
        if (isset($expectedData['stock_data'])) {
            $stockItem = $product->getStockItem();
            foreach ($expectedData['stock_data'] as $attribute => $value) {
                $this->assertEquals($value, $stockItem->getData($attribute), $attribute . ' is not equal.');
            }
        }
    }

    /**
     * Test successful product collection get
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/products_collection.php
     */
    public function testCollectionGet()
    {
        $products = $this->getFixture('products');
        /** @var $firstProduct Mage_Catalog_Model_Product */
        $firstProduct = reset($products);
        $this->_checkProductCollectionGet($products, $firstProduct->getData());
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
        $firstProductDefaultValues = $firstProduct->getData();
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store_on_new_website');
        $firstProduct->setStoreId($store->getId())->load();
        $productDataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductUpdateData.php';
        unset($productDataForUpdate['type_id']);
        unset($productDataForUpdate['attribute_set_id']);
        unset($productDataForUpdate['stock_data']);
        foreach ($productDataForUpdate as $field => $value) {
            $firstProduct->setData($field, $value);
        }
        $firstProduct->save();

        // test collection get from specific store
        $firstProductDataAfterUpdate = $firstProduct->getData();
        $this->_checkProductCollectionGet($originalProducts, $firstProductDataAfterUpdate, $store->getCode());
        // test collection get with default values
        $globalAttributes = array('price', 'special_price', 'msrp', 'gift_wrapping_price');
        foreach ($globalAttributes as $globalAttribute) {
            $firstProductDefaultValues[$globalAttribute] = $firstProductDataAfterUpdate[$globalAttribute];
        }
        $this->_checkProductCollectionGet($originalProducts, $firstProductDefaultValues);
    }

    /**
     * Perform collection get with data check
     *
     * @param array $products
     * @param array $firstProductExpectedData
     * @param string $storeCode
     */
    protected function _checkProductCollectionGet($products, $firstProductExpectedData, $storeCode = null)
    {
        // find all products created after one with id = $firstProductId
        $requestParams = array(
            'filter[0][attribute]' => "entity_id",
            'filter[0][gteq][0]' => $firstProductExpectedData['entity_id'],
        );
        $restResponse = $this->callGet($this->_getResourcePath(null, $storeCode), $requestParams);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $resultProducts = $restResponse->getBody();
        $this->assertEquals(count($products), count($resultProducts), "Invalid products number in response");

        $isFirstProductFoundInResponse = false;
        $dateAttributes = array('news_from_date', 'news_to_date', 'special_from_date', 'special_to_date',
            'custom_design_from', 'custom_design_to');

        // check only first product data
        foreach ($resultProducts as $resultProductData) {
            if ($resultProductData['entity_id'] == $firstProductExpectedData['entity_id']) {
                $isFirstProductFoundInResponse = true;
                foreach ($resultProductData as $key => $resultProductValue) {
                    if (in_array($key, $dateAttributes)) {
                        $this->assertEquals(strtotime($firstProductExpectedData[$key]), strtotime($resultProductValue),
                            "'$key' is invalid");
                    } else {
                        $this->assertEquals($firstProductExpectedData[$key], $resultProductValue, "'$key' is invalid");
                    }
                }
            }
        }
        $this->assertEquals(true, $isFirstProductFoundInResponse,
            "Product with id={$firstProductExpectedData['entity_id']} was not found in response");
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
     * Test product resource post with all fields
     *
     * @param array $productData
     */
    public function testPostMediaAttributesDefaultValue()
    {
        $productData = require dirname(__FILE__) . '/../_fixtures/Backend/SimpleProductData.php';
        $restResponse = $this->callPost($this->_getResourcePath(), $productData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $location = $restResponse->getHeader('Location');
        list($productId) = array_reverse(explode('/', $location));
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($productId);
        $this->assertNotNull($product->getId());
        $this->_deleteProductAfterTest($product);

        $found = false;
        foreach ($product->getMediaAttributes() as $mediaAttribute) {
            $mediaAttrCode = $mediaAttribute->getAttributeCode();
            $this->assertEquals($product->getData($mediaAttrCode), 'no_selection',
                'Attribute "' . $mediaAttrCode . '" has no default value');
            $found = true;
        }
        $this->assertTrue($found, 'Media attrributes not found');
    }

    /**
     * Mark product for removal in tear down
     *
     * @param $product
     */
    protected function _deleteProductAfterTest($product)
    {
        $this->addModelToDelete($product, true);
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

