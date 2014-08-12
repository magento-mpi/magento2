<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\ShipmentTrack;

/**
 * Class ShipmentAddTrackInterface
 */
interface ShipmentAddTrackInterface
{
    /**
     * Invoke shipment add track service
     *
     * @param \Magento\Sales\Service\V1\Data\ShipmentTrack $track
     * @return bool
     * @throws \Exception
     */
    public function invoke(ShipmentTrack $track);
}
