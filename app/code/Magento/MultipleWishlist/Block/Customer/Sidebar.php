<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist sidebar block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block\Customer;

class Sidebar extends \Magento\Wishlist\Block\Customer\Sidebar
{
    /**
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_multipleWishlistHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\MultipleWishlist\Helper\Data $multipleWishlistHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        array $data = array()
    ) {
        $this->_multipleWishlistHelper = $multipleWishlistHelper;
        parent::__construct(
            $context,
            $httpContext,
            $productRepository,
            $data
        );
    }

    /**
     * Retrieve wishlist helper
     *
     * @return \Magento\MultipleWishlist\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->_multipleWishlistHelper;
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->_getHelper()->isMultipleEnabled()) {
            return __('My Wish Lists');
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
