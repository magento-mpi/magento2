<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist sidebar block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Customer_Sidebar extends Magento_Wishlist_Block_Customer_Sidebar
{
    /**
     * Wishlist data1
     *
     * @var Enterprise_Wishlist_Helper_Data
     */
    protected $_wishlistData1 = null;

    /**
     * @param Enterprise_Wishlist_Helper_Data $wishlistData1
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Enterprise_Wishlist_Helper_Data $wishlistData1,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_wishlistData1 = $wishlistData1;
        parent::__construct($wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve wishlist helper
     *
     * @return Enterprise_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->_wishlistData1;
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->_getHelper()->isMultipleEnabled()) {
            return __('My Wish Lists <small>(%1)</small>', $this->getItemCount());
        } else {
            return parent::getTitle();
        }
    }

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getHelper()->getWishlistItemCollection();
    }
}
