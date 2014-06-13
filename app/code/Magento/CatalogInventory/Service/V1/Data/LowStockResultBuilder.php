<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

/**
 * Low stock search result builder object
 */
class LowStockResultBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set search criteria
     *
     * @param LowStockCriteria $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(LowStockCriteria $searchCriteria)
    {
        return $this->_set('search_criteria', $searchCriteria);
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set('total_count', $totalCount);
    }

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->_set('items', $items);
    }
}
