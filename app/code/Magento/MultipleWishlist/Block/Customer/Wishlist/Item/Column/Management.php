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

class Management extends \Magento\Wishlist\Block\Customer\Wishlist\Item\Column
{
    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param array $data
     * @param array $priceBlockTypes
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        parent::__construct(
            $context,
            $catalogConfig,
            $registry,
            $taxData,
            $catalogData,
            $mathRandom,
            $cartHelper,
            $wishlistHelper,
            $compareProduct,
            $layoutHelper,
            $imageHelper,
            $httpContext,
            $customerSession,
            $productFactory,
            $data,
            $priceBlockTypes
        );
    }

    /**
     * Render block
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_wishlistHelper->isMultipleEnabled();
    }

    /**
     * Retrieve current customer wishlist collection
     *
     * @return \Magento\Wishlist\Model\Resource\Wishlist\Collection
     */
    public function getWishlists()
    {
        return $this->_wishlistHelper->getCustomerWishlists();
    }

    /**
     * Retrieve default wishlist for current customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getDefaultWishlist()
    {
        return $this->_wishlistHelper->getDefaultWishlist();
    }

    /**
     * Retrieve current wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getCurrentWishlist()
    {
        return $this->_wishlistHelper->getWishlist();
    }

    /**
     * Check whether user multiple wishlist limit reached
     *
     * @param \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists
     * @return bool
     */
    public function canCreateWishlists(\Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlists)
    {
        $customerId = $this->currentCustomer->getCustomerId();
        return !$this->_wishlistHelper->isWishlistLimitReached($wishlists) && $customerId;
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
