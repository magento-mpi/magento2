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
 * Wishlist rss feed block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Rss extends \Magento\Rss\Block\Wishlist
{
    /**
     * Retrieve Wishlist model
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = $this->_wishlistFactory->create();
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if ($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Build feed title
     *
     * @return string
     */
    protected function _getTitle()
    {
        $customer = $this->_getCustomer();
        if ($this->_getWishlist()->getCustomerId() !== $customer->getId()) {
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->_customerFactory->create();
            $customer->load($this->_getWishlist()->getCustomerId());
        }
        if ($this->_wishlistHelper->isWishlistDefault($this->_getWishlist())
            && $this->_getWishlist()->getName() == $this->_wishlistHelper->getDefaultWishlistName()
        ) {
            return __("%1's Wish List", $customer->getName());
        } else {
            return __("%1's Wish List (%2)", $customer->getName(), $this->_getWishlist()->getName());
        }
    }
}
