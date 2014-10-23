<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

/**
 * Interface for search results.
 */
interface SearchResultsInterface
{
    /**
     * Get customer addresses list.
     *
     * @return mixed
     */
    public function getItems();

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\Data\SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();
}
