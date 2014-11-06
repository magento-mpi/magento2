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
 *
 * @codeCoverageIgnore
 */
class LowStockResultBuilder extends \Magento\Framework\Api\ExtensibleObjectBuilder
{
    /**
     * Set search criteria
     *
     * @param LowStockCriteria $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(LowStockCriteria $searchCriteria)
    {
        return $this->_set(LowStockResult::SEARCH_CRITERIA, $searchCriteria);
    }

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set(LowStockResult::TOTAL_COUNT, $totalCount);
    }

    /**
     * Set items
     *
     * @param array $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->_set(LowStockResult::PRODUCT_SKU_LIST, $items);
    }
}
