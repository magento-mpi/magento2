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
 * Test product categories resource
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */

class Api2_Catalog_Products_Categories_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product_simple', true);
        parent::tearDown();
    }

    /**
     * Delete store fixture
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);
        self::deleteFixture('category', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test product category resource post
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testPost()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/categories/' . $product->getId(), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource post
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testPostMultiAssign()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/categories/' . $product->getId(), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $restResponse2 = $this->callPost('products/categories/' . $product->getId(), $categoryCreatedData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse2->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource post with empty required field
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testPostEmptyRequiredField()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryEmptyRequired.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/categories/' . $product->getId(), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Empty value for "category_id" in request.',
            'Resource data pre-validation error.'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource post with nonexistent categoryId
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testPostInvalideData()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryInvalideData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/categories/' . $product->getId(), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Category not found'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource post for nonexistent product
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testPostWrongProductId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost('products/categories/' . ($product->getId() + 1), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Product not found'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource post for already assigned category
     */
    public function testPostAlreadyAssigned()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require $fixturesDir . '/Catalog/Product.php';
        $product->setStoreId(0)
            ->setCategoryIds($categoryData['category_id'])
            ->save();
        self::setFixture('product_simple', $product);

        $restResponse = $this->callPost('products/categories/' . $product->getId(), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Product #' . $product->getId() . ' is already assigned to category #' . $categoryData['category_id']
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product categories list
     */
    public function testList()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require $fixturesDir . '/Catalog/Product.php';
        $product->setStoreId(0)
            ->setCategoryIds($categoryData['category_id'] . ',' . $categoryCreatedData['category_id'])
            ->save();
        self::setFixture('product_simple', $product);

        $restResponse = $this->callGet('products/categories/' . $product->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $originalData = $product->getCategoryIds();
        $this->assertEquals(count($responseData), count($originalData));
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product categories resource list for nonexistent product
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testListWrongProductId()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callGet('products/categories/' . ($product->getId() + 1));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Product not found'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource delete
     */
    public function testDelete()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        $fixturesDir = realpath(dirname(__FILE__) . '/../../../../../fixtures');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require $fixturesDir . '/Catalog/Product.php';
        $product->setStoreId(0)
            ->setCategoryIds($categoryData['category_id'] . ',' . $categoryCreatedData['category_id'])
            ->save();

        $resourceUri = 'products/categories/' . $product->getId() . '/category/' . $categoryData['category_id'];
        $restResponse = $this->callDelete($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertNotContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource delete for nonexistent product
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testDeleteWrongProductId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $resourceUri = 'products/categories/' . ($product->getId() + 1) . '/category/' . $categoryData['category_id'];
        $restResponse = $this->callDelete($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Product not found'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource delete for nonexistent category
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testDeleteWrongCategoryId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryInvalideData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $resourceUri = 'products/categories/' . $product->getId() . '/category/' . $categoryData['category_id'];
        $restResponse = $this->callDelete($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Category not found'
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test product category resource delete for unassigned category
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     */
    public function testDeleteNotAssigned()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $resourceUri = 'products/categories/' . $product->getId() . '/category/' . $categoryData['category_id'];
        $restResponse = $this->callDelete($resourceUri);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $expectedErrors = array(
            'Product #' . $product->getId() . ' isn\'t assigned to category #' . $categoryData['category_id']
        );

        $this->assertEquals(count($expectedErrors), count($errors));
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }
}
