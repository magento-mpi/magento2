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
 * Catalog Product Website Model
 *
 * @method Magento_Catalog_Model_Resource_Product_Website _getResource()
 * @method Magento_Catalog_Model_Resource_Product_Website getResource()
 * @method int getWebsiteId()
 * @method Magento_Catalog_Model_Product_Website setWebsiteId(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Website extends Magento_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Product_Website');
    }

    /**
     * Retrieve Resource instance wrapper
     *
     * @return Magento_Catalog_Model_Resource_Product_Website
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
     * @return Magento_Catalog_Model_Product_Website
     * @throws Magento_Core_Exception
     */
    public function removeProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->removeProducts($websiteIds, $productIds);
        } catch (Exception $e) {
            throw new Magento_Core_Exception(
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
     * @return Magento_Catalog_Model_Product_Website
     * @throws Magento_Core_Exception
     */
    public function addProducts($websiteIds, $productIds)
    {
        try {
            $this->_getResource()->addProducts($websiteIds, $productIds);
        } catch (Exception $e) {
            throw new Magento_Core_Exception(
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
