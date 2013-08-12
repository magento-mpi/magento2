<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat product on/off backend
 */
class Magento_Catalog_Model_System_Config_Backend_Catalog_Product_Flat extends Magento_Core_Model_Config_Data
{
    /**
     * After enable flat products required reindex
     *
     * @return Magento_Catalog_Model_System_Config_Backend_Catalog_Product_Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalog_product_flat')
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
