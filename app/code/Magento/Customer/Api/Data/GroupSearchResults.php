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
interface GroupSearchResults
{
    /**
     * Get customer groups list.
     *
     * @return \Magento\Customer\Api\Data\Group[]
     */
    public function getItems();

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Service\V1\Data\SearchCriteria
     */
    public function getSearchCriteria();

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();
}
