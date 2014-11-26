<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Api\Data;

/**
 * Interface ShipmentTrackSearchResultInterface
 */
interface ShipmentTrackSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\ShipmentTrackInterface[]
     */
    public function getItems();
}
