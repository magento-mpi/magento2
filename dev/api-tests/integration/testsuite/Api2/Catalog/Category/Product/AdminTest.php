<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test category products resource for admin
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Catalog_Category_Product_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        self::deleteFixture('product_simple', true);
        self::deleteFixture('product_simple_all_fields', true);
        $assignedProducts = self::getFixture('assigned_products');
        if (!is_null($assignedProducts)) {
            foreach ($assignedProducts as $product) {
                self::addModelToDelete($product, true);
            }
        }
        self::deleteFixture('category', true);
        parent::tearDown();
    }

    /**
     * Store-related fixtures removal
     */
    public static function tearDownAfterClass()
    {
        self::deleteFixture('store_on_new_website', true);
        self::deleteFixture('store_group', true);
        self::deleteFixture('website', true);
        self::deleteFixture('category_on_new_website', true);
        parent::tearDownAfterClass();
    }

    /**
     * Test unsuccessful get. Get is not implemented
     *
     * @resourceOperation category_product::get
     */
    public function testGet()
    {
        $restResponse = $this->callGet($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID, 'product'));
        $expectedErrorMessage = "Request does not match any route.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage,
            Mage_Api2_Model_Server::HTTP_NOT_FOUND);
    }

    /**
     * Test successful assigned products list with data
     *
     * @magentoDataFixture fixture/Catalog/Category/category_with_assigned_products.php
     * @resourceOperation category_product::multiget
     */
    public function testList()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $this->_testList($category);
    }

    /**
     * Test successful empty assigned products list get
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category_product::multiget
     */
    public function testListWithoutAssignedProducts()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $this->_testList($category);
    }

    /**
     * Test list of associated products with invalid category ID specified
     *
     * @resourceOperation category_product::multiget
     */
    public function testListInvalidCategory()
    {
        $restResponse = $this->callGet($this->_getResourcePath('invalid_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test successful products assign to category
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple_all_fields.php
     * @resourceOperation category_product::create
     */
    public function testPost()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        /** @var $productAllFields Mage_Catalog_Model_Product */
        $productAllFields = $this->getFixture('product_simple_all_fields');
        $assignedProducts = array(
            array('product_id' => $product->getId(), 'position' => 999),
            array('product_id' => $productAllFields->getId()),
        );

        foreach ($assignedProducts as $assignedProduct) {
            $restResponse = $this->callPost($this->_getResourcePath($category->getId()), $assignedProduct);
            $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(),
                "Invalid response code received.");
        }

        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        $realAssignedProducts = $updatedCategory->getProductsPosition();
        $this->_checkAssignedProducts($assignedProducts, $realAssignedProducts);
    }

    /**
     * Test unsuccessful product assign to category
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @dataProvider dataProviderForPostInvalidData
     * @param array $testData
     * @resourceOperation category_product::create
     */
    public function testPostInvalidData($testData)
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $errorMessages = array();
        foreach ($testData['data'] as $assignedProduct) {
            $assignedProduct = array_merge(array('product_id' => $product->getId()), $assignedProduct);
            $restResponse = $this->callPost($this->_getResourcePath($category->getId()), $assignedProduct);
            // not all requests are expected to return 400 code that is why the status cannot be checked here
            $body = $restResponse->getBody();
            $errors = isset($body['messages']['error']) ? $body['messages']['error'] : array();
            foreach ($errors as $error) {
                $errorMessages[] = $error['message'];
            }
        }
        $this->_checkErrorMessages($testData, $errorMessages);
    }

    /**
     * Test post associated products with invalid category ID specified
     *
     * @resourceOperation category_product::create
     */
    public function testPostInvalidCategory()
    {
        $assignedProduct = array('product_id' => 1);
        $restResponse = $this->callPost($this->_getResourcePath('invalid_id'), $assignedProduct);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test post associated products to the category tree root
     *
     * @resourceOperation category_product::create
     */
    public function testPostToTreeRoot()
    {
        $assignedProduct = array('product_id' => 1);
        $restResponse = $this->callPost($this->_getResourcePath(Mage_Catalog_Model_Category::TREE_ROOT_ID),
            $assignedProduct);
        $expectedErrorMessage = "Products cannot be assigned to the category tree root.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test post associated products without product ID specified
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category_product::create
     */
    public function testPostWithoutProductId()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProduct = array('position' => 1);
        $restResponse = $this->callPost($this->_getResourcePath($category->getId()), $assignedProduct);
        $expectedErrorMessage = "The product ID must be specified.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test successful product assign to category with store specified.
     * In addition check if float position is saved correctly
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::create
     */
    public function testPostWithStoreSpecified()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $assignedProduct = array('product_id' => $product->getId(), 'position' => "999.9");
        $storeId = Mage::app()->getDefaultStoreView()->getId();
        $restResponse = $this->callPost($this->_getResourcePath($category->getId(), null, $storeId), $assignedProduct);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(),
            "Invalid response code received.");
        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->setStoreId($storeId)->load($category->getId());
        $realAssignedProducts = $updatedCategory->getProductsPosition();
        $expectedAssignedProducts = array(array_merge($assignedProduct, array(
            'position' => intval($assignedProduct['position']))));
        $this->_checkAssignedProducts($expectedAssignedProducts, $realAssignedProducts);
    }

    /**
     * Test unsuccessful product assign to category with store. If product is not assigned to specified store
     *
     * @magentoDataFixture fixture/Catalog/Category/category_on_new_website.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::create
     */
    public function testPostInStoreWithNotAssignedProduct()
    {
        /** @var $categoryOnNewWebsite Mage_Catalog_Model_Category */
        $categoryOnNewWebsite = $this->getFixture('category_on_new_website');
        /** @var $storeOnNewWebsite Mage_Core_Model_Store */
        $storeOnNewWebsite = Magento_Test_Webservice::getFixture('store_on_new_website');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $assignedProducts = array(
            array('product_id' => $product->getId(), 'position' => 999),
        );
        $restResponse = $this->callPost($this->_getResourcePath($categoryOnNewWebsite->getId(), null,
            $storeOnNewWebsite->getId()), reset($assignedProducts));
        $expectedErrorMessage = "Product with the specified ID does not exist in the specified store.";
                $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful product assign to category with store. If category is not assigned to specified store
     *
     * @magentoDataFixture fixture/Catalog/Category/category_on_new_website.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::create
     */
    public function testPostInStoreWithNotAssignedCategory()
    {
        /** @var $categoryOnNewWebsite Mage_Catalog_Model_Category */
        $categoryOnNewWebsite = $this->getFixture('category_on_new_website');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $assignedProducts = array(
            array('product_id' => $product->getId(), 'position' => 999),
        );
        $restResponse = $this->callPost($this->_getResourcePath($categoryOnNewWebsite->getId(), null,
            Mage::app()->getDefaultStoreView()->getId()), reset($assignedProducts));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test successful put
     *
     * @magentoDataFixture fixture/Catalog/Category/category_with_assigned_products.php
     * @resourceOperation category_product::update
     */
    public function testPut()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProducts = $category->getProductsPosition();
        $updatedProductId = reset(array_keys($assignedProducts));
        $updatedPosition = '555';
        $assignedProducts[$updatedProductId] = $updatedPosition;
        $dataForUpdate = array('position' => $updatedPosition);

        $restResponse = $this->callPut($this->_getResourcePath($category->getId(), $updatedProductId), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        $realAssignedProducts = $updatedCategory->getProductsPosition();
        $this->_checkAssignedProducts($assignedProducts, $realAssignedProducts);
    }

    /**
     * Test put associated products with invalid category ID specified
     *
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::update
     */
    public function testPutInvalidCategory()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $assignedProducts = array('position' => 1);
        $restResponse = $this->callPut($this->_getResourcePath('invalid_id', $product->getId()), $assignedProducts);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test put associated products with invalid positions specified
     *
     * @magentoDataFixture fixture/Catalog/Category/category_with_assigned_products.php
     * @resourceOperation category_product::update
     */
    public function testPutInvalidPositions()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProducts = $category->getProductsPosition();
        $productId = reset(array_keys($assignedProducts));
        $invalidPositions = array(
            array('position' => null),
            array('position' => -1),
            array('position' => 'invalid_position'),
        );
        foreach ($invalidPositions as $invalidPositionData) {
            $restResponse = $this->callPut($this->_getResourcePath($category->getId(), $productId),
                $invalidPositionData);
            $expectedErrorMessage = "The 'position' value for the product with ID {$productId} "
                . "must be set and must be a positive integer.";
            $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
        }
    }

    /**
     * Test put associated products with invalid product ID specified
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category_product::update
     */
    public function testPutInvalidProduct()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProducts = array('position' => 1);
        $restResponse = $this->callPut($this->_getResourcePath($category->getId(), 'invalid_id'), $assignedProducts);
        $expectedErrorMessage = "The 'product_id' value is invalid or product with such ID does not exist.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful product position in category update
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::update
     */
    public function testPutUnassignedProduct()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $unassignedProduct = array('position' => 999);
        $restResponse = $this->callPut($this->_getResourcePath($category->getId(), $product->getId()),
            $unassignedProduct);
        $expectedErrorMessage = "The product position in the category cannot be updated "
            . "as the product is not assigned to the category.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test successful unassign
     *
     * @magentoDataFixture fixture/Catalog/Category/category_with_assigned_products.php
     * @resourceOperation category_product::delete
     */
    public function testDelete()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProducts = $category->getProductsPosition();
        $deletedProductId = reset(array_keys($assignedProducts));
        unset($assignedProducts[$deletedProductId]);

        $restResponse = $this->callDelete($this->_getResourcePath($category->getId(), $deletedProductId));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        /** @var $updatedCategory Mage_Catalog_Model_Category */
        $updatedCategory = Mage::getModel('Mage_Catalog_Model_Category')->load($category->getId());
        $realAssignedProducts = $updatedCategory->getProductsPosition();
        $this->_checkAssignedProducts($assignedProducts, $realAssignedProducts);
    }

    /**
     * Test unassign associated products with invalid category ID specified
     *
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::delete
     */
    public function testDeleteInvalidCategory()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath('invalid_id', $product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus(),
            "Response code is invalid.");
    }

    /**
     * Test unassign associated products with invalid product ID specified
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @resourceOperation category_product::delete
     */
    public function testDeleteInvalidProduct()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        $assignedProducts = array('position' => 1);
        $restResponse = $this->callDelete($this->_getResourcePath($category->getId(), 'invalid_id'), $assignedProducts);
        $expectedErrorMessage = "The 'product_id' value is invalid or product with such ID does not exist.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Test unsuccessful product unassign from category in case when it is not assigned to the specified category
     *
     * @magentoDataFixture fixture/Catalog/Category/category.php
     * @magentoDataFixture fixture/Catalog/Product/Simple/product_simple.php
     * @resourceOperation category_product::delete
     */
    public function testDeleteUnassignedProduct()
    {
        /** @var $category Mage_Catalog_Model_Category */
        $category = $this->getFixture('category');
        /** @var $unassignedProduct Mage_Catalog_Model_Product */
        $unassignedProduct = $this->getFixture('product_simple');
        $restResponse = $this->callDelete($this->_getResourcePath($category->getId(), $unassignedProduct->getId()));

        $expectedErrorMessage = "The product cannot be unassigned from the specified category "
            . "as it is not assigned to it.";
        $this->_checkErrorMessagesInResponse($restResponse, $expectedErrorMessage);
    }

    /**
     * Provider of invalid data for post
     *
     * @return array
     * @dataSetNumber 6
     */
    public function dataProviderForPostInvalidData()
    {
        return array(
            array(array('data' => array(array('position' => 'invalid_position')),
                'message_patterns' => array(
                    array('pattern' => "/The 'position' value for the product with ID .+ must be a positive integer "
                        . "or may not be set\./", 'matches_count' => 1)
                )
            )),
            array(array('data' => array(array('position' => -1)),
                'message_patterns' => array(
                    array('pattern' => "/The 'position' value for the product with ID .+ must be a positive integer "
                        . "or may not be set\./", 'matches_count' => 1)
                )
            )),
            array(array('data' => array(array('product_id' => 'invalid_product_id', 'position' => 0)),
                'messages' => array("The 'product_id' value is invalid or product with such ID does not exist.")
            )),
            array(array('data' => array(array('product_id' => 'invalid_product_id', 'position' => 'invalid_position')),
                'messages' => array("The 'product_id' value is invalid or product with such ID does not exist.")
            )),
            array(array(
                'data' => array(
                    array('product_id' => 'invalid_product_id', 'position' => 'invalid_position'),
                    array('position' => 'invalid_position')
                ),
                'message_patterns' => array(
                    array('pattern' => "/The 'position' value for the product with ID .+ must be a positive integer "
                        . "or may not be set\./", 'matches_count' => 1),
                    array('pattern' => "/The 'product_id' value is invalid or product with such ID "
                        . "does not exist\./", 'matches_count' => 1),
                )
            )),
            array(array('data' => array(array('position' => '12'), array('position' => '13')),
                'message_patterns' => array(
                    array('pattern' => '/The product with ID .+ is already assigned to the specified category\./',
                        'matches_count' => 1),
                )
            ),
            ));
    }

    /**
     * Test successful get of assigned to category products list
     *
     * @param Mage_Catalog_Model_Category $category
     */
    protected function _testList(Mage_Catalog_Model_Category $category)
    {
        $assignedProducts = $category->getProductsPosition();

        $restResponse = $this->callGet($this->_getResourcePath($category->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $assignedProductsFromResponse = $restResponse->getBody();
        $this->assertInternalType('array', $assignedProductsFromResponse, 'Response has invalid format.');
        $this->_checkAssignedProducts($assignedProducts, $assignedProductsFromResponse);
    }

    /**
     * Check if assigned products are correct. Parameters could be passed in two formats
     *
     * @param array $expectedAssignedProducts
     * @param array $assignedProducts
     */
    protected function _checkAssignedProducts($expectedAssignedProducts, $assignedProducts)
    {
        $expectedAssignedProducts = $this->_formatAssignedProducts($expectedAssignedProducts);
        $assignedProducts = $this->_formatAssignedProducts($assignedProducts);

        $this->assertCount(count($expectedAssignedProducts), $assignedProducts,
            "Products quantity assigned to category is invalid.");
        foreach ($expectedAssignedProducts as $expectedAssignedProduct) {
            $productId = $expectedAssignedProduct['product_id'];
            $position = isset($expectedAssignedProduct['position']) ? $expectedAssignedProduct['position'] : 0;
            $found = false;
            foreach ($assignedProducts as $assignedProduct) {
                if ($productId == $assignedProduct['product_id']) {
                    $found = true;
                    $this->assertEquals($position, $assignedProduct['position'],
                        "Position of product with ID $productId is invalid.");
                }
            }
            $this->assertTrue($found, "Product with ID $productId not found.");
        }
    }

    /**
     * Bring array of assigned products to format: array(array('product_id' => $productId, 'position' => $position), ..)
     *
     * @param array $assignedProducts
     * @return array
     */
    protected function _formatAssignedProducts($assignedProducts)
    {
        if (!isset($assignedProducts[0])) {
            $formattedAssignedProducts = array();
            foreach ($assignedProducts as $productId => $position) {
                $formattedAssignedProducts[] = array('product_id' => $productId, 'position' => $position);
            }
        } else {
            $formattedAssignedProducts = $assignedProducts;
        }
        return $formattedAssignedProducts;
    }

    /**
     * Create path to resource
     *
     * @param string $categoryId
     * @param null|string $productId
     * @param null|string $storeId
     * @return string
     */
    protected function _getResourcePath($categoryId, $productId = null, $storeId = null)
    {
        $path = "categories/$categoryId/products";
        if (!is_null($productId)) {
            $path .= "/$productId";
        }
        if (!is_null($storeId)) {
            $path .= "/store/$storeId";
        }
        return $path;
    }
}
