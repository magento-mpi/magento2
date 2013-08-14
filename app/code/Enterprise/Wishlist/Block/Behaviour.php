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
 * Behaviour block
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Behaviour extends Magento_Core_Block_Template
{
    /**
     * Retrieve wishlists items
     *
     * @return Magento_Wishlist_Model_Resource_Wishlist_Collection
     */
    public function getWishlists()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getCustomerWishlists();
    }

    /**
     * Retrieve add item to wishlist url
     *
     * @return string
     */
    public function getAddItemUrl()
    {
        return $this->getUrl('wishlist/index/add', array('wishlist_id' => '%wishlist_id%'));
    }

    /**
     * Retrieve Wishlist creation url
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('wishlist/index/createwishlist');
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getDefaultWishlist()
    {
        return Mage::helper('Enterprise_Wishlist_Helper_Data')->getDefaultWishlist();
    }

    /**
     * Check whether customer reached wishlist limit
     *
     * @param Magento_Wishlist_Model_Resource_Wishlist_Collection
     * @return bool
     */
    public function canCreateWishlists($wishlistList)
    {
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
        return !Mage::helper('Enterprise_Wishlist_Helper_Data')->isWishlistLimitReached($wishlistList) && $customerId;
    }

    /**
     * Get customer wishlist list
     *
     * @return array
     */
    public function getWishlistShortList()
    {
        $wishlistData = array();
        foreach($this->getWishlists() as $wishlist){
            $wishlistData[] = array('id' => $wishlist->getId(), 'name' => $wishlist->getName());
        }
        return $wishlistData;
    }

    /**
     * Render block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (Mage::helper('Enterprise_Wishlist_Helper_Data')->isMultipleEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}
