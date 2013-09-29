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
 * Wishlist item management column (copy, move, etc.)
 */
namespace Magento\MultipleWishlist\Block\Customer\Wishlist\Item\Column;

class Management
    extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Construct
     *
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($coreRegistry, $wishlistData, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_wishlistData->isMultipleEnabled();
    }

    /**
     * Retrieve current customer wishlist collection
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistData->getCustomerWishlists();
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistData->getDefaultWishlist();
    }

    /**
     * Retrieve current wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getCurrentWishlist()
    {
        return $this->_wishlistData->getWishlist();
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists(\Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists)
    {
        $customerId = $this->_customerSession->getCustomerId();
        return !$this->_wishlistData->isWishlistLimitReached($wishlists) && $customerId;
    }

    /**
     * Get wishlist item copy url
     *
     * @return string
     */
    public function getCopyItemUrl()
    {
        return $this->getUrl('wishlist/index/copyitem');
    }
}
