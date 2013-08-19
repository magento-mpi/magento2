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
 * Catalog Product Website Model
 *
 * @method Mage_Catalog_Model_Resource_Product_Website _getResource()
 * @method Mage_Catalog_Model_Resource_Product_Website getResource()
 * @method int getWebsiteId()
 * @method Mage_Catalog_Model_Product_Website setWebsiteId(int $value)
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Website extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Catalog_Model_Resource_Product_Website');
    }

    /**
     * Retrieve Resource instance wrapper
     *
     * @return Mage_Catalog_Model_Resource_Product_Website
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Removes products from websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Product_Website
     */
    public function removeProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->removeProducts($websiteIds, $productIds);
        }
        catch (Exception $e) {
            Mage::throwException(
                __('Something went wrong removing products from the websites.')
            );
        }
        return $this;
    }

    /**
     * Add products to websites
     *
     * @param array $websiteIds
     * @param array $productIds
     * @return Mage_Catalog_Model_Product_Website
     */
    public function addProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->addProducts($websiteIds, $productIds);
        }
        catch (Exception $e) {
            Mage::throwException(
                __('Something went wrong adding products to websites.')
            );
        }
        return $this;
    }

    /**
     * Retrieve product websites
     * Return array with key as product ID and value array of websites
     *
     * @param int|array $productIds
     * @return array
     */
    public function getWebsites($productIds)
    {
        return $this->_getResource()->getWebsites($productIds);
    }
}
