<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute model, UnitPrice abstract
 *
 * @category   Saas
 * @package    Saas_UnitPrice
 */
abstract class Saas_UnitPrice_Model_Entity_Resource_Eav_Attribute_Abstract
    extends Magento_Catalog_Model_Resource_Eav_Attribute
{
    protected $_unitPriceDefaultKey = '';

    public function getDefaultValue()
    {
        $value = Mage::helper('Saas_UnitPrice_Helper_Data')
            ->getConfig($this->_unitPriceDefaultKey);

        return $value;
    }
}
