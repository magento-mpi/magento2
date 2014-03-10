<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Service\Data\AbstractObjectBuilder;

/**
 * Abstract Builder for AbstractFilterGroup DATA.
 */
abstract class AbstractFilterGroupBuilder extends AbstractObjectBuilder
{
    /**
     * Add filter
     *
     * @param \Magento\Customer\Service\V1\Data\Filter $filter
     * @return $this
     */
    public function addFilter(\Magento\Customer\Service\V1\Data\Filter $filter)
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
     * Set filters
     *
     * @param \Magento\Customer\Service\V1\Data\Filter[] $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        return $this->_set(AbstractFilterGroup::FILTERS, $filters);
    }

    /**
     * Add filter group
     *
     * @param \Magento\Customer\Service\V1\Data\Search\FilterGroupInterface $group
     * @return $this
     */
    public function addGroup(\Magento\Customer\Service\V1\Data\Search\FilterGroupInterface $group)
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
     * Set filter groups
     *
     * @param \Magento\Customer\Service\V1\Data\Search\FilterGroupInterface[] $groups
     * @return $this
     */
    public function setGroups($groups)
    {
        return $this->_set(AbstractFilterGroup::GROUPS, $groups);
    }
}
