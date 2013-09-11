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
 * Wishlist sidebar block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Customer;

class Sidebar extends \Magento\Wishlist\Block\Customer\Sidebar
{
    /**
     * Retrieve wishlist helper
     *
     * @return \Magento\MultipleWishlist\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->helper('Magento\MultipleWishlist\Helper\Data');
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
     * @return \Magento\Wishlist\Model\Resource\Item\Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->_getHelper()->getWishlistItemCollection();
    }
}
