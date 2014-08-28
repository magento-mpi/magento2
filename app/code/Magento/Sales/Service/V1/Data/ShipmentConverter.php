<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Data;

/**
 * Class ShipmentConverter
 *
 * @package Magento\Sales\Service\V1\Data
 */
class ShipmentConverter
{
    /**
     * @var \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader
     */
    protected $shipmentLoader;

    /**
     * @param \Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader
     */
    public function __construct(\Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader $shipmentLoader)
    {
        $this->shipmentLoader = $shipmentLoader;
    }

    /**
     * @param Shipment $dataObject
     * @return \Magento\Sales\Model\Order\Shipment
     * @throws \Exception
     */
    public function getModel(Shipment $dataObject)
    {
        $this->shipmentLoader->setOrderId($dataObject->getOrderId());
        $this->shipmentLoader->setShipmentId($dataObject->getEntityId());

        $items = [];
        foreach ($dataObject->getItems() as $item) {
            $items[$item->getOrderItemId()] = $item->getQty();
        }
        $shipmentItems = ['items' => $items];
        $this->shipmentLoader->setShipment($shipmentItems);
        $this->shipmentLoader->setTracking($dataObject->getTracks());
        return $this->shipmentLoader->load();
    }
}
