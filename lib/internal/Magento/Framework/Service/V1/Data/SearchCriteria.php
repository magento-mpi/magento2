<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractExtensibleObject;
use Magento\Framework\Api\Data\SearchCriteriaInterface;

/**
 * Data Object for SearchCriteria
 */
class SearchCriteria extends AbstractExtensibleObject implements SearchCriteriaInterface
{
    /**#@+
     * Constants for Data Object keys
     */
    const FILTER_GROUPS = 'filterGroups';
    const SORT_ORDERS = 'sort_orders';
    const PAGE_SIZE = 'page_size';
    const CURRENT_PAGE = 'current_page';

    /**
     * {@inheritdoc}
     */
    public function getFilterGroups()
    {
        return $this->_get(self::FILTER_GROUPS);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrders()
    {
        return $this->_get(self::SORT_ORDERS);
    }

    /**
     * {@inheritdoc}
     */
    public function getPageSize()
    {
        return $this->_get(self::PAGE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentPage()
    {
        return $this->_get(self::CURRENT_PAGE);
    }
}
