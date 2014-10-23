<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api\Data;

/**
 * Interface for customer search results.
 */
interface CustomerSearchResultsInterface
{
    /**
     * Get customers list.
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface[]
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
