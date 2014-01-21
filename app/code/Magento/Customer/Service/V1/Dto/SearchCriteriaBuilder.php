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

class SearchCriteriaBuilder extends \Magento\Service\Entity\AbstractDtoBuilder
{
    /**
     * @param Filter $filter
     *
     * @return SearchCriteriaBuilder
     */
    public function addFilter($filter)
    {
        if (!isset($this->_data['filters'])) {
            $this->_data['filters'] = array();
        }

        $this->_data['filters'][] = $filter;
        return $this;
    }

    /**
     * @param string $field
     * @param int $direction
     *
     * @return SearchCriteriaBuilder
     */
    public function addSortOrder($field, $direction)
    {
        if (!isset($this->_data['sort_orders'])) {
            $this->_data['sort_orders'] = array();
        }

        $this->_data['sort_orders'][$field] = $direction;
        return $this;
    }

    /**
     * @param int $pageSize
     *
     * @return SearchCriteriaBuilder
     */
    public function setPageSize($pageSize)
    {
        return $this->_set('page_size', $pageSize);
    }

    /**
     * @param int $currentPage
     *
     * @return SearchCriteriaBuilder
     */
    public function setCurrentPage($currentPage)
    {
        return $this->_set('current_page', $currentPage);
    }
}
