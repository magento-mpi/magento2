<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data\Search;

use Magento\Service\Data\AbstractObject;

/**
 * Groups two or more filters together using a logical group type
 */
abstract class AbstractFilterGroup extends AbstractObject
{
    const FILTERS = 'filters';
    const AND_GROUPS = 'andGroups';
    const OR_GROUPS = 'orGroups';

    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Service\V1\Data\Filter[]|null
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\AndGroup[]|null
     */
    public function getAndGroups()
    {
        $groups = $this->_get(self::AND_GROUPS);
        return is_null($groups) ? [] : $groups;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\OrGroup[]|null
     */
    public function getOrGroups()
    {
        $groups = $this->_get(self::OR_GROUPS);
        return is_null($groups) ? [] : $groups;
    }
}
