<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface SearchResultsInterface 
{
    /**
     * Get items
     *
     * @return \Magento\Framework\Object
     */
    public function getItems();

    /**
     * Get search criteria
     *
     * @return \Magento\Framework\Data\Search\SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount();
}
