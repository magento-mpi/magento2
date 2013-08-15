<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Minimum product qty backend model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_System_Config_Backend_Minqty extends Magento_Core_Model_Config_Data
{
    /**
    * Validate minimum product qty value
    *
    * @return Magento_CatalogInventory_Model_System_Config_Backend_Minqty
    */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $minQty = (int)$this->getValue() >= 0 ? (int)$this->getValue() : (int)$this->getOldValue();
        $this->setValue((string) $minQty);
        return $this;
    }
}
