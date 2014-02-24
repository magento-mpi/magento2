<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Customer\Service\V1\Data\Filter;
use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Abstract Builder for AbstractFilterGroup DTOs.
 */
abstract class AbstractFilterGroupBuilder extends AbstractDtoBuilder
{
    /**
     * @param Filter $filter
     * @return $this
     */
    public function addFilter(Filter $filter)
    {
        if (!isset($this->_data[AbstractFilterGroup::FILTERS])
            || !is_array($this->_data[AbstractFilterGroup::FILTERS])
        ) {
            $this->_data[AbstractFilterGroup::FILTERS] = [];
        }
        $this->_data[AbstractFilterGroup::FILTERS][] = $filter;
        return $this;
    }

    /**
     * @param Filter[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        return $this->_set(AbstractFilterGroup::FILTERS, $filters);
    }

    /**
     * @param FilterGroupInterface $group
     * @return $this
     */
    public function addGroup(FilterGroupInterface $group)
    {
        if (!isset($this->_data[AbstractFilterGroup::GROUPS])
            || !is_array($this->_data[AbstractFilterGroup::GROUPS])
        ) {
            $this->_data[AbstractFilterGroup::GROUPS] = [];
        }
        $this->_data[AbstractFilterGroup::GROUPS][] = $group;
        return $this;
    }

    /**
     * @param FilterGroupInterface[] $groups
     * @return $this
     */
    public function setGroups($groups)
    {
        return $this->_set(AbstractFilterGroup::GROUPS, $groups);
    }
}
