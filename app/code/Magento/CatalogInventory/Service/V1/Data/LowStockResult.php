<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

/**
 * Low stock search result data object
 *
 * @codeCoverageIgnore
 */
class LowStockResult extends AbstractExtensibleObject
{
    /**#@+
     * Low stock search result object data keys
     */
    const PRODUCT_SKU_LIST = 'items';
    const SEARCH_CRITERIA = 'search_criteria';
    const TOTAL_COUNT = 'total_count';
    /**#@-*/

    /**
     * Get items
     *
     * @return string[]
     */
    public function getItems()
    {
        return is_null($this->_get(self::PRODUCT_SKU_LIST)) ? array() : $this->_get(self::PRODUCT_SKU_LIST);
    }

    /**
     * Get search criteria
     *
     * @return \Magento\CatalogInventory\Service\V1\Data\LowStockCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get(self::SEARCH_CRITERIA);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::TOTAL_COUNT);
    }
}
