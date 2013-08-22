<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Catalog_Model_Config_Backend_Seo_Product extends Magento_Core_Model_Config_Data
{
    /**
     * Refresh category url rewrites if configuration was changed
     *
     * @return Magento_Catalog_Model_Config_Backend_Seo_Product
     */
    protected function _afterSave()
    {
        return $this;
    }
}
