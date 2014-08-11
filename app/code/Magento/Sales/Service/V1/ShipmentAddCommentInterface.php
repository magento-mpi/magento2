<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\Comment;

/**
 * Class ShipmentAddCommentInterface
 */
interface ShipmentAddCommentInterface
{
    /**
     * Invoke shipment add comment service
     *
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function invoke(Comment $comment);
}
