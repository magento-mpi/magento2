<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Sales\Service\V1\Data\Comment;
use Magento\Sales\Model\Order\Creditmemo\CommentConverter;

/**
 * Class CreditmemoAddComment
 */
class CreditmemoAddComment implements CreditmemoAddCommentInterface
{
    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentConverter
     */
    protected $commentConverter;

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\CommentConverter $commentConverter
     */
    public function __construct(CommentConverter $commentConverter)
    {
        $this->commentConverter = $commentConverter;
    }

    /**
     * Invoke creditmemo add comment service
     *
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     */
    public function invoke(Comment $comment)
    {
        $result = true;
        /** @var \Magento\Sales\Model\Order\Creditmemo\Comment $commentModel */
        $commentModel = $this->commentConverter->getModel($comment);
        try {
            $commentModel->save();
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }
}
