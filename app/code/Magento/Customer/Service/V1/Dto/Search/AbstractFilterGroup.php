<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Dto\Search;

use Magento\Service\Entity\AbstractDto;

/**
 * Groups two or more filters together using a logical group type
 */
abstract class AbstractFilterGroup extends AbstractDto implements FilterGroupInterface
{
    const FILTERS = 'filters';
    const GROUPS = 'groups';

    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Customer\Service\V1\Dto\Filter[]
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Dto\Search\FilterGroupInterface[]
     */
    public function getGroups()
    {
        $groups = $this->_get(self::GROUPS);
        return is_null($groups) ? [] : $groups;
    }

    /**
     * Returns the grouping type such as 'OR' or 'AND'.
     *
     * @return string
     */
    public abstract function getGroupType();
}
