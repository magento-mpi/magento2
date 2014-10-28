<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api\Data;

/**
 * Search criteria interface.
 */
interface SearchCriteriaInterface
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * Get a list of filter groups.
     *
     * @return \Magento\Framework\Service\V1\Data\Search\FilterGroup[]
     */
    public function getFilterGroups();

    /**
     * Get sort order.
     *
     * @return \Magento\Framework\Service\V1\Data\SortOrder[]|null
     */
    public function getSortOrders();

    /**
     * Get page size.
     *
     * @return int|null
     */
    public function getPageSize();

    /**
     * Get current page.
     *
     * @return int|null
     */
    public function getCurrentPage();
}
