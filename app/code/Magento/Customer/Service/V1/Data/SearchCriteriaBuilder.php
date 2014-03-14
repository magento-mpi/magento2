<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\Data\Search\OrGroupBuilder;
use Magento\Service\Data\AbstractObject;
use Magento\Service\Data\AbstractObjectBuilder;

/**
 * Builder for SearchCriteria Service Data Object
 */
class SearchCriteriaBuilder extends AbstractObjectBuilder
{
    /**
     * Builds the Data Object
     *
     * @return SearchCriteria
     */
    public function create()
    {
        $this->_data[SearchCriteria::ROOT_GROUP_TYPE] = $this->getFilterGroup() instanceof AbstractObject
            ? $this->getFilterGroup()
            : $this->getFilterGroup()->create();
        return parent::create();
    }

    /**
     * Add filter
     *
     * @param \Magento\Service\Data\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Service\Data\Filter $filter)
    {
        $this->getFilterGroup()->addFilter($filter);
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
        // TODO : Fix AbstractFilterGroupBuilder to override _setDataValues to set Filter object correctly
        //Check ex. \Magento\Customer\Service\V1\Data\AddressBuilder::_setDataValues
        //(new Search\AndGroupBuilder())->populate($filterGroup);
        $this->_data[SearchCriteria::ROOT_GROUP_TYPE] = $filterGroup;
        return $this;
    }

    /**
     * Add an OR grouping of filters to this SearchCriteria.
     *
     * @param \Magento\Service\Data\Filter[] $filters
     * @return $this
     */
    public function addOrGroup($filters)
    {
        $orGroup = new OrGroupBuilder();
        foreach ($filters as $filter) {
            $orGroup->addFilter($filter);
        }
        $this->getFilterGroup()->addOrGroup($orGroup->create());
        return $this;
    }

    /**
     * Get filter group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\AndGroupBuilder
     */
    private function getFilterGroup()
    {
        if (!isset($this->_data[SearchCriteria::ROOT_GROUP_TYPE])) {
            $this->_data[SearchCriteria::ROOT_GROUP_TYPE] = new Search\AndGroupBuilder();
        }
        return $this->_data[SearchCriteria::ROOT_GROUP_TYPE];
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
