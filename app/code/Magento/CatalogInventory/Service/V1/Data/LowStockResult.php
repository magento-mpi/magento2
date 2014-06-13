<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * Low stock search result data object
 */
class LowStockResult extends AbstractObject
{
    /**
     * Get items
     *
     * @return array
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? array() : $this->_get('items');
    }

    /**
     * Get search criteria
     *
     * @return \Magento\CatalogInventory\Service\V1\Data\LowStockCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get('search_criteria');
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get('total_count');
    }
}
