<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoDataFixture Mage/Sales/Model/Order/Api/_files/shipment.php
 */
class Mage_Sales_Model_Order_Shipment_ApiTest extends PHPUnit_Framework_TestCase
{
    public function testCRUD()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::registry('order');

        $id = $order->getIncrementId();

        // Create new shipment
        $newShipmentId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => true,
                'includeComment' => true
            )
        );
        Mage::register('shipmentIncrementId', $newShipmentId);

        // View new shipment
        $shipment = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderShipmentInfo',
            array(
                'shipmentIncrementId' => $newShipmentId
            )
        );

        $this->assertEquals($newShipmentId, $shipment['increment_id']);
    }

    /**
     * Test shipment create API call results
     */
    public function testAutoIncrementType()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::registry('quote');
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
        Magento_Test_Helper_Api::setIncrementIdPrefix('shipment', $prefix);

        // Create new shipment
        $newShipmentId = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $id,
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => true,
                'includeComment' => true
            )
        );
        Mage::unregister('shipmentIncrementId');
        Mage::register('shipmentIncrementId', $newShipmentId);

        $this->assertTrue(is_string($newShipmentId), 'Increment Id is not a string');
        $this->assertStringStartsWith($prefix, $newShipmentId, 'Increment Id returned by API is not correct');
    }

    /**
     * Test send shipping info API
     */
    public function testSendInfo()
    {
        /** @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::registry('quote');
        //Create order
        $quoteService = new Mage_Sales_Model_Service_Quote($quote);
        //Set payment method to check/money order
        $quoteService->getQuote()->getPayment()->setMethod('checkmo');
        $order = $quoteService->submitOrder();
        $order->place();
        $order->save();
        $id = $order->getIncrementId();

        // Create new shipment
        $newShipmentId = Magento_Test_Helper_Api::call(
            $this,
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
        Mage::unregister('shipmentIncrementId');
        Mage::register('shipmentIncrementId', $newShipmentId);

        // Send info
        $isOk = Magento_Test_Helper_Api::call(
            $this,
            'salesOrderShipmentSendInfo',
            array(
                'shipmentIncrementId' => $newShipmentId,
                'comment' => $id
            )
        );

        $this->assertTrue((bool)$isOk);
    }
}
