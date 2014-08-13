<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Action\ShipmentAddTrack;
use Magento\Sales\Service\V1\Action\ShipmentRemoveTrack;
use Magento\Sales\Service\V1\Action\ShipmentEmail;
use Magento\Sales\Service\V1\Action\ShipmentAddComment;
use Magento\Sales\Service\V1\Data\ShipmentTrack;
use Magento\Sales\Service\V1\Data\Comment;

/**
 * Class ShipmentWrite
 */
class ShipmentWrite implements ShipmentWriteInterface
{
    /**
     * @var ShipmentAddTrack
     */
    protected $shipmentAddTrack;

    /**
     * @var ShipmentRemoveTrack
     */
    protected $shipmentRemoveTrack;

    /**
     * @var ShipmentEmail
     */
    protected $shipmentEmail;

    /**
     * @var ShipmentAddComment
     */
    protected $shipmentAddComment;

    /**
     * @param ShipmentAddTrack $shipmentAddTrack
     * @param ShipmentRemoveTrack $shipmentRemoveTrack
     * @param ShipmentEmail $shipmentEmail
     * @param ShipmentAddComment $shipmentAddComment
     */
    public function __construct(
        ShipmentAddTrack $shipmentAddTrack,
        ShipmentRemoveTrack $shipmentRemoveTrack,
        ShipmentEmail $shipmentEmail,
        ShipmentAddComment $shipmentAddComment
    )
    {
        $this->shipmentAddTrack = $shipmentAddTrack;
        $this->shipmentRemoveTrack = $shipmentRemoveTrack;
        $this->shipmentEmail = $shipmentEmail;
        $this->shipmentAddComment = $shipmentAddComment;
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\ShipmentTrack $track
     * @return bool
     * @throws \Exception
     */
    public function addTrack(ShipmentTrack $track)
    {
        return $this->shipmentAddTrack->invoke($track);
    }

    /**
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function removeTrack($id)
    {
        return $this->shipmentRemoveTrack->invoke($id);
    }

    /**
     * @param int $id
     * @return bool
     */
    public function email($id)
    {
        return $this->shipmentEmail->invoke($id);
    }

    /**
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function addComment(Comment $comment)
    {
        return $this->shipmentAddComment->invoke($comment);
    }
}
