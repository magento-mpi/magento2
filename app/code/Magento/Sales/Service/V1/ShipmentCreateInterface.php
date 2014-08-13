<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\ShipmentConverter;

/**
 * Class ShipmentCreate
 *
 * @package Magento\Sales\Service\V1
 */
interface ShipmentCreateInterface
{
    /**
     * Invoke CreateShipment service
     *
     * @param Data\Shipment $shipmentDataObject
     * @return bool
     * @throws \Exception
     */
    public function invoke(\Magento\Sales\Service\V1\Data\Shipment $shipmentDataObject);
}
