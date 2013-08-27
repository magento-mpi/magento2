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
 * Url rewrite suffix backend
 */
class Magento_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix extends Magento_Core_Model_Config_Data
{
    /**
     * Check url rewrite suffix - whether we can support it
     *
     * @return Magento_Catalog_Model_System_Config_Backend_Catalog_Url_Rewrite_Suffix
     */
    protected function _beforeSave()
    {
        Mage::helper('Magento_Core_Helper_Url_Rewrite')->validateSuffix($this->getValue());
        return $this;
    }
}
