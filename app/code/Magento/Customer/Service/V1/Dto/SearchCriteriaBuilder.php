<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Dto;

use Magento\Customer\Service\V1\Dto\Search\OrGroupBuilder;
use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Builder for SearchCriteria DTO
 */
class SearchCriteriaBuilder extends AbstractDtoBuilder
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $this->_data['filters'] = $this->getFilterGroup()->create();
        return parent::create();
    }
    /**
     * @param Filter $filter
     *
     * @return SearchCriteriaBuilder
     */
    public function addFilter(Filter $filter)
    {
        $this->getFilterGroup()->addFilter($filter);
        return $this;
    }

    /**
     * Add an OR grouping of filters to this SearchCriteria.
     *
     * @param Filter[] $filters
     */
    public function addOrGroup($filters)
    {
        $orGroup = new OrGroupBuilder();
        foreach ($filters as $filter) {
            $orGroup->addFilter($filter);
        }
        $this->getFilterGroup()->addGroup($orGroup->create());
    }

    /**
     * @return Search\AndGroupBuilder
     */
    private function getFilterGroup()
    {
        if (!isset($this->_data['filters'])) {
            $this->_data['filters'] = new Search\AndGroupBuilder();
        }
        return $this->_data['filters'];
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
