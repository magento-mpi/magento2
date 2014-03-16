<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Service\Data\AbstractObject;

/**
 * Data Object for SearchCriteria
 */
class SearchCriteria extends AbstractObject
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;
    const ROOT_GROUP_TYPE = 'andGroup';

    /**
     * Get filters
     * 
     * @return \Magento\Customer\Service\V1\Data\Search\AndGroup
     */
    public function getAndGroup()
    {
        return $this->_get(self::ROOT_GROUP_TYPE);
    }

    /**
     * Get sort order
     *
     * @return string[]
     */
    public function getSortOrders()
    {
        return $this->_get('sort_orders');
    }

    /**
     * Get page size
     *
     * @return int
     */
    public function getPageSize()
    {
        return $this->_get('page_size');
    }

    /**
     * Get current page
     *
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_get('current_page');
    }
}
