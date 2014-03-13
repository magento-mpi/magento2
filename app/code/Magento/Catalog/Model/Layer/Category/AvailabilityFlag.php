<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Category;

use Magento\Catalog\Model\Layer\AvailabilityFlagInterface;

class AvailabilityFlag implements AvailabilityFlagInterface
{
    /**
     * Is filter enabled
     *
     * @param $layer
     * @param $filters
     * @return bool
     */
    public function isEnabled($layer, $filters)
    {
        return $this->canShowOptions($filters) || count($layer->getState()->getFilters());
    }

    /**
     * @param array $filters
     * @return bool
     */
    protected function canShowOptions($filters)
    {
        foreach ($filters as $filter) {
            if ($filter->getItemsCount()) {
                return true;
            }
        }

        return false;
    }
}
