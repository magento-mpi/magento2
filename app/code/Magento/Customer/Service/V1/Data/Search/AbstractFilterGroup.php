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
    public function __construct(AbstractFilterGroupBuilder $filterGroupBuilder)
    {
        parent::__construct($filterGroupBuilder);
        $this->_data['group_type'] = $this->getGroupType();
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups()
    {
        $groups = $this->_get(self::GROUPS);
        return is_null($groups) ? [] : $groups;
    }

    /**
     * {@inheritdoc}
     */
    public abstract function getGroupType();
}
