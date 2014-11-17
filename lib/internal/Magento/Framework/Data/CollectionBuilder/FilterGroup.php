<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Data\CollectionBuilder;

use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Groups two or more filters together using a logical OR
 */
class FilterGroup extends AbstractExtensibleModel
{
    const FILTERS = 'filters';

    /**
     * Returns a list of filters in this group
     *
     * @return Filter[]|null
     */
    public function getFilters()
    {
        $filters = $this->_getData(self::FILTERS);
        return is_null($filters) ? [] : $filters;
    }
}
