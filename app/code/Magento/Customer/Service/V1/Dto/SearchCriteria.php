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
     * @return \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface
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
     * Create Array
     *
     * @todo to be implemented in MAGETWO-18201. Change return type to array.
     *
     * @return void
     */
    private function _createArray()
    {
    }
}
