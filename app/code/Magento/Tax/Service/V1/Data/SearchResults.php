<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObject;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class SearchResults extends AbstractObject
{
    /**
     * Get items
     *
     * @return TaxRule[]
     */
    public function getItems()
    {
        return is_null($this->_get('items')) ? array() : $this->_get('items');
    }

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria
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
