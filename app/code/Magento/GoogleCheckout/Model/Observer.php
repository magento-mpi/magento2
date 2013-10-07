<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Checkout Event Observer
 *
 * @category   Magento
 * @package    Magento_GoogleCheckout
 */
namespace Magento\GoogleCheckout\Model;

class Observer
{
    /**
     * @var ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var ApiFactory
     */
    protected $apiFactory;

    /**
     * @param ShippingFactory $shippingFactory
     * @param ApiFactory $apiFactory
     */
    public function __construct(
        ShippingFactory $shippingFactory,
        ApiFactory $apiFactory
    ) {
        $this->shippingFactory = $shippingFactory;
        $this->apiFactory = $apiFactory;
    }

    public function salesOrderShipmentTrackSaveAfter(\Magento\Event\Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();

        $order = $track->getShipment()->getOrder();
        $shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
        if (!$shippingMethod) {
            return;
        }

        // Process only Google Checkout internal methods
        /* @var $gcCarrier \Magento\GoogleCheckout\Model\Shipping */
        $gcCarrier = $this->shippingFactory->create();
        list($carrierCode, $methodCode) = explode('_', $shippingMethod);
        if ($gcCarrier->getCarrierCode() != $carrierCode) {
            return;
        }
        $internalMethods = $gcCarrier->getInternallyAllowedMethods();
        if (!isset($internalMethods[$methodCode])) {
            return;
        }

        $this->apiFactory->create()
            ->setStoreId($order->getStoreId())
            ->deliver($order->getExtOrderId(), $track->getCarrierCode(), $track->getNumber());
    }

    /*
     * Performs specifical actions on Google Checkout internal shipments saving
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function salesOrderShipmentSaveAfter(\Magento\Event\Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
        if (!$shippingMethod) {
            return;
        }

        // Process only Google Checkout internal methods
        /* @var $gcCarrier \Magento\GoogleCheckout\Model\Shipping */
        $gcCarrier = $this->shippingFactory->create();
        list($carrierCode, $methodCode) = explode('_', $shippingMethod);
        if ($gcCarrier->getCarrierCode() != $carrierCode) {
            return;
        }
        $internalMethods = $gcCarrier->getInternallyAllowedMethods();
        if (!isset($internalMethods[$methodCode])) {
            return;
        }

        // Process this saving
        $items = array();
        foreach ($shipment->getAllItems() as $item) {
            if ($item->getOrderItem()->getParentItemId()) {
                continue;
            }
            $items[] = $item->getSku();
        }

        if ($items) {
            $this->apiFactory->create()
                ->setStoreId($order->getStoreId())
                ->shipItems($order->getExtOrderId(), $items);
        }
    }
}
