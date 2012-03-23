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
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for review item API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Review_Review_GuestTest extends Magento_Test_Webservice_Rest_Guest
{
    /**
     * Remove fixtures
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $this->deleteFixture('review', true);

        parent::tearDown();
    }

    /**
     * Remove store fixture
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('store', true);

        parent::tearDownAfterClass();
    }

    /**
     * Test successful retrieving of existing product review
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGet()
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getUrl($product->getId(), $review->getId()));

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $reviewOriginalData = $review->getData();

        //asserts

        $this->assertEquals($reviewOriginalData['review_id'], $responseData['review_id']);
        $this->assertEquals($reviewOriginalData['created_at'], $responseData['created_at']);
        $this->assertEquals($reviewOriginalData['title'], $responseData['title']);
        $this->assertEquals($reviewOriginalData['detail'], $responseData['detail']);
        $this->assertEquals($reviewOriginalData['nickname'], $responseData['nickname']);
        $this->assertEquals(count($reviewOriginalData['stores']), count($responseData['stores']));
        foreach ($responseData['stores'] as $store) {
            $this->assertContains($store, $reviewOriginalData['stores']);
        }
        $this->assertEquals('0%', $responseData['rating_summary']);
        $this->assertEquals(array(), $responseData['detailed_rating']);
    }

    /**
     * Test retrieving not existing review item
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGetUnavailableResource()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getUrl($product->getId(), 'invalid_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test retrieve of existing not approved review
     *
     * @param int $status
     * @dataProvider providerTestGetWithInappropriateStatus
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGetWithInappropriateStatus($status)
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $review->setStatusId($status)->save();

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callGet($this->_getUrl($product->getId(), $review->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Provider of not eligible for guest review statuses
     *
     * @return array
     */
    public function providerTestGetWithInappropriateStatus()
    {
        return array(
            array(Mage_Review_Model_Review::STATUS_NOT_APPROVED),
            array(Mage_Review_Model_Review::STATUS_PENDING),
        );
    }

    /**
     * Guest should not be able to remove any review
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callDelete($this->_getUrl($product->getId(), ''));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Guest should not be able to update any review
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdate()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callPut($this->_getUrl($product->getId(), 'any_id'), array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $restResponse->getStatus());
    }

    /**
     * Build URL for review resource
     *
     * @param int $productId
     * @param int $reviewId
     * @return string
     */
    protected function _getUrl($productId, $reviewId = null)
    {
        return 'products/' . $productId . '/reviews/' . $reviewId;
    }

    /**
     * Test successful review creation
     *
     * @param array $reviewData
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Review/_fixtures/store.php
     * @dataProvider dataProviderTestPost
     */
    public function testPost($reviewData)
    {
        $reviewData['stores'] = array($this->getFixture('store')->getId());

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost("products/{$product->getId()}/reviews", $reviewData);

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location');
        list($reviewId) = array_reverse(explode('/', $location));

        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);

        $this->setFixture('review', $review);
        $this->assertEquals($reviewData['nickname'], $review->getNickname());
        $this->assertEquals($reviewData['title'], $review->getTitle());
        $this->assertEquals($reviewData['detail'], $review->getDetail());
    }

    /**
     * Data provider for testPost()
     *
     * @return array
     */
    public function dataProviderTestPost()
    {
        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewData.php';
        $reviewDataSqlInjection = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewDataSqlInj.php';
        return array(
            array($reviewData),
            array($reviewDataSqlInjection),
        );
    }

    /**
     * Test successful review creation on custom store
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Review/_fixtures/store.php
     */
    public function testPostCustomStore()
    {
        $this->_getAppCache()->flush();

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewData.php';
        $reviewData['product_id'] = $product->getId();
        $reviewData['stores'] = array($store->getId());

        $restResponse = $this->callPost("products/{$product->getId()}/reviews", $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location');
        list($reviewId) = array_reverse(explode('/', $location));

        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);

        $this->setFixture('review', $review);
        $this->assertEquals($store->getId(), $review->getStoreId());
        $this->assertEquals($reviewData['nickname'], $review->getNickname());
        $this->assertEquals($reviewData['title'], $review->getTitle());
        $this->assertEquals($reviewData['detail'], $review->getDetail());
    }

    /**
     * Test unsuccessful review creation on store with disabled review creation by guests
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostDisabledReviewCreationByGuests()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewData.php';
        $reviewData['product_id'] = $product->getId();

        $this->_updateAppConfig('catalog/review/allow_guest', 0);
        $restResponse = $this->callPost("products/{$product->getId()}/reviews", $reviewData);
        $this->_updateAppConfig('catalog/review/allow_guest', 1);
        $this->_cleanAppConfigCache();
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Test creating new review with invalid data.
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostEmptyRequired()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewDataEmptyRequired.php';
        $reviewData['product_id'] = $product->getId();

        // update review using API
        $restResponse = $this->callPost("products/{$product->getId()}/reviews", $reviewData);

        // check error code
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();

        //Mage::dump($body); exit;

        $expectedErrors = array(
            'Resource data pre-validation error.',
            "nickname: Value is required and can't be empty",
            "nickname: Invalid type given. String expected",
            "title: Value is required and can't be empty",
            "title: Invalid type given. String expected",
            "detail: Value is required and can't be empty",
            "detail: Invalid type given. String expected",
            "Invalid detailed_rating data provided.",
            "Missed stores data."
        );

        // check error messages
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $this->assertCount(count($expectedErrors), $errors);
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test creating new review with invalid product.
     * Negative test.
     */
    public function testPostInvalidProduct()
    {
        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataInvalidProduct.php';

        $restResponse = $this->callPost('products/invalid_id/reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Product not found.');
    }

    /**
     * Test creating new review with invalid store (not existing store given).
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostInvalidStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewDataInvalidStores.php';
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost("products/{$product->getId()}/reviews", $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided.');
    }

    /**
     * Test retrieving list of reviews
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function testGetCollection()
    {
        $reviewsList = $this->getFixture('reviews_list');

        //simple product
        $productSimpleId = $this->getFixture('product_simple')->getId();

        $this->_getAppCache()->flush();
        $restResponseSimple = $this->callGet($this->_getUrl($productSimpleId), array(
            'order' => 'review_id',
            'dir'   => Zend_Db_Select::SQL_DESC
        ));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponseSimple->getStatus());
        $body = $restResponseSimple->getBody();
        $this->assertNotEmpty($body);
        $responseReviewsSimple = array();
        foreach ($body as $responseReview) {
            $responseReviewsSimple[$responseReview['review_id']] = $responseReview;
        }

        foreach ($reviewsList as $review) {
            if ($review->getEntityPkValue()!==$productSimpleId) {
                continue;
            }
            $this->assertTrue(
                isset($responseReviewsSimple[$review->getId()]),
                'Review created from fixture was not found in response'
            );
        }

        //virtual product
        $productVirtualId = $this->getFixture('product_virtual')->getId();

        $restResponseVirtual = $this->callGet($this->_getUrl($productVirtualId), array(
            'order' => 'review_id',
            'dir'   => Zend_Db_Select::SQL_DESC
        ));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponseVirtual->getStatus());
        $body = $restResponseVirtual->getBody();
        $this->assertNotEmpty($body);
        $responseReviewsVirtual = array();
        foreach ($body as $responseReview) {
            $responseReviewsVirtual[$responseReview['review_id']] = $responseReview;
        }

        foreach ($reviewsList as $review) {
            if ($review->getEntityPkValue()!==$productVirtualId) {
                continue;
            }
            $this->assertTrue(
                isset($responseReviewsVirtual[$review->getId()]),
                'Review created from fixture was not found in response'
            );

            /** @var $reviewStatus Mage_Review_Model_Review_Status */
            $reviewStatus = Mage::getModel('review/review_status')->load($review->getStatusId());
        }
    }

    /**
     * Test retrieving list of reviews
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function testGetCollectionCustomStore()
    {
        $this->_getAppCache()->flush();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Magento_Test_Webservice::getFixture('product_virtual');
        /** @var $store Mage_Core_Model_Store */
        $store = Magento_Test_Webservice::getFixture('store');
        $restResponse = $this->callGet($this->_getUrl($product->getId()),
            array(
                'order' => 'review_id',
                'store_id' => $store->getId(),
                'product_id' => $product->getId()
        ));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $this->assertCount(1, $body, 'There should be 1 review assigned to custom store in this test.');
    }

    /**
     * Test retrieving list of reviews without product filter.
     * Negative test.
     */
    public function testGetCollectionNoProductFilter()
    {
        $restResponse = $this->callGet($this->_getUrl('invalid_product_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid product');
    }

    /**
     * Test retrieving list of reviews with product filter
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function _testGetCollectionProductFilter()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Magento_Test_Webservice::getFixture('product_simple');

        $restResponse = $this->callGet('reviews', array('product_id' => $product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $reviews = $restResponse->getBody();
        $this->assertNotEmpty($reviews);
        $this->assertCount(2, $reviews, 'There should be 2 reviews posted to the simple product in this test');

        foreach ($reviews as $review) {
            $this->assertEquals($product->getId(), $review['product_id'], 'Review is not for correct product');
        }
    }

    /**
     * Test invalid product id in filter
     */
    public function _testGetCollectionProductFilterInvalid()
    {
        $restResponse = $this->callGet('reviews', array('product_id' => 'INVALID PRODUCT'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid product');
    }
}
