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
 * Test for reviews collection API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Review_Reviews_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        $this->deleteFixture('product_simple', true);
        $this->deleteFixture('review', true);
        $reviewsList = $this->getFixture('reviews_list');
        if ($reviewsList && count($reviewsList)) {
            foreach ($reviewsList as $review) {
                $this->callModelDelete($review, true);
            }
        }

        parent::tearDown();
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
        $reviewData['product_id'] = $product->getId();

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location2');
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

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $expectedErrors = array('Resource data pre-validation error.');
        unset($reviewData['product_id']);
        foreach ($reviewData as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
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

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Product not found');
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

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid status provided');
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

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided');
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

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided');
    }

    /**
     * Test retrieving list of reviews
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function testGet()
    {
        $restResponse = $this->callGet('reviews', array('order' => 'review_id', 'dir' => 'DESC'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $this->assertNotEmpty($body);
        $responseReviews = array();
        foreach ($body as $responseReview) {
            $responseReviews[$responseReview['review_id']] = $responseReview;
        }

        $reviewsList = $this->getFixture('reviews_list');
        foreach ($reviewsList as $review) {
            $this->assertTrue(isset($responseReviews[$review->getId()]),
                'Review created from fixture was not found in response');
            $this->assertEquals($review->getEntityPkValue(), $responseReviews[$review->getId()]['product_id']);
            $this->assertEquals($review->getStatusId(), $responseReviews[$review->getId()]['status_id']);
        }
    }

    /**
     * Test retrieving list of reviews with product filter
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function testGetProductFilter()
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
    public function testGetProductFilterInvalid()
    {
        $restResponse = $this->callGet('reviews', array('product_id' => 'INVALID PRODUCT'));
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
    public function testGetStatusFilter()
    {
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
    public function testGetStatusFilterInvalid()
    {
        $restResponse = $this->callGet('reviews', array('status_id' => 'INVALID STATUS'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid status provided');
    }
}

