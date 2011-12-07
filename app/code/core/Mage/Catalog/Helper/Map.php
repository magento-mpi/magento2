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
 * Catalog (site)map helper
 */
class Mage_Catalog_Helper_Map extends Mage_Core_Helper_Abstract
{
    CONST XML_PATH_USE_TREE_MODE = 'catalog/sitemap/tree_mode';

    public function getCategoryUrl()
    {
        return $this->_getUrl('catalog/seo_sitemap/category');
    }

    public function getProductUrl()
    {
        return $this->_getUrl('catalog/seo_sitemap/product');
    }

    /**
     * Return true if category tree mode enabled
     *
     * @return boolean
     */
    public function getIsUseCategoryTreeMode()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_USE_TREE_MODE);
    }

}
