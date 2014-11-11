<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data\CollectionBuilder;

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Groups two or more filters together using a logical OR
 */
class FilterGroup extends AbstractExtensibleObject
{
    const FILTERS = 'filters';

    /**
     * Returns a list of filters in this group
     *
     * @return Filter[]|null
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }
}
