<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layer attribute filter
 *
 */
namespace Magento\Catalog\Model\Layer\Search\Filter;

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
