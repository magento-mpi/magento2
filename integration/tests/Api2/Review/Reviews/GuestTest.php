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
class Api2_Review_Reviews_GuestTest extends Magento_Test_Webservice_Rest_Guest
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
     * Delete store fixture after test case
     */
    public static function tearDownAfterClass()
    {
        Magento_TestCase::deleteFixture('store', true);

        parent::tearDownAfterClass();
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
        $this->getWebservice()->getClient()->setHeaders('Cookie', 'XDEBUG_SESSION=PHPSTORM');

        $restResponse = $this->callPost('reviews', $reviewData);
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
     * Test unsuccessful review creation on store with disabled review creation by guests
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @magentoConfigFixture catalog/review/allow_guest 0
     */
    public function testPostDisabledReviewCreationByGuests()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewData.php';
        $reviewData['product_id'] = $product->getId();

        /** @var $coreConfig Mage_Core_Model_Config */
        $coreConfig = Mage::getModel('core/config');
        $coreConfig->saveConfig('catalog/review/allow_guest', 0);
        $restResponse = $this->callPost('reviews', $reviewData);
        $coreConfig->saveConfig('catalog/review/allow_guest', 1);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_METHOD_NOT_ALLOWED, $restResponse->getStatus());
    }

    /**
     * Test successful review creation on custom store
     *
     * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
     * @magentoDataFixture Api2/Review/_fixtures/store.php
     */
    public function testPostCustomStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');
        /** @var $store Mage_Core_Model_Store */
        $store = $this->getFixture('store');

        $reviewData = require dirname(__FILE__) . '/../_fixtures/Frontend/ReviewData.php';
        $reviewData['product_id'] = $product->getId();
        $reviewData['store_id'] = $store->getId();

        $restResponse = $this->callPost('reviews', $reviewData);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location');
        list($reviewId) = array_reverse(explode('/', $location));
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);
        $this->setFixture('review', $review);
        $this->assertEquals($reviewData['store_id'], $review->getStoreId());
        $this->assertEquals($reviewData['nickname'], $review->getNickname());
        $this->assertEquals($reviewData['title'], $review->getTitle());
        $this->assertEquals($reviewData['detail'], $review->getDetail());
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
        /** @var $product Mage_Catalog_Model_Product */
        $product = Magento_Test_Webservice::getFixture('product_simple');

        $restResponse = $this->callGet('reviews', array('order' => 'review_id', 'product_id' => $product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $this->assertNotEmpty($body);
        $responseReviews = array();
        foreach ($body as $responseReview) {
            $responseReviews[$responseReview['review_id']] = $responseReview;
        }

        $reviewsList = $this->getFixture('reviews_list');
        foreach ($reviewsList as $review) {
            if ($review->getEntityPkValue() == $product->getId()) {
                $this->assertTrue(isset($responseReviews[$review->getId()]),
                    'Review created from fixture was not found in response');
                $this->assertEquals($review->getEntityPkValue(), $responseReviews[$review->getId()]['product_id']);
                $this->assertEquals($review->getStatusId(), $responseReviews[$review->getId()]['status_id']);
            } else {
                $this->assertFalse(isset($responseReviews[$review->getId()]),
                    'Review created from fixture should not be present in response');
            }
        }
    }

    /**
     * Test retrieving list of reviews
     *
     * @magentoDataFixture Api2/Review/_fixtures/reviews_list.php
     */
    public function testGetCustomStore()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = Magento_Test_Webservice::getFixture('product_virtual');
        /** @var $store Mage_Core_Model_Store */
        $store = Magento_Test_Webservice::getFixture('store');
        $restResponse = $this->callGet('reviews', array('order' => 'review_id', 'store_id' => $store->getId(),
            'product_id' => $product->getId()));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $this->assertCount(1, $body, 'There should be 1 review assigned to custom store in this test.');
    }

    /**
     * Test retrieving list of reviews without product filter.
     * Negative test.
     */
    public function testGetNoProductFilter()
    {
        $restResponse = $this->callGet('reviews');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Product id is required');
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
}

