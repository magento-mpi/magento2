<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoDataFixture Api/Mage/SalesOrder/_fixture/shipment.php
 */
class SalesOrder_ShipmentTest extends SalesOrder_AbstractTest
{
    /**
     * Clean up shipment and revert changes to entity store model
     *
     * @return void
     */
    protected function tearDown()
    {
        $shipment = Mage::getModel('Mage_Sales_Model_Order_Shipment');
        $shipment->loadByIncrementId(self::getFixture('shipmentIncrementId'));
        $this->callModelDelete($shipment, true);

        $this->_restoreIncrementIdPrefix();
        parent::tearDown();
    }

    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = self::getFixture('order');

        $id = $order->getIncrementId();

        // Create new shipment
        $newShipmentId = $this->call(
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => true,
                'includeComment' => true
            )
        );
        self::setFixture('shipmentIncrementId', $newShipmentId);

        // View new shipment
        $shipment = $this->call(
            'sales_salesOrderShipmentInfo',
            array(
                'shipmentIncrementId' => $newShipmentId
            )
        );

        $this->assertEquals($newShipmentId, $shipment['increment_id']);
    }

    /**
     * Test shipment create API call results
     *
     * @return void
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = self::getFixture('quote');
        //Create order
        $quoteService = new Mage_Sales_Model_Service_Quote($quote);
        //Set payment method to check/money order
        $quoteService->getQuote()->getPayment()->setMethod('checkmo');
        $order = $quoteService->submitOrder();
        $order->place();
        $order->save();
        $id = $order->getIncrementId();

        // Set shipping increment id prefix
        $prefix = '01';
        $this->_setIncrementIdPrefix('shipment', $prefix);

        // Create new shipment
        $newShipmentId = $this->call(
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => true,
                'includeComment' => true
            )
        );
        self::setFixture('shipmentIncrementId', $newShipmentId);

        $this->assertTrue(is_string($newShipmentId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $newShipmentId, 'Increment Id returned by API is not correct');
    }

    /**
     * Test send shipping info API
     *
     * @return void
     */
    public function testSendInfo()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = self::getFixture('quote');
        //Create order
        $quoteService = new Mage_Sales_Model_Service_Quote($quote);
        //Set payment method to check/money order
        $quoteService->getQuote()->getPayment()->setMethod('checkmo');
        $order = $quoteService->submitOrder();
        $order->place();
        $order->save();
        $id = $order->getIncrementId();

        // Create new shipment
        $newShipmentId = $this->call(
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => false,
                'includeComment' => true
            )
        );
        $this->assertGreaterThan(0, strlen($newShipmentId));
        self::setFixture('shipmentIncrementId', $newShipmentId);

        // Send info
        $isOk = $this->call(
            'salesOrderShipmentSendInfo',
            array(
                'shipmentIncrementId' => $newShipmentId,
                'comment' => $id
            )
        );

        $this->assertTrue((bool)$isOk);
    }
}
