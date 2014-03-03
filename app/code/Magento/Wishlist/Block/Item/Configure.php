<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist Item Configure block
 * Serves for configuring item on product view page
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @module     Wishlist
 */
namespace Magento\Wishlist\Block\Item;

class Configure extends \Magento\View\Element\Template
{
    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Returns product being edited
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * Get update params for http post
     *
     * @return bool|string
     */
    public function getUpdateParams()
    {
        return $this->_wishlistData->getUpdateParams($this->getWishlistItem());
    }

    /**
     * Returns wishlist item being configured
     *
     * @return \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item
     */
    protected function getWishlistItem()
    {
        return $this->_coreRegistry->registry('wishlist_item');
    }

    /**
     * Configure product view blocks
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        // Set custom add to cart url
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
            $url = $this->_wishlistData->getAddToCartUrl($this->getWishlistItem());
            $block->setCustomAddToCartUrl($url);
        }

        return parent::_prepareLayout();
    }
}
