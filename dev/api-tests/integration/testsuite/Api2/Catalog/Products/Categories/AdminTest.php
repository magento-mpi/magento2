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
     * @resourceOperation product_category::create
     */
    public function testPost()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource post
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostMultiAssign()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $categoryCreatedData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryCreatedData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $restResponse2 = $this->callPost($this->_getResourcePath($product->getId()), $categoryCreatedData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse2->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource post with empty required field
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostEmptyRequiredField()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryEmptyRequired.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $expectedErrors = array(
            'category_id: Value is required and can\'t be empty',
            'Resource data pre-validation error.'
        );
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrors);
    }

    /**
     * Test product category resource post with invalid categoryId
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostInvalidData()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryInvalidData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $expectedErrors = array(
            'category_id: Please use numbers only in "category_id" field.',
            'Resource data pre-validation error.'
        );
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrors);
    }

    /**
     * Test product category resource post with nonexistent categoryId
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostCategoryNotExists()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryNotExistsData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $this->_checkErrorMessagesInResponse($restResponse, 'Category not found',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource post for nonexistent product
     *
     * @resourceOperation product_category::create
     */
    public function testPostWrongProductId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        $restResponse = $this->callPost($this->_getResourcePath('INVALID_PRODUCT'), $categoryData);
        $this->_checkErrorMessagesInResponse($restResponse, 'Resource not found.',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource post for already assigned category
     *
     * @resourceOperation product_category::create
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

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $this->_checkErrorMessagesInResponse($restResponse,
            'Product #' . $product->getId() . ' is already assigned to category #' . $categoryData['category_id']);
    }

    /**
     * Test product category resource assigning to category tree root
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostToCategoryTreeRoot()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryTreeRootData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callPost($this->_getResourcePath($product->getId()), $categoryData);
        $this->_checkErrorMessagesInResponse($restResponse, 'Cannot assign product to tree root category.');
    }

    /**
     * Test product categories list
     *
     * @resourceOperation product_category::multiget
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

        $restResponse = $this->callGet($this->_getResourcePath($product->getId()));
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
     * @resourceOperation product_category::multiget
     */
    public function testListWrongProductId()
    {
        $restResponse = $this->callGet($this->_getResourcePath('INVALID_ID'));
        $this->_checkErrorMessagesInResponse($restResponse, 'Resource not found.',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource delete
     *
     * @resourceOperation product_category::delete
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

        $restResponse = $this->callDelete($this->_getResourcePath($product->getId(), $categoryData['category_id']));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('Mage_Catalog_Model_Product')->load($product->getId());
        $this->assertNotNull($product->getId());
        self::setFixture('product_simple', $product);
        $this->assertNotContains($categoryData['category_id'], $product->getCategoryIds());
        $this->assertContains($categoryCreatedData['category_id'], $product->getCategoryIds());
    }

    /**
     * Test product category resource delete for nonexistent product
     *
     * @resourceOperation product_category::delete
     */
    public function testDeleteWrongProductId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';
        $restResponse = $this->callDelete($this->_getResourcePath('INVALID_ID', $categoryData['category_id']));
        $this->_checkErrorMessagesInResponse($restResponse, 'Resource not found.',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource delete for nonexistent category
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::delete
     */
    public function testDeleteWrongCategoryId()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryInvalidData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callDelete($this->_getResourcePath($product->getId(), $categoryData['category_id']));
        $this->_checkErrorMessagesInResponse($restResponse, 'Category not found',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource delete for unassigned category
     *
     * @magentoDataFixture Api2/Catalog/Products/Categories/_fixtures/product_simple.php
     * @resourceOperation product_category::delete
     */
    public function testDeleteNotAssigned()
    {
        $categoryData = require dirname(__FILE__) . '/_fixtures/Backend/ProductCategoryData.php';

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callDelete($this->_getResourcePath($product->getId(), $categoryData['category_id']));
        $this->_checkErrorMessagesInResponse($restResponse,
            'Product #' . $product->getId() . ' isn\'t assigned to category #' . $categoryData['category_id']);
    }

    /**
     * Create path to resource
     *
     * @param int $productId
     * @param int $categoryId
     * @return string
     */
    protected function _getResourcePath($productId, $categoryId = null)
    {
        $path = "products/{$productId}/categories";
        if ($categoryId) {
            $path .= "/{$categoryId}";
        }
        return $path;
    }
}
