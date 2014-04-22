<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service\V1\Data;

use Magento\Framework\Service\Data\AbstractObjectBuilder;
use Magento\Framework\Service\V1\Data\Search\FilterGroupBuilder;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends AbstractObjectBuilder
{
    /**
     * @var FilterGroupBuilder
     */
    protected $_filterGroupBuilder;

    /**
     * Constructor
     *
     * @param FilterGroupBuilder $filterGroupBuilder
     */
    public function __construct(FilterGroupBuilder $filterGroupBuilder)
    {
        parent::__construct();
        $this->_filterGroupBuilder = $filterGroupBuilder;
    }

    /**
     * Builds the SearchCriteria Data Object
     *
     * @return SearchCriteria
     */
    public function create()
    {
        //Initialize with empty array if not set
        if (empty($this->_data[SearchCriteria::FILTER_GROUPS])) {
            $this->_set(SearchCriteria::FILTER_GROUPS, []);
        }
        return parent::create();
    }

    /**
     * Create a filter group based on the filter array provided and add to the filter groups
     *
     * @param \Magento\Framework\Service\V1\Data\Filter[] $filter
     * @return $this
     */
    public function addFilter(array $filter)
    {
        $this->_data[SearchCriteria::FILTER_GROUPS][] = $this->_filterGroupBuilder->setFilters($filter)->create();
        return $this;
    }

    /**
     * Set filter groups
     *
     * @param \Magento\Framework\Service\V1\Data\Search\FilterGroup[] $filterGroups
     * @return $this
     */
    public function setFilterGroups(array $filterGroups)
    {
        return $this->_set(SearchCriteria::FILTER_GROUPS, $filterGroups);
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
        if (!isset($this->_data[SearchCriteria::SORT_ORDERS])) {
            $this->_data[SearchCriteria::SORT_ORDERS] = array();
        }

        $this->_data[SearchCriteria::SORT_ORDERS][$field] = $direction;
        return $this;
    }

    /**
     * Set sort orders
     *
     * @param array $sortOrders
     * @return $this
     */
    public function setSortOrders(array $sortOrders)
    {
        return $this->_set(SearchCriteria::SORT_ORDERS, $sortOrders);
    }

    /**
     * Set page size
     *
     * @param int $pageSize
     * @return $this
     */
    public function setPageSize($pageSize)
    {
        return $this->_set(SearchCriteria::PAGE_SIZE, $pageSize);
    }

    /**
     * Set current page
     *
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        return $this->_set(SearchCriteria::CURRENT_PAGE, $currentPage);
    }
}
