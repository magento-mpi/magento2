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
 * Catalog Search change Search Type backend model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Catalog_Search_Type extends Mage_Core_Model_Config_Data
{
    /**
     * After change Catalog Search Type process
     *
     * @return Mage_Backend_Model_Config_Catalog_Search_Type
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
            Mage::getSingleton('Mage_CatalogSearch_Model_Fulltext')->resetSearchResults();
        }

        return $this;
    }
}
