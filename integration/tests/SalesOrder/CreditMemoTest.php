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
 * @magentoDataFixture SalesOrder/_fixtures/invoice.php
 */
class SalesOrder_CreditMemoTest extends Magento_Test_Webservice
{
    /**
     * Test sales order credit memo list, info, create, cancel
     *
     * @return void
     */
    public function testCRUD()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('creditmemo/product_virtual');

        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('creditmemo/order');

        $orderItems = $order->getAllItems();
        $qtys = array();

        /** @var $orderItem Mage_Sales_Model_Order_Item */
        foreach ($orderItems as $orderItem) {
            $qtys[] = array('order_item_id' => $orderItem->getId(), 'qty' => 1);
        }
        $adjustmentPositive = 2;
        $adjustmentNegative = 1;
        $data = array(
            'qtys'                => $qtys,
            'adjustment_positive' => $adjustmentPositive,
            'adjustment_negative' => $adjustmentNegative
        );
        $orderIncrementalId = $order->getIncrementId();

        //Test create
        $creditMemoIncrementId = $this->call('order_creditmemo.create', array($orderIncrementalId, $data));

        $this->assertNotEmpty($creditMemoIncrementId, 'Creditmemo was not created');

        //Test list
        $creditmemoList = $this->call('order_creditmemo.list');
        $this->assertInternalType('array', $creditmemoList);
        $this->assertNotEmpty($creditmemoList, 'Creditmemo list is empty');

        //Test add comment
        $commentText = 'Creditmemo comment';
        $this->assertTrue($this->call('order_creditmemo.addComment', array($creditMemoIncrementId, $commentText)));

        //Test info
        $creditmemoInfo = $this->call('order_creditmemo.info', array($creditMemoIncrementId));
        $this->assertInternalType('array', $creditmemoInfo);
        $this->assertNotEmpty($creditmemoInfo);
        $this->assertEquals($creditMemoIncrementId, $creditmemoInfo['increment_id']);

        //Test adjustments fees were added
        $this->assertEquals($adjustmentPositive, $creditmemoInfo['adjustment_positive']);
        $this->assertEquals($adjustmentNegative, $creditmemoInfo['adjustment_negative']);

        //Test order items were refunded
        $this->assertArrayHasKey('items', $creditmemoInfo);
        $this->assertInternalType('array', $creditmemoInfo['items']);
        $this->assertGreaterThan(0, count($creditmemoInfo['items']));

        $this->assertEquals($creditmemoInfo['items'][0]['order_item_id'], $qtys[0]['order_item_id']);
        $this->assertEquals($product->getId(), $creditmemoInfo['items'][0]['product_id']);

        //Test comment was added correctly
        $this->assertArrayHasKey('comments', $creditmemoInfo);
        $this->assertInternalType('array', $creditmemoInfo['comments']);
        $this->assertGreaterThan(0, count($creditmemoInfo['comments']));
        $this->assertEquals($commentText, $creditmemoInfo['comments'][0]['comment']);

        //Test cancel
        //Situation when creditmemo is possible to cancel was not found
        $this->setExpectedException('Exception');
        $this->call('order_creditmemo.cancel', array($creditMemoIncrementId));
    }

    /**
     * Test Exception when refund amount greater than available to refund amount
     *
     * @expectedException Exception
     * @return void
     */
    public function testNegativeRefundException()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('creditmemo/order');
        $overRefundAmount = $order->getGrandTotal() + 10;

        $this->call('order_creditmemo.create', array($order->getIncrementId(), array(
            'adjustment_positive' => $overRefundAmount
        )));
    }

    /**
     * Test filtered list empty if filter contains incorrect order id
     *
     * @return void
     */
    public function testListEmptyFilter()
    {
        $filter = array('order_id' => 'invalid-id');
        if (self::$_ws instanceof Magento_Test_Webservice_SoapV2) {
            $filter = new stdClass();
            $filter->filter = array();
            $filter->filter[] = (object)array('key' => 'order_id', 'value' => 'invalid-id');
        }

        $creditmemoList = $this->call('order_creditmemo.list', array($filter));
        $this->assertEquals(0, count($creditmemoList));
    }

    /**
     * Test Exception on invalid creditmemo create data
     *
     * @expectedException Exception
     * @return void
     */
    public function testCreateInvalidOrderException()
    {
        $this->call('order_creditmemo.create', array('invalid-id', array()));
    }

    /**
     * Test Exception on invalid credit memo while adding comment
     *
     * @expectedException Exception
     * @return void
     */
    public function testAddCommentInvalidOrderException()
    {
        $this->call('order_creditmemo.addComment', array('invalid-id', 'Comment'));
    }

    /**
     * Test Exception on invalid credit memo while getting info
     *
     * @expectedException Exception
     * @return void
     */
    public function testInfoInvalidOrderException()
    {
        $this->call('order_creditmemo.info', array('invalid-id'));
    }

    /**
     * Test exception on invalid credit memo cancel
     *
     * @expectedException Exception
     * @return void
     */
    public function testCancelInvalidIdException()
    {
        $this->call('order_creditmemo.cancel', array('invalid-id'));
    }
}
