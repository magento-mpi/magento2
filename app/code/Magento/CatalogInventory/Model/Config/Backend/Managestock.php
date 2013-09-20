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
 * Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Magento
 * @package    Magento_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Config_Backend_Managestock
    extends Magento_Core_Model_Config_Value
{
/**
     * After change Catalog Inventory Manage value process
     *
     * @return Magento_CatalogInventory_Model_Config_Backend_Managestock
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_config->getValue(
            Magento_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')->rebuild();
        }

        return $this;
    }
}
