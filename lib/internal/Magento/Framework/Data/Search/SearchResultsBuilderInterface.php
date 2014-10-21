<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface SearchResultsBuilderInterface
{
    /**
     * Builder for the SearchResults Service Data Object
     */
    function create();

    /**
     * Set search criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria);

    /**
     * Set total count
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);

    /**
     * Set items
     *
     * @param \Magento\Framework\Service\Data\AbstractExtensibleObject[] $items
     * @return $this
     */
    public function setItems($items);
}
