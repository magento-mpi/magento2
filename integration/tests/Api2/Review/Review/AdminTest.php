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
 * Test for review item Api2
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
                $this->assertEquals($value, $reviewOriginalData[$field]);
            } else {
                $this->assertEquals(count($value), count($reviewOriginalData[$field]));
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
        $reviewId = $review->getId();
        // check if review item exists
        $existingReview = Mage::getModel('review/review');
        $existingReview->load($reviewId);
        $this->assertNotEmpty($existingReview->getId());
        // delete review using API
        $restResponse = $this->callDelete('review/' . $review->getId());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());
        // check if review item was deleted
        $removedReview = Mage::getModel('review/review');
        $removedReview->load($reviewId);
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
}

