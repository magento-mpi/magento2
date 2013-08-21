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
class Magento_CatalogSearch_Model_Layer_Filter_Attribute extends Magento_Catalog_Model_Layer_Filter_Attribute
{
    /**
     * Check whether specified attribute can be used in LN
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute  $attribute
     * @return bool
     */
    protected function _getIsFilterableAttribute($attribute)
    {
        return $attribute->getIsFilterableInSearch();
    }

}
