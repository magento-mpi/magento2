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
 * Catalog Category/Product Index
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Index
{
    /**
     * Rebuild indexes
     *
     * @return Magento_Catalog_Model_Index
     */
    public function rebuild()
    {
        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category')
            ->refreshProductIndex();
        foreach (Mage::app()->getStores() as $store) {
            Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product')
                ->refreshEnabledIndex($store);
        }
        return $this;
    }
}
