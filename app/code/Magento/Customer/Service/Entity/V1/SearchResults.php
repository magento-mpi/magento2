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


class SearchResults extends \Magento\Service\Entity\AbstractDto
{
    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_get('items');
    }

    /**
     * @return SearchCriteria
     */
    public function getSearchCriteria()
    {
        return $this->_get('search_criteria');
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get('total_count');
    }

    /**
     * @param \Magento\Customer\Service\Entity\V1\SearchCriteria $searchCriteria
     *
     * @return SearchResults
     */
    public function setSearchCriteria(SearchCriteria $searchCriteria)
    {
        return $this->_set('search_criteria', $searchCriteria);
    }

    /**
     * @param int $totalCount
     *
     * @return SearchResults
     */
    public function setTotalCount($totalCount)
    {
        return $this->_set('total_count', $totalCount);
    }

    /**
     * @param array $items
     *
     * @return SearchResults
     */
    public function setItems($items)
    {
        return $this->_set('items', $items);
    }
}