<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend for qty increments
 *
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Qtyincrements extends Magento_Core_Model_Config_Data
{
    /**
     * Validate data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        if (floor($value) != $value) {
            throw new Magento_Core_Exception('Decimal qty increments is not allowed.');
        }
    }
}
