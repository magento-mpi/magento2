<?php
/**
 * Customer Service Address Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;


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

    /**
     * @param Filter $filter
     *
     * @return SearchCriteria
     */
    public function addFilter($filter)
    {
        $this->getFilters()[] = $filter;
        return $this;
    }

    /**
     * @param string $field
     * @param int $direction
     *
     * @return SearchCriteria
     */
    public function addSortOrder($field, $direction)
    {
        $this->getSortOrders()[$field] = $direction;
        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return SearchCriteria
     */
    public function setPageSize($pageSize)
    {
        return $this->_set('page_size', $pageSize);
    }

    /**
     * @param int $currentPage
     *
     * @return SearchCriteria
     */
    public function setCurrentPage($currentPage)
    {
        return $this->_set('current_page', $currentPage);
    }
}