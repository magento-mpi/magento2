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
 * @magentoDataFixture Api/SalesOrder/_fixtures/invoice.php
 */

class Api_SalesOrder_CreditMemoTest extends Magento_Test_Webservice
{
    /**
     * tear down
     *
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        $entityStoreModel = self::getFixture('entity_store_model');
        if ($entityStoreModel instanceof Mage_Eav_Model_Entity_Store) {
            $origIncrementData = self::getFixture('orig_creditmemo_increment_data');
            $entityStoreModel->loadByEntityStore($entityStoreModel->getEntityTypeId(), $entityStoreModel->getStoreId());
            $entityStoreModel->setIncrementPrefix($origIncrementData['prefix'])
                ->save();
        }
    }

    /**
     * Delete created fixtures
     */
    static public function tearDownAfterClass()
    {
        self::deleteFixture('order', true);
        self::deleteFixture('product_virtual', true);
    }

    /**
     * Test sales order credit memo list, info, create, cancel
     *
     * @return void
     */
    public function testCRUD()
    {
        /** @var $product Mage_Catalog_Model_Product */
        $product = self::getFixture('product_virtual');

        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

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
        $creditMemoIncrementId = $this->call('order_creditmemo.create', array(
            'creditmemoIncrementId' => $orderIncrementalId,
            'creditmemoData' => $data
        ));

        $this->assertNotEmpty($creditMemoIncrementId, 'Creditmemo was not created');

        //Test list
        $creditmemoList = $this->call('order_creditmemo.list');
        $this->assertInternalType('array', $creditmemoList);
        $this->assertNotEmpty($creditmemoList, 'Creditmemo list is empty');

        //Test add comment
        $commentText = 'Creditmemo comment';
        $this->assertTrue((bool) $this->call('order_creditmemo.addComment', array(
            'creditmemoIncrementId' => $creditMemoIncrementId,
            'comment' => $commentText
        )));

        //Test info
        $creditmemoInfo = $this->call('order_creditmemo.info', array(
            'creditmemoIncrementId' => $creditMemoIncrementId
        ));

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

        if (!isset($creditmemoInfo['items'][0])) { // workaround for WSI plain array response
            $creditmemoInfo['items'] = array($creditmemoInfo['items']);
        }

        $this->assertEquals($creditmemoInfo['items'][0]['order_item_id'], $qtys[0]['order_item_id']);
        $this->assertEquals($product->getId(), $creditmemoInfo['items'][0]['product_id']);

        if (!isset($creditmemoInfo['comments'][0])) { // workaround for WSI plain array response
            $creditmemoInfo['comments'] = array($creditmemoInfo['comments']);
        }

        //Test comment was added correctly
        $this->assertArrayHasKey('comments', $creditmemoInfo);
        $this->assertInternalType('array', $creditmemoInfo['comments']);
        $this->assertGreaterThan(0, count($creditmemoInfo['comments']));
        $this->assertEquals($commentText, $creditmemoInfo['comments'][0]['comment']);

        //Test cancel
        //Situation when creditmemo is possible to cancel was not found
        $this->setExpectedException(self::DEFAULT_EXCEPTION);
        $this->call('order_creditmemo.cancel', array('creditmemoIncrementId' => $creditMemoIncrementId));
    }

    /**
     * Test Exception when refund amount greater than available to refund amount
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testNegativeRefundException()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');
        $overRefundAmount = $order->getGrandTotal() + 10;

        $this->call('order_creditmemo.create', array(
            'creditmemoIncrementId' => $order->getIncrementId(),
            'creditmemoData' => array(
                'adjustment_positive' => $overRefundAmount
            )
        ));
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
            $filter = array(
                'filter' => array(array('key' => 'order_id', 'value' => 'invalid-id'))
            );
        }

        $creditmemoList = $this->call('order_creditmemo.list', array('filters' => $filter));
        $this->assertEquals(0, count($creditmemoList));
    }

    /**
     * Test Exception on invalid creditmemo create data
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testCreateInvalidOrderException()
    {
        $this->call('order_creditmemo.create', array(
            'creditmemoIncrementId' => 'invalid-id',
            'creditmemoData' => array()
        ));
    }

    /**
     * Test Exception on invalid credit memo while adding comment
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testAddCommentInvalidOrderException()
    {
        $this->call('order_creditmemo.addComment', array(
            'creditmemoIncrementId' => 'invalid-id',
            'comment' => 'Comment'
        ));
    }

    /**
     * Test Exception on invalid credit memo while getting info
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testInfoInvalidOrderException()
    {
        $this->call('order_creditmemo.info', array('creditmemoIncrementId' => 'invalid-id'));
    }

    /**
     * Test exception on invalid credit memo cancel
     *
     * @expectedException DEFAULT_EXCEPTION
     * @return void
     */
    public function testCancelInvalidIdException()
    {
        $this->call('order_creditmemo.cancel', array('creditmemoIncrementId' => 'invalid-id'));
    }

    /**
     * Test credit memo create API call results
     *
     * @return void
     */
    public function testAutoIncrementType()
    {
        // Set creditmemo increment id prefix
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('eav/entity_type')->loadByCode('creditmemo');
        $entityStoreModel = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($entityTypeModel->getId(), $storeId);
        $prefix = $entityStoreModel->getIncrementPrefix() == null ? $storeId : $entityStoreModel->getIncrementPrefix();
        self::setFixture('orig_creditmemo_increment_data', array(
            'prefix' => $prefix,
            'increment_last_id' => $entityStoreModel->getIncrementLastId()
        ));
        $entityStoreModel->setEntityTypeId($entityTypeModel->getId());
        $entityStoreModel->setStoreId($storeId);
        $entityStoreModel->setIncrementPrefix('01');
        $entityStoreModel->save();
        self::setFixture('entity_store_model', $entityStoreModel);

        $order = self::getFixture('order2');

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
        $creditMemoIncrementId = $this->call('order_creditmemo.create', array(
            'creditmemoIncrementId' => $orderIncrementalId,
            'creditmemoData' => $data
        ));
        self::setFixture('creditmemoIncrementId', $creditMemoIncrementId);

        $this->assertTrue(is_string($creditMemoIncrementId), 'Increment Id is not a string');
        $this->assertStringStartsWith($entityStoreModel->getIncrementPrefix(), $creditMemoIncrementId,
            'Increment Id returned by API is not correct');
    }
}
