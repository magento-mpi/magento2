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
 * Backend for serialized array data
 *
 */
class Magento_CatalogInventory_Model_System_Config_Backend_Minsaleqty extends Magento_Core_Model_Config_Value
{
    /**
     * Process data after load
     */
    protected function _afterLoad()
    {
        $value = $this->getValue();
        $value = Mage::helper('Magento_CatalogInventory_Helper_Minsaleqty')->makeArrayFieldValue($value);
        $this->setValue($value);
    }

    /**
     * Prepare data before save
     */
    protected function _beforeSave()
    {
        $value = $this->getValue();
        $value = Mage::helper('Magento_CatalogInventory_Helper_Minsaleqty')->makeStorableArrayFieldValue($value);
        $this->setValue($value);
    }
}
