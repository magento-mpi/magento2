<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Service\V1\Data\Search;

use Magento\Service\Data\AbstractObject;

/**
 * Groups two or more filters together using a logical OR
 */
class FilterGroup extends AbstractObject
{
    const FILTERS = 'filters';

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
}
