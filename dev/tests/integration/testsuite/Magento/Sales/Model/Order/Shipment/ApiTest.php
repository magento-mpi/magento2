<?php
/**
 * Tests for shipment API.
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Magento_Sales_Model_Order_Shipment_ApiTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensure that partial shipment works correctly.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/order_with_shipping.php
     */
    public function testPartialShipmentCreate()
    {
        $order = $this->_getOrderFixture();
        $items = $order->getAllItems();
        $this->assertCount(1, $items, "Exactly one order item was expected to exist.");
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = reset($items);
        $qtyToShip = 3;
        $this->assertGreaterThan(
            $qtyToShip,
            $orderItem->getQtyOrdered(),
            "Product quantity ordered must more than $qtyToShip for this test."
        );
        /** Create partial shipment via API. */
        $shipmentIncrementId = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $this->_getOrderFixture()->getIncrementId(),
                'itemsQty' => array(
                    (object)array('order_item_id' => $orderItem->getId(), 'qty' => $qtyToShip)
                )
            )
        );
        $this->assertGreaterThan(0, (int)$shipmentIncrementId, 'Shipment was not created.');
        /** Ensure that shipment was created partially. */
        $shipment = Mage::getModel('\Magento\Sales\Model\Order\Shipment')->load($shipmentIncrementId, 'increment_id');
        $this->assertEquals(
            $qtyToShip,
            (int)$shipment->getTotalQty(),
            "Items quantity shipped is invalid, partial shipment failed."
        );
    }

    /**
     * Test retrieving the list of shipments related to the order via API.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/shipment.php
     */
    public function testItems()
    {
        /** Prepare data. */
        $shipmentFixture = $this->_getShipmentFixture();
        $filters = array(
            'filters' => (object)array(
                'filter' => array(
                    (object)array('key' => 'increment_id', 'value' => $shipmentFixture->getIncrementId()),
                )
            )
        );

        /** Retrieve list of shipments via API. */
        $shipmentsList = Magento_TestFramework_Helper_Api::call($this, 'salesOrderShipmentList', $filters);

        /** Verify received list of shipments. */
        $this->assertCount(1, $shipmentsList, "Exactly 1 shipment is expected to be in the list results.");
        $fieldsToCompare = array('increment_id', 'total_qty', 'entity_id' => 'shipment_id');
        Magento_TestFramework_Helper_Api::checkEntityFields(
            $this,
            $shipmentFixture->getData(),
            reset($shipmentsList),
            $fieldsToCompare
        );
    }

    /**
     * Test retrieving available carriers for the specified order.
     *
     * @magentoDataFixture Magento/Sales/_files/order.php
     */
    public function testGetCarriers()
    {
        /** Prepare data. */
        /** @var \Magento\Sales\Model\Order $order */
        $order = Mage::getModel('\Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        /** Retrieve carriers list */
        $carriersList = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentGetCarriers',
            array($order->getIncrementId())
        );

        /** Verify carriers list. */
        $this->assertCount(6, $carriersList, "Carriers list contains unexpected quantity of items.");
        $dhlCarrierData = end($carriersList);
        $expectedDhlData = array('key' => 'dhlint', 'value' => 'DHL');
        $this->assertEquals($expectedDhlData, $dhlCarrierData, "Carriers list item is invalid.");
    }

    /**
     * Test adding comment to shipment via API.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/shipment.php
     */
    public function testAddComment()
    {
        /** Add comment to shipment via API. */
        $commentText = 'Shipment test comment.';
        $isAdded = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentAddComment',
            array(
                $this->_getShipmentFixture()->getIncrementId(),
                $commentText,
                true, // should email be sent?
                true, // should comment be included into email body?
            )
        );
        $this->assertTrue($isAdded, "Comment was not added to the shipment.");

        /** Ensure that comment was actually added to the shipment. */
        /** @var \Magento\Sales\Model\Resource\Order\Shipment\Comment\Collection $commentsCollection */
        $commentsCollection = $this->_getShipmentFixture()->getCommentsCollection(true);
        $this->assertCount(1, $commentsCollection->getItems(), "Exactly 1 shipment comment is expected to exist.");
        /** @var \Magento\Sales\Model\Order\Shipment\Comment $comment */
        $comment = $commentsCollection->getFirstItem();
        $this->assertEquals($commentText, $comment->getComment(), 'Comment text was saved to DB incorrectly.');
    }

    /**
     * Test adding and removing tracking information via shipment API.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/shipment.php
     */
    public function testTrackOperations()
    {
        /** Prepare data. */
        $carrierCode = 'ups';
        $trackingTitle = 'Tracking title';
        $trackingNumber = 'N123456';

        /** Add tracking information via API. */
        $trackingNumberId = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentAddTrack',
            array($this->_getShipmentFixture()->getIncrementId(), $carrierCode, $trackingTitle, $trackingNumber)
        );
        $this->assertGreaterThan(0, (int)$trackingNumberId, "Tracking information was not added.");

        /** Ensure that tracking data was saved correctly. */
        $tracksCollection = $this->_getShipmentFixture()->getTracksCollection();
        $this->assertCount(1, $tracksCollection->getItems(), "Tracking information was not saved to DB.");
        /** @var \Magento\Sales\Model\Order\Shipment\Track $track */
        $track = $tracksCollection->getFirstItem();
        $this->assertEquals(
            array($carrierCode, $trackingTitle, $trackingNumber),
            array($track->getCarrierCode(), $track->getTitle(), $track->getNumber()),
            'Tracking data was saved incorrectly.'
        );

        /** Remove tracking information via API. */
        $isRemoved = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentRemoveTrack',
            array($this->_getShipmentFixture()->getIncrementId(), $trackingNumberId)
        );
        $this->assertTrue($isRemoved, "Tracking information was not removed.");

        /** Ensure that tracking data was saved correctly. */
        /** @var \Magento\Sales\Model\Order\Shipment $updatedShipment */
        $updatedShipment = Mage::getModel('\Magento\Sales\Model\Order\Shipment');
        $updatedShipment->load($this->_getShipmentFixture()->getId());
        $tracksCollection = $updatedShipment->getTracksCollection();
        $this->assertCount(0, $tracksCollection->getItems(), "Tracking information was not removed from DB.");
    }

    /**
     * Test shipment create and info via API.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/order_with_shipping.php
     * @magentoDbIsolation enabled
     */
    public function testCRUD()
    {
        // Create new shipment
        $newShipmentId = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $this->_getOrderFixture()->getIncrementId(),
                'itemsQty' => array(),
                'comment' => 'Shipment Created',
                'email' => true,
                'includeComment' => true
            )
        );
        Mage::register('shipmentIncrementId', $newShipmentId);

        // View new shipment
        $shipment = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentInfo',
            array(
                'shipmentIncrementId' => $newShipmentId
            )
        );

        $this->assertEquals($newShipmentId, $shipment['increment_id']);
    }

    /**
     * Test shipment create API.
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/order_with_shipping.php
     * @magentoDbIsolation enabled
     */
    public function testAutoIncrementType()
    {
        // Set shipping increment id prefix
        $prefix = '01';
        Magento_TestFramework_Helper_Eav::setIncrementIdPrefix('shipment', $prefix);

        // Create new shipment
        $newShipmentId = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentCreate',
            array(
                'orderIncrementId' => $this->_getOrderFixture()->getIncrementId(),
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
     *
     * @magentoDataFixture Magento/Sales/Model/Order/Api/_files/shipment.php
     * @magentoDbIsolation enabled
     */
    public function testSendInfo()
    {
        $isSent = Magento_TestFramework_Helper_Api::call(
            $this,
            'salesOrderShipmentSendInfo',
            array(
                'shipmentIncrementId' => $this->_getShipmentFixture()->getIncrementId(),
                'comment' => 'Comment text.'
            )
        );

        $this->assertTrue((bool)$isSent);
    }

    /**
     * Retrieve order from fixture.
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _getOrderFixture()
    {
        /** @var $order \Magento\Sales\Model\Resource\Order\Collection */
        $orderCollection = Mage::getModel('\Magento\Sales\Model\Resource\Order\Collection');
        $this->assertCount(1, $orderCollection->getItems());
        return $orderCollection->getFirstItem();
    }

    /**
     * Retrieve shipment from fixture.
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    protected function _getShipmentFixture()
    {
        /** @var $order \Magento\Sales\Model\Resource\Order\Shipment\Collection */
        $collection = Mage::getModel('\Magento\Sales\Model\Resource\Order\Shipment\Collection');
        $this->assertCount(1, $collection->getItems());
        return $collection->getFirstItem();
    }
}
