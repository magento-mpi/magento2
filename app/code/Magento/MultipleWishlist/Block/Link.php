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
 * "My Wish Lists" link
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Link extends \Magento\Wishlist\Block\Link
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\MultipleWishlist\Helper\Data $wishlistData,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        parent::__construct($context, $wishlistHelper, $data);
    }

    /**
     * Count items in wishlist
     *
     * @return int
     */
    protected function _getItemCount()
    {
        return $this->_wishlistData->getItemCount();
    }

    /**
     * Create Button label
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            if ($count > 1) {
                return __('My Wish Lists (%1 items)', $count);
            } else if ($count == 1) {
                return __('My Wish Lists (%1 item)', $count);
            } else {
                return __('My Wish Lists');
            }
        } else {
            return parent::_createLabel($count);
        }
    }
}
