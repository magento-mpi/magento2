<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch layer attribute filter
 *
 */
namespace Magento\CatalogSearch\Model\Layer\Filter;

class Attribute extends \Magento\Catalog\Model\Layer\Filter\Attribute
{
    /**
     * Check whether specified attribute can be used in LN
     *
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute  $attribute
     * @return bool
     */
    protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterableInSearch();
    }

}
