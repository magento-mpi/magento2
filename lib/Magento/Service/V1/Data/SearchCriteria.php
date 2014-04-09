<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\V1\Data;

use Magento\Service\Data\AbstractObject;

/**
 * Data Object for SearchCriteria
 */
class SearchCriteria extends AbstractObject
{
    const SORT_ASC = 1;

    const SORT_DESC = -1;
    const FILTER_GROUPS = 'filterGroups';

    /**
     * Returns a list of filter groups
     *
     * @return \Magento\Service\V1\Data\Search\FilterGroup[]
     */
    public function getFilterGroups()
    {
        return $this->_get(self::FILTER_GROUPS);
    }

    /**
     * Get sort order
     *
     * @return string[]|null
     */
    public function getSortOrders()
    {
        return $this->_get('sort_orders');
    }

    /**
     * Get page size
     *
     * @return int|null
     */
    public function getPageSize()
    {
        return $this->_get('page_size');
    }

    /**
     * Get current page
     *
     * @return int|null
     */
    public function getCurrentPage()
    {
        return $this->_get('current_page');
    }
}
