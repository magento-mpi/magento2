<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;


class AvailabilityFlag extends \Magento\CatalogSearch\Model\Layer\AvailabilityFlag
{
    public function isEnabled($layer, $filters)
    {
        if ($this->_searchData->isThirdPartSearchEngine() && $this->_searchData->isActiveEngine()) {
            return \Magento\Catalog\Model\Layer\AvailabilityFlag::isEnabled($layer, $filters);
        }
        return parent::isEnabled($layer, $filters);
    }
} 
