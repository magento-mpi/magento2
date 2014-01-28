<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Search;

use Magento\Customer\Service\V1\Dto\Filter;
use Magento\Service\Entity\AbstractDtoBuilder;

/**
 * Builder for AndGroup DTO.
 */
abstract class AbstractFilterGroupBuilder extends AbstractDtoBuilder
{
    /**
     * @param \Magento\Customer\Service\V1\Dto\Filter $filter
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
     * @param \Magento\Customer\Service\V1\Dto\Filter[] $filters
     * @return $this
     */
    public function setFilters($filters) {
        return $this->_set(AbstractFilterGroup::FILTERS, $filters);
    }

    /**
     * @param \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface $group
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
     * @param \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface[] $groups
     * @return $this
     */
    public function setGroups($groups) {
        return $this->_set(AbstractFilterGroup::GROUPS, $groups);
    }
}
