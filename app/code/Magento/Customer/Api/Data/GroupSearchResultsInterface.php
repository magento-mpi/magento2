<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Interface for customer groups search results.
 */
interface GroupSearchResultsInterface
{
    /**
     * Get customer groups list.
     *
     * @return \Magento\Customer\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface
     */
    public function getSearchCriteria();

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();
}
