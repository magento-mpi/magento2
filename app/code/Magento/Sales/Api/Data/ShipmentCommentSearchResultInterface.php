<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Api\Data;

/**
 * Interface ShipmentCommentSearchResultInterface
 */
interface ShipmentCommentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\ShipmentCommentInterface[]
     */
    public function getItems();
}
