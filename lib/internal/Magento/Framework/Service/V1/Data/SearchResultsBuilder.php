<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method SearchResults create()
 */
class SearchResultsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set search criteria
     *
     * @param SearchCriteria $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria)
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
