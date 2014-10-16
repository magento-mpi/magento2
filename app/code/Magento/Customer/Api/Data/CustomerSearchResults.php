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
interface CustomerSearchResults
{
    /**
     * Get customers list.
     *
     * @return \Magento\Customer\Api\Data\Customer[]
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
