<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

class SearchResultsBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param \Magento\Customer\Service\Entity\V1\SearchCriteria $searchCriteria
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
