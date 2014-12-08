<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface ShipmentSearchResultInterface
 */
interface ShipmentSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\ShipmentInterface[]
     */
    public function getItems();
}
