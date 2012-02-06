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
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        $restResponse = $this->callGet('review/' . $review->getId());

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);
        $reviewOriginalData = $review->getData();
        foreach ($responseData as $field => $value) {
            if (!is_array($value)) {
                $this->assertEquals($reviewOriginalData[$field], $value);
            } else {
                $this->assertEquals(count($reviewOriginalData[$field]), count($value));
            }
        }
    }

    /**
     * Test retrieving not existing review item
     */
    public function testGetUnavailableResource()
    {
        $restResponse = $this->callGet('review/' . 'invalid_id');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test successful review item removal
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testDelete()
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        // check if review item exists
        $existingReview = Mage::getModel('review/review');
        $existingReview->load($review->getId());
        $this->assertNotEmpty($existingReview->getId());
        // delete review using API
        $restResponse = $this->callDelete('review/' . $review->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // check if review item was deleted
        $removedReview = Mage::getModel('review/review');
        $removedReview->load($review->getId());
        $this->assertEmpty($removedReview->getId());
    }

    /**
     * Test removing not existing review item
     */
    public function testDeleteUnavailableResource()
    {
        $restResponse = $this->callDelete('review/' . 'invalid_id');
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
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');
        // update review using API
        $restResponse = $this->callPut('review/' . $review->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // check if review item was updated
        $updatedReview = Mage::getModel('review/review');
        $updatedReview->load($review->getId());
        $updatedReviewData = $updatedReview->getData();
        foreach ($dataForUpdate as $field => $value) {
            $this->assertEquals($value, $updatedReviewData[$field]);
        }
    }

    /**
     * Data provider for testUpdate()
     *
     * @return array
     */
    public function dataProviderTestUpdate()
    {
        $validReviewData = array(
            'nickname' => 'Updated nickname',
            'title' => 'Updated title',
            'detail' => 'Updated detail',
            'status_id' => Mage_Review_Model_Review::STATUS_PENDING,
            'stores' => array(0)
        );
        $validReviewDataSqlInjection = require dirname(__FILE__) . '/../_fixtures/ReviewDataSqlInj.php';
        // unset fields that could not be used for SQL-injections
        unset($validReviewDataSqlInjection['product_id']);
        unset($validReviewDataSqlInjection['stores']);
        unset($validReviewDataSqlInjection['status_id']);

        return array(array($validReviewData), array($validReviewDataSqlInjection));
    }

    /**
     * Test updating not existing review item
     */
    public function testUpdateUnavailableResource()
    {
        $restResponse = $this->callPut('review/' . 'invalid_id', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test unsuccessful review item update with empty required data
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateEmptyRequired()
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/ReviewDataEmptyRequired.php';
        unset($dataForUpdate['product_id']);

        // update review using API
        $restResponse = $this->callPut('review/' . $review->getId(), $dataForUpdate);

        // check error code
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        // check error messages
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $expectedErrors = array('Resource data pre-validation error.');
        foreach ($dataForUpdate as $key => $value) {
            $expectedErrors[] = sprintf('Empty value for "%s" in request.', $key);
        }
        $this->assertEquals(count($expectedErrors), count($errors));
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
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/ReviewDataInvalidStatus.php';
        unset($dataForUpdate['product_id']);

        // update review using API
        $restResponse = $this->callPut('review/' . $review->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $errors = $body['messages']['error'];
        $this->assertNotEmpty($errors);
        $error = reset($errors);
        $expectedErrorMessage = 'Invalid status provided';
        $this->assertEquals($error['message'], $expectedErrorMessage);
    }

    /**
     * Test unsuccessful review item update with invalid stores
     *
     * @magentoDataFixture Api2/Review/_fixtures/review.php
     */
    public function testUpdateInvalidStores()
    {
        /** @var $product Mage_Review_Model_Review */
        $review = $this->getFixture('review');

        $dataForUpdate = require dirname(__FILE__) . '/../_fixtures/ReviewDataInvalidStores.php';
        unset($dataForUpdate['product_id']);

        // update review using API
        $restResponse = $this->callPut('review/' . $review->getId(), $dataForUpdate);
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_BAD_REQUEST, $restResponse->getStatus());
        $body = $restResponse->getBody();
        $error = reset($body['messages']['error']);
        $this->assertEquals($error['message'], 'Invalid stores provided');
    }
}

