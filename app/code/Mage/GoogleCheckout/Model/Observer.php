<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Google Checkout Event Observer
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 */
class Mage_GoogleCheckout_Model_Observer
{
    public function salesOrderShipmentTrackSaveAfter(Magento_Event_Observer $observer)
    {
        $track = $observer->getEvent()->getTrack();

        $order = $track->getShipment()->getOrder();
        $shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
        if (!$shippingMethod) {
            return;
        }

        // Process only Google Checkout internal methods
        /* @var $gcCarrier Mage_GoogleCheckout_Model_Shipping */
        $gcCarrier = Mage::getModel('Mage_GoogleCheckout_Model_Shipping');
        list($carrierCode, $methodCode) = explode('_', $shippingMethod);
        if ($gcCarrier->getCarrierCode() != $carrierCode) {
            return;
        }
        $internalMethods = $gcCarrier->getInternallyAllowedMethods();
        if (!isset($internalMethods[$methodCode])) {
            return;
        }

        Mage::getModel('Mage_GoogleCheckout_Model_Api')
            ->setStoreId($order->getStoreId())
            ->deliver($order->getExtOrderId(), $track->getCarrierCode(), $track->getNumber());
    }

    /*
     * Performs specifical actions on Google Checkout internal shipments saving
     *
     * @param Magento_Event_Observer $observer
     * @return void
     */
    public function salesOrderShipmentSaveAfter(Magento_Event_Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $shippingMethod = $order->getShippingMethod(); // String in format of 'carrier_method'
        if (!$shippingMethod) {
            return;
        }

        // Process only Google Checkout internal methods
        /* @var $gcCarrier Mage_GoogleCheckout_Model_Shipping */
        $gcCarrier = Mage::getModel('Mage_GoogleCheckout_Model_Shipping');
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
            Mage::getModel('Mage_GoogleCheckout_Model_Api')
                ->setStoreId($order->getStoreId())
                ->shipItems($order->getExtOrderId(), $items);
        }
    }
}
