<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class CreditmemoCommentsList
 */
interface CreditmemoCommentsListInterface
{

    /**
     * Invoke CreditmemoCommentsList service
     *
     * @param int $id
     * @return \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder
     */
    public function invoke($id);
}
