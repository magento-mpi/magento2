<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Catalog Inventory Manage Stock Config Backend Model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Catalog_Inventory_Managestock
    extends Mage_Core_Model_Config_Data
{
/**
     * After change Catalog Inventory Manage value process
     *
     * @return Mage_Backend_Model_Config_Backend_Catalog_Inventory_Managestock
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = Mage::getConfig()->getNode(
            Mage_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            Mage::getSingleton('Mage_CatalogInventory_Model_Stock_Status')->rebuild();
        }

        return $this;
    }
}
