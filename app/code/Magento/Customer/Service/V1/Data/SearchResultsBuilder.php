<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\Data\SearchCriteria;

class SearchResultsBuilder extends \Magento\Service\Data\AbstractObjectBuilder
{
    /**
     * @param \Magento\Customer\Service\V1\Data\SearchCriteria $searchCriteria
     *
     * @return SearchResultsBuilder
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->_set('search_criteria', $searchCriteria);
    }

    /**
     * @param int $totalCount
     *
     * @return SearchResultsBuilder
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set('total_count', $totalCount);
    }

    /**
     * @param array $items
     *
     * @return SearchResultsBuilder
     */
    public function setItems($items)
    {
        return $this->_set('items', $items);
    }
}
