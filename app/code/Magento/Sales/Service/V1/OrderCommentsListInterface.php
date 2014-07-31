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
     * @return \Magento\Framework\Service\V1\Data\SearchResults
     */
    public function invoke($id);
}
