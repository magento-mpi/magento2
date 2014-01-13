<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Customer\Service\V1\Dto\Filter;

class SearchCriteria extends \Magento\Service\Entity\AbstractDto
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->_get('filters', $this->_createArray());
    }

    /**
     * @return array
     */
    public function getSortOrders()
    {
        return $this->_get('sort_orders', $this->_createArray());
    }

    /**
     * @return int
     */
    public function getPageSize()
    {
        return $this->_get('page_size');
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->_get('current_page');
    }
}
