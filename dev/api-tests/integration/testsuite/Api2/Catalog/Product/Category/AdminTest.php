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

class Api2_Catalog_Product_Category_AdminTest extends Magento_Test_Webservice_Rest_Admin
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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPost()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');

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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostMultiAssign()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');
        $categoryCreatedData = $this->_loadCategoryFixtureData('product_category_created_data');

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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostEmptyRequiredField()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_empty_required');

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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostInvalidData()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_invalid_data');

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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostCategoryNotExists()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_not_exists_data');

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
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');

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
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require TEST_FIXTURE_DIR . '/_block/Catalog/Product.php';
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
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::create
     */
    public function testPostToCategoryTreeRoot()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_tree_root_data');

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
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');
        $categoryCreatedData = $this->_loadCategoryFixtureData('product_category_created_data');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require TEST_FIXTURE_DIR . '/_block/Catalog/Product.php';
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
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');
        $categoryCreatedData = $this->_loadCategoryFixtureData('product_category_created_data');

        /* @var $productFixture Mage_Catalog_Model_Product */
        $product = require TEST_FIXTURE_DIR . '/_block/Catalog/Product.php';
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
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');
        $restResponse = $this->callDelete($this->_getResourcePath('INVALID_ID', $categoryData['category_id']));
        $this->_checkErrorMessagesInResponse($restResponse, 'Resource not found.',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource delete for nonexistent category
     *
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::delete
     */
    public function testDeleteWrongCategoryId()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_invalid_data');

        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_simple');

        $restResponse = $this->callDelete($this->_getResourcePath($product->getId(), $categoryData['category_id']));
        $this->_checkErrorMessagesInResponse($restResponse, 'Category not found',
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test product category resource delete for unassigned category
     *
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation product_category::delete
     */
    public function testDeleteNotAssigned()
    {
        $categoryData = $this->_loadCategoryFixtureData('product_category_data');

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

    /**
     * Load category fixture data
     * 
     * @param string $fixtureName
     * @return array
     */
    protected function _loadCategoryFixtureData($fixtureName)
    {
        return require TEST_FIXTURE_DIR . "/_data/Catalog/Product/Category/{$fixtureName}.php";
    }
}
