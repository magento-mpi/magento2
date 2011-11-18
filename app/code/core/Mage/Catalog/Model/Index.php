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
 * Catalog Category/Product Index
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Index
{
    /**
     * Rebuild indexes
     *
     * @return Mage_Catalog_Model_Index
     */
    public function rebuild()
    {
        Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Category')
            ->refreshProductIndex();
        foreach (Mage::app()->getStores() as $store) {
            Mage::getResourceSingleton('Mage_Catalog_Model_Resource_Product')
                ->refreshEnabledIndex($store);
        }
        return $this;
    }
}
