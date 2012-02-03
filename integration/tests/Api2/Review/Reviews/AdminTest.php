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
 * Test reviews collection
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
/**
 * @magentoDataFixture Api/SalesOrder/_fixtures/product_simple.php
 */

class Api2_Review_Reviews_AdminTest extends Magento_Test_Webservice_Rest_Admin
{
    /**
     * Tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        $product = $this->getFixture('product_simple');
        $this->callModelDelete($product, true);
        $review = $this->getFixture('review');
        $this->callModelDelete($review, true);

        parent::tearDown();
    }

    /**
     * Test creating new review
     *
     * @return void
     */
    public function testPost()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->getFixture('product_simple');

        $reviewData = require dirname(__FILE__) . '/_fixtures/ReviewData.php';
        $reviewData['admin']['create']['product_id'] = $product->getId();

        $this->getWebService()->getClient()->setHeaders('Cookie', 'XDEBUG_SESSION=netbeans-xdebug');

        $restResponse = $this->callPost('reviews', $reviewData['admin']['create']);
        $this->assertEquals(200, $restResponse->getStatus());
        // Get created review id from Location header and check that it has been saved correctly
        $location = $restResponse->getHeader('Location2');
        list($reviewId) = array_reverse(explode('/', $location));
        /** @var $review Mage_Review_Model_Review */
        $review = Mage::getModel('review/review')->load($reviewId);
        $this->setFixture('review', $review);
        $this->assertEquals($reviewData['admin']['create']['status_id'], $review->getStatusId());
        $this->assertEquals($reviewData['admin']['create']['nickname'], $review->getNickname());
        $this->assertEquals($reviewData['admin']['create']['title'], $review->getTitle());
        $this->assertEquals($reviewData['admin']['create']['detail'], $review->getDetail());
    }
}

