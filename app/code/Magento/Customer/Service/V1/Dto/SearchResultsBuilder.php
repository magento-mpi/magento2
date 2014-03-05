<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

/**
 * Class SearchResultsBuilder
 */
class SearchResultsBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * Set search criteria
     *
     * @param \Magento\Customer\Service\V1\Dto\SearchCriteria $searchCriteria
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
     * @param \Magento\Service\Entity\AbstractDto[] $items
     * @return $this
     */
    public function setItems($items)
    {
        return $this->_set('items', $items);
    }
}
