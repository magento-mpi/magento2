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
abstract class AbstractFilterGroup extends AbstractObject implements FilterGroupInterface
{
    const FILTERS = 'filters';
    const AND_GROUPS = 'andGroups';
    const OR_GROUPS = 'orGroups';

    /**
     * {@inheritdoc}
     */
    public function __construct(AbstractFilterGroupBuilder $filterGroupBuilder)
    {
        parent::__construct($filterGroupBuilder);
        //$this->_data['group_type'] = $this->getGroupType();
    }

    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Service\Data\Filter[]
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\AndGroup[]
     */
    public function getAndGroups()
    {
        $groups = $this->_get(self::AND_GROUPS);
        return is_null($groups) ? [] : $groups;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\OrGroup[]
     */
    public function getOrGroups()
    {
        $groups = $this->_get(self::OR_GROUPS);
        return is_null($groups) ? [] : $groups;
    }
}
