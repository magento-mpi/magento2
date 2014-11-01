<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api\Search;

use Magento\Framework\Service\Data\AbstractExtensibleObject;

/**
 * Groups two or more filters together using a logical OR
 */
class FilterGroup extends AbstractExtensibleObject
{
    const FILTERS = 'filters';

    /**
     * Returns a list of filters in this group
     *
     * @return \Magento\Framework\Api\Filter[]|null
     */
    public function getFilters()
    {
        $filters = $this->_get(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }
}
