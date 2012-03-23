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
class Api2_Review_Review_AdminTest extends Magento_Test_Webservice_Rest_Admin
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
     * Test retrieving existing product review item
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testGet()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $restResponse = $this->callGet($this->_getUrl($product->getId(), $review->getId()));

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $reviewOriginalData = $review->getData();

        //asserts

        /** @var $reviewStatus Mage_Review_Model_Review_Status */
        $reviewStatus = Mage::getModel('review/review_status')->load($reviewOriginalData['status_id']);

        $this->assertEquals($reviewOriginalData['review_id'], $responseData['review_id']);
        $this->assertEquals($reviewOriginalData['created_at'], $responseData['created_at']);
        $this->assertEquals($reviewOriginalData['product_id'], $responseData['entity_pk_value']);
        $this->assertEquals($reviewOriginalData['title'], $responseData['title']);
        $this->assertEquals($reviewOriginalData['detail'], $responseData['detail']);
        $this->assertEquals($reviewOriginalData['nickname'], $responseData['nickname']);
        $this->assertEquals($reviewStatus->getStatusCode(), $responseData['status']);
        $this->assertEquals(count($reviewOriginalData['stores']), count($responseData['stores']));
        foreach ($responseData['stores'] as $store) {
            $this->assertContains($store, $reviewOriginalData['stores']);
        }
        $this->assertEquals('Guest', $responseData['posted_by']);
        $this->assertEquals('Guest', $responseData['user_type']);
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
     * Test successful review item removal
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        // check if review item exists
        $existingReview = Mage::getModel('review/review');
        $existingReview->load($review->getId());
        $this->assertNotEmpty($existingReview->getId());

        // delete review using API
        $restResponse = $this->callDelete($this->_getUrl($product->getId(), $review->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        // check if review item was deleted
        $removedReview = Mage::getModel('review/review');
        $removedReview->load($review->getId());
        $this->assertEmpty($removedReview->getId());
    }

    /**
     * Test removing not existing review item
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testDeleteUnavailableResource()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callDelete($this->_getUrl($product->getId(), 'invalid_id'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful review item update
     *
     * @param array $dataForUpdate
     * @dataProvider dataProviderTestUpdate
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdate($dataForUpdate)
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        // update review using API
        $restResponse = $this->callPut($this->_getUrl($product->getId(), $review->getId()), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // check if review item was updated
        $updatedReview = Mage::getModel('review/review');
        $updatedReview->load($review->getId());
        foreach ($dataForUpdate as $field => $value) {
            if ($updatedReview->hasData($field)) {
                $this->assertEquals($updatedReview->getData($field), $value);
            } elseif ('status' == $field) {
                /** @var $reviewStatus Mage_Review_Model_Review_Status */
                $reviewStatus = Mage::getModel('review/review_status')->load($value, 'status_code');
                $this->assertEquals($updatedReview->getStatusId(), $reviewStatus->getId());
            }
        }
    }

    /**
     * Data provider for testUpdate()
     *
     * @return array
     */
    public function dataProviderTestUpdate()
    {
        /** @var $reviewStatus Mage_Review_Model_Review_Status */
        $reviewStatus = Mage::getModel('review/review_status')->load(Mage_Review_Model_Review::STATUS_PENDING);

        $validReviewData = array(
            'nickname' => 'Updated nickname',
            'title' => 'Updated title',
            'detail' => 'Updated detail',
            'status' => $reviewStatus->getStatusCode(),
            'stores' => array(0)
        );
        $validReviewDataSqlInjection = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataSqlInj.php';
        // unset fields that could not be used for SQL-injections
        unset($validReviewDataSqlInjection['product_id']);
        unset($validReviewDataSqlInjection['stores']);
        unset($validReviewDataSqlInjection['status']);
        unset($validReviewDataSqlInjection['status_id']);

        return array(array($validReviewData), array($validReviewDataSqlInjection));
    }

    /**
     * Test updating not existing review item
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateUnavailableResource()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callPut("products/{$product->getId()}/reviews/invalid_id", array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test updating review item of non existing product item
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateInvalidProductReview()
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $restResponse = $this->callPut($this->_getUrl('invalid_id', $review->getId()), array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful review item update with empty required data
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateEmptyRequired()
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataEmptyRequired.php';
        unset($dataForUpdate['product_id']);

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        // update review using API
        $restResponse = $this->callPut($this->_getUrl($product->getId(), $review->getId()), $dataForUpdate);

        // check error code
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());

        $expectedErrors = array('Resource data pre-validation error.');
        foreach ($dataForUpdate as $key => $value) {
            if ('stores' != $key) {
                $expectedErrors[] = sprintf('%s: Invalid type given. String expected', $key);
            } else {
                $expectedErrors[] = 'Invalid stores provided.';
            }
        }

        // check error messages
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);

        $this->assertCount(count($expectedErrors), $errors);
        foreach ($errors as $error) {
            $this->assertContains($error['message'], $expectedErrors);
        }
    }

    /**
     * Test unsuccessful review item update with invalid status
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateInvalidStatus()
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataInvalidStatus.php';
        unset($dataForUpdate['product_id']);

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        // update review using API
        $restResponse = $this->callPut($this->_getUrl($product->getId(), $review->getId()), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $error = reset($errors);
        $expectedErrorMessage = 'Invalid status provided.';
        $this->assertEquals($error['message'], $expectedErrorMessage);
    }

    /**
     * Test unsuccessful review item update with invalid stores
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateInvalidStores()
    {
        /** @var $review Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataInvalidStores.php';
        unset($dataForUpdate['product_id']);

        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        // update review using API
        $restResponse = $this->callPut($this->_getUrl($product->getId(), $review->getId()), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided.');
    }

    /**
     * Build URL for review resource
     *
     * @param int $productId
     * @param int|string $reviewId
     * @return string
     */
    protected function _getUrl($productId, $reviewId = '')
    {
        return 'products/' . $productId . '/reviews/' . $reviewId;
    }

    /**
     * Test successful review creation
     *
     * @param array $reviewData
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @dataProvider dataProviderTestPost
     */
    public function testPost($reviewData)
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $restResponse = $this->callPost($this->_getUrl($product->getId()), $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus(), $restResponse->getMessage());
        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location');
        list($reviewId) = array_reverse(explode('/', $location));
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);
        $this->setFixture('review', $review);
        $this->assertEquals($reviewData['status_id'], $review->getStatusId());
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
        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewData.php';
        $reviewDataSqlInjection = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataSqlInj.php';
        return array(
            array($reviewData),
            array($reviewDataSqlInjection),
        );
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

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataEmptyRequired.php';
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost($this->_getUrl($product->getId()), $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $expectedErrors = array(
            'Resource data pre-validation error.',
            "status: Value is required and can't be empty",
            "nickname: Value is required and can't be empty",
            "title: Value is required and can't be empty",
            "detail: Value is required and can't be empty",
            "status: Invalid type given. String expected",
            "nickname: Invalid type given. String expected",
            "title: Invalid type given. String expected",
            "detail: Invalid type given. String expected",
        );
        $this->assertEquals(count($expectedErrors), count($errors));
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

        $restResponse = $this->callPost($this->_getUrl($reviewData['product_id']), $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Product not found.');
    }

    /**
     * Test creating new review with invalid status.
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostInvalidStatus()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataInvalidStatus.php';
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost($this->_getUrl($product->getId()), $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid status provided.');
    }

    /**
     * Test creating new review with invalid stores (not array given).
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostInvalidStores()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewDataInvalidStores.php';
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost($this->_getUrl($product->getId()), $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided.');
    }

    /**
     * Test creating new review with invalid stores (non existing store provided).
     * Negative test.
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     */
    public function testPostInvalidStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Backend/ReviewData.php';
        $reviewData['product_id'] = $product->getId();
        $invalidStoreId = 32000;
        $reviewData['stores'][] = $invalidStoreId;

        $restResponse = $this->callPost($this->_getUrl($product->getId()), $reviewData);
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

            /** @var $reviewStatus Mage_Review_Model_Review_Status */
            $reviewStatus = Mage::getModel('review/review_status')->load($review->getStatusId());

            $this->assertEquals(
                $review->getEntityPkValue(),
                $responseReviewsSimple[$review->getId()]['entity_pk_value']
            );
            $this->assertEquals($reviewStatus->getStatusCode(), $responseReviewsSimple[$review->getId()]['status']);
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

            $this->assertEquals(
                $review->getEntityPkValue(),
                $responseReviewsVirtual[$review->getId()]['entity_pk_value']
            );
            $this->assertEquals($reviewStatus->getStatusCode(), $responseReviewsVirtual[$review->getId()]['status']);
        }
    }

    /**
     * Test retrieving list of reviews with customer filter
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list_customer.php
     */
    public function _testGetCollectionCustomerFilter()
    {
        $productSimple = $this->getFixture('product_simple');

        //$this->_getAppCache()->flush();
        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail(TESTS_CUSTOMER_EMAIL);

        $restResponse = $this->callGet($this->_getUrl($productSimple->getId()), array(
            'filter[0][attribute]' => "customer_id",
            'filter[0][eq]' => $customer->getId(),
            'order'         => 'review_id',
            'dir'           => Zend_Db_Select::SQL_DESC
        ));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $reviews = $restResponse->getBody();
        $this->assertNotEmpty($reviews);
        $reviewsIds = array();
        foreach ($reviews as $review) {
            $reviewsIds[] = $review['review_id'];
        }
        $fixtureReviews = $this->getFixture('reviews_list');

        foreach ($fixtureReviews as $fixtureReview) {
            if ($fixtureReview->getCustomerId() == $customer->getId()) {
                $this->assertContains($fixtureReview->getId(), $reviewsIds,
                    'Review by current customer should be in response');
            } else {
                $this->assertNotContains($fixtureReview->getId(), $reviewsIds,
                    'Review by current customer should NOT be in response');
            }
        }
    }

    /**
     * Test retrieving list of reviews with product filter
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function _testGetCollectionProductFilter()
    {
        $this->_getAppCache()->flush();
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
        $restResponse = $this->callGet('reviews', array('product_id' => 'INVALID_PRODUCT'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid product');
    }

    /**
     * Test retrieving list of reviews with status filter
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function _testGetCollectionStatusFilter()
    {
        $this->_getAppCache()->flush();
        $restResponse = $this->callGet('reviews', array('status_id' => Mage_Review_Model_Review::STATUS_APPROVED));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $reviews = $restResponse->getBody();
        $this->assertNotEmpty($reviews);
        foreach ($reviews as $review) {
            $this->assertEquals(Mage_Review_Model_Review::STATUS_APPROVED, $review['status_id']);
        }
    }

    /**
     * Test invalid status in filter
     */
    public function _testGetCollectionStatusFilterInvalid()
    {
        $restResponse = $this->callGet('reviews', array('status_id' => 'INVALID_STATUS'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid status provided');
    }
}
