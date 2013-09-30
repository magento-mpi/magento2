<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Shipping
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Shipping_Model_Info extends Magento_Object
{
    /**
     * Tracking info
     *
     * @var array
     */
    protected $_trackingInfo = array();

    /**
     * Shipping data
     *
     * @var Magento_Shipping_Helper_Data
     */
    protected $_shippingData;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Sales_Model_Order_ShipmentFactory
     */
    protected $_shipmentFactory;

    /**
     * @var Magento_Sales_Model_Order_Shipment_TrackFactory
     */
    protected $_trackFactory;

    /**
     * @param Magento_Shipping_Helper_Data $shippingData
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Sales_Model_Order_ShipmentFactory $shipmentFactory
     * @param Magento_Sales_Model_Order_Shipment_TrackFactory $trackFactory
     * @param array $data
     */
    public function __construct(
        Magento_Shipping_Helper_Data $shippingData,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Sales_Model_Order_ShipmentFactory $shipmentFactory,
        Magento_Sales_Model_Order_Shipment_TrackFactory $trackFactory,
        array $data = array()
    ) {
        $this->_shippingData = $shippingData;
        $this->_orderFactory = $orderFactory;
        $this->_shipmentFactory = $shipmentFactory;
        $this->_trackFactory = $trackFactory;
        parent::__construct($data);
    }

    /**
     * Generating tracking info
     *
     * @param array $hash
     * @return Magento_Shipping_Model_Info
     */
    public function loadByHash($hash)
    {
        /* @var $helper Magento_Shipping_Helper_Data */
        $helper = $this->_shippingData;
        $data = $helper->decodeTrackingHash($hash);
        if (!empty($data)) {
            $this->setData($data['key'], $data['id']);
            $this->setProtectCode($data['hash']);

            if ($this->getOrderId() > 0) {
                $this->getTrackingInfoByOrder();
            } elseif($this->getShipId() > 0) {
                $this->getTrackingInfoByShip();
            } else {
                $this->getTrackingInfoByTrackId();
            }
        }
        return $this;
    }

    /**
     * Retrieve tracking info
     *
     * @return array
     */
    public function getTrackingInfo()
    {
        return $this->_trackingInfo;
    }

    /**
     * Instantiate order model
     *
     * @return Magento_Sales_Model_Order|bool
     */
    protected function _initOrder()
    {
        /** @var Magento_Sales_Model_Order $order */
        $order = $this->_orderFactory->create()->load($this->getOrderId());

        if (!$order->getId() || $this->getProtectCode() != $order->getProtectCode()) {
            return false;
        }

        return $order;
    }

    /**
     * Instantiate ship model
     *
     * @return Magento_Sales_Model_Order_Shipment|bool
     */
    protected function _initShipment()
    {
        /* @var $model Magento_Sales_Model_Order_Shipment */
        $model = $this->_shipmentFactory->create();
        $ship = $model->load($this->getShipId());
        if (!$ship->getEntityId() || $this->getProtectCode() != $ship->getProtectCode()) {
            return false;
        }

        return $ship;
    }

    /**
     * Retrieve all tracking by order id
     *
     * @return array
     */
    public function getTrackingInfoByOrder()
    {
        $shipTrack = array();
        $order = $this->_initOrder();
        if ($order) {
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment){
                $increment_id = $shipment->getIncrementId();
                $tracks = $shipment->getTracksCollection();

                $trackingInfos=array();
                foreach ($tracks as $track){
                    $trackingInfos[] = $track->getNumberDetail();
                }
                $shipTrack[$increment_id] = $trackingInfos;
            }
        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve all tracking by ship id
     *
     * @return array
     */
    public function getTrackingInfoByShip()
    {
        $shipTrack = array();
        $shipment = $this->_initShipment();
        if ($shipment) {
            $increment_id = $shipment->getIncrementId();
            $tracks = $shipment->getTracksCollection();

            $trackingInfos=array();
            foreach ($tracks as $track){
                $trackingInfos[] = $track->getNumberDetail();
            }
            $shipTrack[$increment_id] = $trackingInfos;

        }
        $this->_trackingInfo = $shipTrack;
        return $this->_trackingInfo;
    }

    /**
     * Retrieve tracking by tracking entity id
     *
     * @return array
     */
    public function getTrackingInfoByTrackId()
    {
        /** @var Magento_Sales_Model_Order_Shipment_Track $track */
        $track = $this->_trackFactory->create()->load($this->getTrackId());
        if ($track->getId() && $this->getProtectCode() == $track->getProtectCode()) {
            $this->_trackingInfo = array(array($track->getNumberDetail()));
        }
        return $this->_trackingInfo;
    }
}
