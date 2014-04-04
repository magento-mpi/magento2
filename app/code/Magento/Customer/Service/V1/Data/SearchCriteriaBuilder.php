<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\Data\Search\OrGroupBuilder;
use Magento\Service\Data\AbstractObjectBuilder;
use Magento\Service\V1\Data\FilterBuilder;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends AbstractObjectBuilder
{
    /**
     * @var Search\AndGroupBuilder
     */
    protected $_andGroupBuilder;

    /**
     * Constructor
     *
     * @param Search\AndGroupBuilder $andGroupBuilder
     */
    public function __construct(Search\AndGroupBuilder $andGroupBuilder)
    {
        parent::__construct();
        $this->_andGroupBuilder = $andGroupBuilder;
    }

    /**
     * Builds the SearchCriteria Data Object
     *
     * @return SearchCriteria
     */
    public function create()
    {
        $this->_set(SearchCriteria::ROOT_GROUP_TYPE, $this->_andGroupBuilder->create());
        return parent::create();
    }

    /**
     * Add filter
     *
     * @param \Magento\Service\V1\Data\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Service\V1\Data\Filter $filter)
    {
        $this->_andGroupBuilder->addFilter($filter);
        return $this;
    }

    /**
     * Set filters
     *
     * @param \Magento\Customer\Service\V1\Data\Search\AndGroup $filterGroup
     * @return $this
     */
    public function setAndGroup($filterGroup)
    {
        $this->_andGroupBuilder->populate($filterGroup);
        return $this;
    }

    /**
     * Add an OR grouping of filters to this SearchCriteria.
     *
     * @param \Magento\Service\V1\Data\Filter[] $filters
     * @return $this
     */
    public function addOrGroup($filters)
    {
        $orGroup = new OrGroupBuilder(new FilterBuilder());
        foreach ($filters as $filter) {
            $orGroup->addFilter($filter);
        }
        $this->_andGroupBuilder->addOrGroup($orGroup->create());
        return $this;
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
