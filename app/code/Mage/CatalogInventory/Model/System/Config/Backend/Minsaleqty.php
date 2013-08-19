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
 * Backend for serialized array data
 *
 */
class Mage_CatalogInventory_Model_System_Config_Backend_Minsaleqty extends Mage_Core_Model_Config_Value
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = Mage::helper('Mage_CatalogInventory_Helper_Minsaleqty')->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = Mage::helper('Mage_CatalogInventory_Helper_Minsaleqty')->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
