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

    const GROUPS = 'groups';

    /**
     * {@inheritdoc}
     */
    public function __construct(AbstractFilterGroupBuilder $builder)
    {
        parent::__construct($builder);
        $this->_data['group_type'] = $this->getGroupType();
    }

    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Filter[]
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? array() : $filters;
    }

    /**
     * Returns a list of filter groups in this group
     *
     * @return \Magento\Customer\Service\V1\Data\Search\FilterGroupInterface[]
     */
    public function getGroups()
    {
        $groups = $this->_get(self::GROUPS);
        return is_null($groups) ? array() : $groups;
    }

    /**
     * Returns the grouping type such as 'OR' or 'AND'.
     *
     * @return string
     */
    abstract public function getGroupType();
}
