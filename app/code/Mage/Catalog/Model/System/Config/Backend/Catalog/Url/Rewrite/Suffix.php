<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Url rewrite suffix backend
 */
class Mage_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix extends Magento_Core_Model_Config_Data
{
    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return Mage_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix
     */
    protected function _beforeSave()
    {
        Mage::helper('Magento_Core_Helper_Url_Rewrite')->validateSuffix($this->getValue());
        return $this;
    }
}
