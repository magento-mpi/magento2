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
     * Retrieve wishlist helper
     *
     * @return Enterprise_Wishlist_Helper_Data
     */
    protected function _getHelper()
    {
        return $this->helper('Enterprise_Wishlist_Helper_Data');
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
