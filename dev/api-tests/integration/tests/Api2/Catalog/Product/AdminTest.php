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
class Api2_Catalog_Product_AdminTest extends Magento_Test_Webservice_Rest_Admin
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
        Magento_TestCase::deleteFixture('product_simple_all_fields', true);
        Magento_TestCase::deleteFixture('store_group', true);
        Magento_TestCase::deleteFixture('website', true);
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
        $fieldsMap = array('type' => 'type_id', 'product_id' => 'entity_id', 'set' => 'attribute_set_id');
        foreach ($responseData as $field => $value) {
            if (isset($fieldsMap[$field])) {
                $field = $fieldsMap[$field];
            }
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
     * Test successful product update
     *
     * @param array $productDataForUpdate
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @dataProvider dataProviderTestUpdateSuccessful()
     */
    public function testUpdateSuccessful($productDataForUpdate)
    {
        unset($productDataForUpdate['type']);
        unset($productDataForUpdate['set']);
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callPut($this->_getResourcePath($product->getId()), $productDataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        /** @var $updatedProduct Mage_Catalog_Model_Product */
        $updatedProduct = Mage::getModel('catalog/product')
            ->load($product->getId())
            ->clearInstance()
            ->setStoreId(Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID)
            ->load($product->getId());
        // Validate URL Key - all special chars should be replaced with dash sign
        $productDataForUpdate['url_key'] = '123-abc';
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

        return array(
            array($productDataSpecialChars),
            array($productData),
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
        unset($productDataForUpdate['type']);
        unset($productDataForUpdate['set']);
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
        unset($productDataForUpdate['type']);
        unset($productDataForUpdate['set']);
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
            . 'Please avoid spaces or other characters such as dots or commas.',
            'Please use numbers only in the "max_sale_qty" field in the "stock_data" set. '
            . 'Please avoid spaces or other characters such as dots or commas.',
            'Please use numbers only in the "qty_increments" field in the "stock_data" set. '
            . 'Please avoid spaces or other characters such as dots or commas.',
            'Invalid "backorders" value in the "stock_data" set.',
            'Invalid "is_in_stock" value in the "stock_data" set.',
            'Please enter a number 0 or greater in the "gift_wrapping_price" field.',
            'Resource data pre-validation error.',
        );
        $invalidValueAttributes = array('status', 'visibility', 'msrp_enabled', 'msrp_display_actual_price_type',
            'enable_googlecheckout', 'tax_class_id', 'custom_design', 'page_layout', 'options_container',
            'gift_message_available', 'gift_wrapping_available');
        foreach ($invalidValueAttributes as $attribute) {
            $expectedErrors[] = sprintf('Invalid value for attribute "%s".', $attribute);
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
     * Test product update resource with not unique sku value
     * Negative test.
     *
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Catalog/_fixtures/product_simple_all_fields.php
     */
    public function testPostNotUniqueSku()
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
            $this->assertEquals(strtotime($expectedData[$attribute]), strtotime($product->getData($attribute)),
                $attribute .' is not equal.');
        }
        $exclude = array_merge($dateAttributes, array('group_price', 'tier_price', 'stock_data'));
        $productAttributes = array_diff_key($expectedData, array_flip($exclude));
        foreach ($productAttributes as $attribute => $value) {
            $this->assertEquals($value, $product->getData($attribute), $attribute .' is not equal.');
        }
        if (isset($expectedData['stock_data'])) {
            $stockItem = $product->getStockItem();
            foreach ($expectedData['stock_data'] as $attribute => $value) {
                $this->assertEquals($value, $stockItem->getData($attribute), $attribute .' is not equal.');
            }
        }
    }
}
