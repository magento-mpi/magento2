<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "My Wish Lists" link
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Link extends \Magento\Wishlist\Block\Link
{
    /**
     * Wishlist data
     *
     * @var \Magento\MultipleWishlist\Helper\Data|null
     */
    protected $_wishlistData = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\MultipleWishlist\Helper\Data $wishlistData
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
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
     * @return string
     */
    public function getLabel()
    {
        if ($this->_wishlistData->isMultipleEnabled()) {
            return __('My Wish Lists');
        } else {
            return parent::getLabel();
        }
    }
}
