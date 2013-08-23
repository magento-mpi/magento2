<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Default Product Price Backend Model
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
    extends Magento_Core_Model_Config_Data
{
    /**
     * Check permission to edit product prices before the value is saved
     *
     * @return Magento_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $defaultProductPriceValue = floatval($this->getValue());
        if (!Mage::helper('Magento_PricePermissions_Helper_Data')->getCanAdminEditProductPrice()
            || ($defaultProductPriceValue < 0)
        ) {
            $defaultProductPriceValue = floatval($this->getOldValue());
        }
        $this->setValue((string)$defaultProductPriceValue);
        return $this;
    }

    /**
     * Check permission to read product prices before the value is shown to user
     *
     * @return Magento_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if (!Mage::helper('Magento_PricePermissions_Helper_Data')->getCanAdminReadProductPrice()) {
            $this->setValue(null);
        }
        return $this;
    }
}
