<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class ShipmentCommentsList
 */
interface ShipmentCommentsListInterface
{
    /**
     * Invoke ShipmentCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResults
     */
    public function invoke($id);
}
