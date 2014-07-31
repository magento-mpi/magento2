<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Interface OrderCommentsListInterface
 */
interface OrderCommentsListInterface
{
    /**
     * Invoke OrderCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\OrderStatusHistorySearchResults
     */
    public function invoke($id);
}
