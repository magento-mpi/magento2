<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Data\Search;

interface SearchCriteriaInterface
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * Returns a list of filter groups
     *
     * @return \Magento\Framework\Data\Search\FilterGroupInterface[]
     */
    public function getFilterGroups();

    /**
     * Get sort order
     *
     * @return \Magento\Framework\Data\Search\SortOrderInterface[]|null
     */
    public function getSortOrders();

    /**
     * Get page size
     *
     * @return int|null
     */
    public function getPageSize();

    /**
     * Get current page
     *
     * @return int|null
     */
    public function getCurrentPage();
}
