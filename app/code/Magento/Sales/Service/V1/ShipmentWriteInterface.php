<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\ShipmentTrack;
use Magento\Sales\Service\V1\Data\Comment;

interface ShipmentWriteInterface
{
    /**
     * @param \Magento\Sales\Service\V1\Data\ShipmentTrack $track
     * @return bool
     * @throws \Exception
     */
    public function addTrack(ShipmentTrack $track);

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function removeTrack($id);

    /**
     * @param int $id
     * @return bool
     */
    public function email($id);

    /**
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function addComment(Comment $comment);

    /**
     * @param \Magento\Sales\Service\V1\Data\Shipment $shipmentDataObject
     * @return bool
     * @throws \Exception
     */
    public function create(\Magento\Sales\Service\V1\Data\Shipment $shipmentDataObject);
}
