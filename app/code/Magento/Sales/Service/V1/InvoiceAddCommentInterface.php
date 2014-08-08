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
 * Class InvoiceAddCommentInterface
 */
interface InvoiceAddCommentInterface
{
    /**
     * Invoke invoice add comment service
     *
     * @param \Magento\Sales\Service\V1\Data\Comment $comment
     * @return bool
     */
    public function invoke(Comment $comment);
}
