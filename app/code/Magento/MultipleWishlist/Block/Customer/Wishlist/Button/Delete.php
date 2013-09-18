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
 * Wishlist delete button
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist\Button;

class Delete extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Build block html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_wishlistData->isMultipleEnabled() && $this->isWishlistDeleteable()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * Check whether current wishlist can be deleted
     *
     * @return bool
     */
    protected function isWishlistDeleteable()
    {
        return !$this->_wishlistData->isWishlistDefault($this->getWishlistInstance());
    }

    /**
     * Build wishlist deletion url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('wishlist/index/deletewishlist', array('wishlist_id' => '%item%'));
    }

    /**
     * Retrieve url to redirect customer to after wishlist is deleted
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('wishlist/index/index');
    }
}
