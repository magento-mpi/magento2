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
 * Catalog Comapare Products Sidebar Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Block_Product_Compare_Sidebar extends Magento_Catalog_Block_Product_Compare_Abstract
{
    /**
     * Compare Products Collection
     *
     * @var null|Magento_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    protected $_itemsCollection = null;

    /**
     * Initialize block
     *
     */
    protected function _construct()
    {
        $this->setId('compare');
    }

    /**
     * Retrieve Compare Products Collection
     *
     * @return Magento_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    public function getItems()
    {
        if ($this->_itemsCollection) {
            return $this->_itemsCollection;
        }
        return $this->_getHelper()->getItemCollection();
    }

    /**
     * Set Compare Products Collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item_Collection $collection
     * @return Magento_Catalog_Block_Product_Compare_Sidebar
     */
    public function setItems($collection)
    {
        $this->_itemsCollection = $collection;
        return $this;
    }

    /**
     * Retrieve compare product helper
     *
     * @return Magento_Catalog_Helper_Product_Compare
     */
    public function getCompareProductHelper()
    {
        return $this->_getHelper();
    }

    /**
     * Retrieve Clean Compared Items URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        return $this->_getHelper()->getClearListUrl();
    }

    /**
     * Retrieve Full Compare page URL
     *
     * @return string
     */
    public function getCompareUrl()
    {
        return $this->_getHelper()->getListUrl();
    }
}
