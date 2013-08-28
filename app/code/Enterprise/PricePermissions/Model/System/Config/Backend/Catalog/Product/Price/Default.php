<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermission
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Default Product Price Backend Model
 *
 * @category    Enterprise
 * @package     Enterprise_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PricePermissions_Model_System_Config_Backend_Catalog_Product_Price_Default
    extends Magento_Core_Model_Config_Data
{
    /**
     * Check permission to edit product prices before the value is saved
     *
     * @return Enterprise_PricePermossions_Model_System_Config_Backend_Catalog_Product_Price_Default
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $defaultProductPriceValue = floatval($this->getValue());
        if (!Mage::helper('Enterprise_PricePermissions_Helper_Data')->getCanAdminEditProductPrice()
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
     * @return Enterprise_PricePermossions_Model_System_Config_Backend_Catalog_Product_Price_Default
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if (!Mage::helper('Enterprise_PricePermissions_Helper_Data')->getCanAdminReadProductPrice()) {
            $this->setValue(null);
        }
        return $this;
    }
}
