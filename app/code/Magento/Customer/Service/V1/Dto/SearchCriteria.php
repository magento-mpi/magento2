<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Service\Entity\AbstractDto;

/**
 * DTO for SearchCriteria
 */
class SearchCriteria extends AbstractDto
{
    const SORT_ASC = 1;
    const SORT_DESC = -1;

    /**
     * Get filters
     *
     * @return \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface
     */
    public function getFilters()
    {
        return $this->_get('filters');
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
