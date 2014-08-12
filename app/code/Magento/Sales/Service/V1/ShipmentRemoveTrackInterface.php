<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class ShipmentRemoveTrackInterface
 */
interface ShipmentRemoveTrackInterface
{
    /**
     * Invoke shipment remove track
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id);
}
