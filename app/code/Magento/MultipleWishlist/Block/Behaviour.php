<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Behaviour block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Behaviour extends \Magento\Core\Block\Template
{
    /**
     * Retrieve wishlists items
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getCustomerWishlists();
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
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return \Mage::helper('Magento\MultipleWishlist\Helper\Data')->getDefaultWishlist();
    }

    /**
     * Check whether customer reached wishlist limit
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection
     * @return bool
     */
    public function canCreateWishlists($wishlistList)
    {
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();
        return !\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isWishlistLimitReached($wishlistList) && $customerId;
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
        if (\Mage::helper('Magento\MultipleWishlist\Helper\Data')->isMultipleEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }
}
