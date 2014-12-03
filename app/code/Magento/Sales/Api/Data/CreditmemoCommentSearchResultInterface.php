<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Api\Data;

/**
 * Interface CreditmemoCommentSearchResultInterface
 */
interface CreditmemoCommentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\CreditmemoCommentInterface[]
     */
    public function getItems();
}
