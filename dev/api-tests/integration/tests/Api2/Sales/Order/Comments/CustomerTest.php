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
 * Test for sales order comments API2 by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Order_Comments_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Count of a history items
     */
    const HISTORY_COUNT = 3;

    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer_order', true);
        Magento_Test_Webservice::deleteFixture('customer_quote', true);
        $fixtureProducts = $this->getFixture('customer_products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        Magento_Test_Webservice::deleteFixture('order', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        $fixtureProducts = $this->getFixture('products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Generate sales order comments
     *
     * @param Mage_Sales_Model_Order $order
     * @param int $isVisibleOnFront
     * @return Api2_Sales_Order_Comments_CustomerTest
     */
    protected function _generateCollection(Mage_Sales_Model_Order $order, $isVisibleOnFront = 1)
    {
        $counter = 0;
        while ($counter++ < self::HISTORY_COUNT) {
            /** @var $history Mage_Sales_Model_Order_Status_History */
            $history = require dirname(__FILE__) . '/../../../../../fixtures/Sales/Order/History.php';
            $history->setIsVisibleOnFront($isVisibleOnFront)
                ->setEntityName(Mage_Sales_Model_Order::HISTORY_ENTITY_NAME);
            $order->addStatusHistory($history);
        }
        return $this;
    }

    /**
     * Test create sales order comment
     */
    public function testCreate()
    {
        $response = $this->callPost('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test retrieve sales order comments collection
     *
     * @magentoDataFixture Api2/Sales/_fixtures/customer_order.php
     */
    public function testRetrieve()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        $this->_generateCollection($order);
        $order->save();

        $response = $this->callGet("orders/{$order->getId()}/comments");

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
        $this->assertCount(self::HISTORY_COUNT, $response->getBody());
    }

    /**
     * Test retrieve sales order comments collection
     *
     * @magentoDataFixture Api2/Sales/_fixtures/customer_order.php
     */
    public function testRetrieveVisibleOnFront()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = $this->getFixture('customer_order');

        $this->_generateCollection($order)
            ->_generateCollection($order, 0);
        $order->save();

        $response = $this->callGet("orders/{$order->getId()}/comments");

        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $response->getStatus());
        $this->assertCount(self::HISTORY_COUNT, $response->getBody());
    }

    /**
     * Test retrieve another sales order comments collection
     *
     * @magentoDataFixture Api2/Sales/_fixtures/order.php
     */
    public function testRetrieveForeignResource()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');
        $restResponse = $this->callGet("orders/{$fixtureOrder->getId()}/comments");
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test update action
     */
    public function testUpdate()
    {
        $response = $this->callPut('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }

    /**
     * Test delete action
     */
    public function testDelete()
    {
        $response = $this->callDelete('orders/1/comments', array('qwerty'));
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_FORBIDDEN, $response->getStatus());
    }
}
