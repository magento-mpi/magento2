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
     * @var \Magento\MultipleWishlist\Helper\Data
     */
    protected $_multipleWishlistHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\MultipleWishlist\Helper\Data $multipleWishlistHelper
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\MultipleWishlist\Helper\Data $multipleWishlistHelper,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_multipleWishlistHelper = $multipleWishlistHelper;
        parent::__construct(
            $context,
            $customerSession,
            $productFactory,
            $data,
            $priceBlockTypes
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
