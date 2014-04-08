<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\V1\Data;

use Magento\Service\Data\AbstractObjectBuilder;
use Magento\Service\V1\Data\Search\FilterGroupBuilder;

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
     * @param \Magento\Service\V1\Data\Filter[] $group
     * @return $this
     */
    public function addFilterGroup(array $group)
    {
        $this->_data[SearchCriteria::FILTER_GROUPS][] = $this->_filterGroupBuilder->setFilters($group)->create();
        return $this;
    }

    /**
     * Set filter groups
     *
     * @param \Magento\Service\V1\Data\Search\FilterGroup[] $groups
     * @return $this
     */
    public function setFilterGroups(array $groups)
    {
        return $this->_set(SearchCriteria::FILTER_GROUPS, $groups);
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
