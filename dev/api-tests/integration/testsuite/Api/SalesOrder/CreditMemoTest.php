<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Api_SalesOrder_CreditMemoTest extends Magento_Test_Webservice
{
    /**
     * Remove all created models
     */
    protected function tearDown()
    {
        $this->deleteFixture('invoice', true);
        $this->deleteFixture('invoice2', true);
        $this->deleteFixture('order2', true);
        $this->deleteFixture('quote2', true);
        $this->deleteFixture('order', true);
        $this->deleteFixture('quote', true);
        $this->deleteFixture('product_virtual', true);
        $this->deleteFixture('customer_address', true);
        $this->deleteFixture('customer', true);

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
     * Test sales order credit memo list, info, create, cancel
     *
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testCRUD()
    {
        $creditmemoInfo = $this->_createCreditmemo();
        list($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId) = $creditmemoInfo;

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
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
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
     */
    public function testListEmptyFilter()
    {
        $filter = array('order_id' => 'invalid-id');
        if (self::$_adapterRegistry[self::$_defaultAdapterCode] instanceof Magento_Test_Webservice_SoapV2) {
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
     */
    public function testInfoInvalidOrderException()
    {
        $this->call('order_creditmemo.info', array('creditmemoIncrementId' => 'invalid-id'));
    }

    /**
     * Test exception on invalid credit memo cancel
     *
     * @expectedException DEFAULT_EXCEPTION
     */
    public function testCancelInvalidIdException()
    {
        $this->call('order_creditmemo.cancel', array('creditmemoIncrementId' => 'invalid-id'));
    }

    /**
     * Test credit memo create API call results
     *
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     */
    public function testAutoIncrementType()
    {
        // Set creditmemo increment id prefix
        $website = Mage::app()->getWebsite();
        $storeId = $website->getDefaultStore()->getId();
        $entityTypeModel = Mage::getModel('Mage_Eav_Model_Entity_Type')->loadByCode('creditmemo');
        $entityStoreModel = Mage::getModel('Mage_Eav_Model_Entity_Store')
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

    /**
     * Test order creditmemo list. With filters
     *
     * @magentoDataFixture testsuite/Api/SalesOrder/_fixture/invoice.php
     * @magentoAppIsolation enabled
     * @depends testCRUD
     */
    public function testListWithFilters()
    {
        $creditmemoInfo = $this->_createCreditmemo();
        list($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId) = $creditmemoInfo;

        /** @var $creditmemo Mage_Sales_Model_Order_Creditmemo */
        $creditmemo = Mage::getModel('Mage_Sales_Model_Order_Creditmemo')->load($creditMemoIncrementId, 'increment_id');

        if (self::_isSoapV2()) {
            $filters = array('filters' => array(
                'filter' => array(
                    array('key' => 'state', 'value' => $creditmemo->getData('state')),
                    array('key' => 'created_at', 'value' => $creditmemo->getData('created_at'))
                ),
                'complex_filter' => array(
                    array(
                        'key'   => 'creditmemo_id',
                        'value' => array('key' => 'in', 'value' => array($creditmemo->getId(), 0))
                    ),
                )
            ));
        } else {
            $filters = array(array(
                'state' => array('0', $creditmemo->getData('state')),
                'created_at' => $creditmemo->getData('created_at'),
                'creditmemo_id' => array('in' => array($creditmemo->getId(), 0))
            ));
        }
        $result = $this->call('order_creditmemo.list', $filters);

        if (!isset($result[0])) { // workaround for WS-I
            $result = array($result);
        }
        $this->assertInternalType('array', $result, "Response has invalid format");
        $this->assertEquals(1, count($result), "Invalid creditmemos quantity received");
        foreach(reset($result) as $field => $value) {
            if ($field == 'creditmemo_id') {
                // process field mapping
                $field = 'entity_id';
            }
            $this->assertEquals($creditmemo->getData($field), $value, "Field '{$field}' has invalid value");
        }
    }

    /**
     * Check if SOAP API is testsd
     *
     * @return bool
     */
    protected static function _isSoapV2()
    {
        return TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2 || TESTS_WEBSERVICE_TYPE == self::TYPE_SOAPV2_WSI;
    }

    /**
     * Create creditmemo using API. Invoice fixture must be initialized for this method
     *
     * @return array
     */
    protected function _createCreditmemo()
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
            'qtys' => $qtys,
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
        return array($product, $qtys, $adjustmentPositive, $adjustmentNegative, $creditMemoIncrementId);
    }
}
