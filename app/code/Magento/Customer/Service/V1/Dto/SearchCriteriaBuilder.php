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
     * Add filter
     *
     * @param \Magento\Customer\Service\V1\Dto\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Customer\Service\V1\Dto\Filter $filter)
    {
        $this->getFilterGroup()->addFilter($filter);
        return $this;
    }

    /**
     * Add an OR grouping of filters to this SearchCriteria.
     *
     * @param \Magento\Customer\Service\V1\Dto\Filter[] $filters
     * @return $this
     */
    public function addOrGroup($filters)
    {
        $orGroup = new OrGroupBuilder();
        foreach ($filters as $filter) {
            $orGroup->addFilter($filter);
        }
        $this->getFilterGroup()->addGroup($orGroup->create());
        return $this;
    }

    /**
     * Get filter group
     *
     * @return \Magento\Customer\Service\V1\Dto\Search\AndGroupBuilder
     */
    private function getFilterGroup()
    {
        if (!isset($this->_data['filters'])) {
            $this->_data['filters'] = new Search\AndGroupBuilder();
        }
        return $this->_data['filters'];
    }

    /**
     * Add sort order
     *
     * @param string $field
     * @param int $direction
     * @return $this
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
     * Set page size
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        return $this->_set('page_size', $pageSize);
    }

    /**
     * Set current page
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        return $this->_set('current_page', $currentPage);
    }
}
