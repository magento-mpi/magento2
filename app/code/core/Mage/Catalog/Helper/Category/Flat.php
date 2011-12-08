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
 * Catalog flat helper
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Helper_Category_Flat extends Mage_Core_Helper_Abstract
{
    const XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY = 'catalog/frontend/flat_catalog_category';

    /**
     * Return true if flat catalog is enabled, rebuileded and is not Admin
     *
     * @param boolean $skipAdmin
     * @return boolean
     */
    public function isEnabled($skipAdminCheck = false)
    {
        $flatFlag = Mage::getStoreConfigFlag(self::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY);
        $isFront = !Mage::app()->getStore()->isAdmin();
        if ($skipAdminCheck === true) {
            $isFront = true;
        }

        return (boolean) $flatFlag && $isFront;
    }

    /**
     * Return true if catalog category flat data rebuilt
     *
     * @return boolean
     */
    public function isRebuilt()
    {
        return Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Category_Flat')->isRebuilt();
    }

    /**
     * Back Flat compatibility: check is built and enabled flat
     *
     * @return bool
     */
    public function isBuilt()
    {
        return $this->isEnabled(true);
    }
}
