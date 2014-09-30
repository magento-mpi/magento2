<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\Comment;
use Magento\Sales\Model\Order\Shipment\CommentConverter;

/**
 * Class ShipmentAddComment
 */
class ShipmentAddComment
{
    /**
     * @var \Magento\Sales\Model\Order\Shipment\CommentConverter
     */
    protected $commentConverter;

    /**
     * @param \Magento\Sales\Model\Order\Shipment\CommentConverter $commentConverter
     */
    public function __construct(CommentConverter $commentConverter)
    {
        $this->commentConverter = $commentConverter;
    }

    /**
     * Invoke shipment add comment service
     *
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function invoke(Comment $comment)
    {
        /** @var \Magento\Sales\Model\Order\Shipment\Comment $commentModel */
        $commentModel = $this->commentConverter->getModel($comment);
        $commentModel->save();

        return true;
    }
}
