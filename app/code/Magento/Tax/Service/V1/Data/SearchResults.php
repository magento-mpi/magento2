<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

/**
 * Data object for Tax class search results.
 */
class SearchResults extends \Magento\Framework\Service\Data\AbstractObject
{
    /**
     * Get list of tax classes.
     *
     * @return \Magento\Tax\Service\V1\Data\TaxClass[]
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? [] : $this->_get('items');
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get('search_criteria');
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get('total_count');
    }
}
