<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

use Magento\Sales\Service\V1\Data\Comment;
use Magento\Sales\Model\Order\Invoice\CommentConverter;

/**
 * Class InvoiceAddComment
 */
class InvoiceAddComment
{
    /**
     * @var \Magento\Sales\Model\Order\Invoice\CommentConverter
     */
    protected $commentConverter;

    /**
     * @param \Magento\Sales\Model\Order\Invoice\CommentConverter $commentConverter
     */
    public function __construct(CommentConverter $commentConverter)
    {
        $this->commentConverter = $commentConverter;
    }

    /**
     * Invoke invoice add comment service
     *
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     * @throws \Exception
     */
    public function invoke(Comment $comment)
    {
        /** @var \Magento\Sales\Model\Order\Invoice\Comment $commentModel */
        $commentModel = $this->commentConverter->getModel($comment);
        $commentModel->save();

        return true;
    }
}
