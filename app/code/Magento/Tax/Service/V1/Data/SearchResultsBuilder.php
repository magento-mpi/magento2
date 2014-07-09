<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1\Data;

use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Builder for the SearchResults Service Data Object
 *
 * @method SearchResults create()
 */
class SearchResultsBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Set search criteria.
     *
     * @param SearchCriteria $searchCriteria
     * @return SearchResultsBuilder
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->_set('search_criteria', $searchCriteria);
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return SearchResultsBuilder
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set('total_count', $totalCount);
    }

    /**
     * Set items.
     *
     * @param TaxClass[] $items
     * @return SearchResultsBuilder
     */
    public function setItems($items)
    {
        return $this->_set('items', $items);
    }
}
