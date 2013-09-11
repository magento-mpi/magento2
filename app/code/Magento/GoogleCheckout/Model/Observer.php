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
        $gcCarrier = \Mage::getModel('\Magento\GoogleCheckout\Model\Shipping');
        list($carrierCode, $methodCode) = explode('_', $shippingMethod);
        if ($gcCarrier->getCarrierCode() != $carrierCode) {
            return;
        }
        $internalMethods = $gcCarrier->getInternallyAllowedMethods();
        if (!isset($internalMethods[$methodCode])) {
            return;
        }

        \Mage::getModel('\Magento\GoogleCheckout\Model\Api')
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
        $gcCarrier = \Mage::getModel('\Magento\GoogleCheckout\Model\Shipping');
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
            \Mage::getModel('\Magento\GoogleCheckout\Model\Api')
                ->setStoreId($order->getStoreId())
                ->shipItems($order->getExtOrderId(), $items);
        }
    }
}
