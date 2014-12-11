<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Helper;

class Rss extends \Magento\Wishlist\Helper\Rss
{
    /**
     * @var Data
     */
    protected $_multiplewishlistHelperData;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param Data $multiplewishlistHelperData
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\PostData $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Data $multiplewishlistHelperData
    ) {
        $this->_multiplewishlistHelperData = $multiplewishlistHelperData;

        parent::__construct(
            $context,
            $coreRegistry,
            $scopeConfig,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $wishlistProvider,
            $customerBuilder,
            $customerRepository
        );
    }

    /**
     * Check whether given wishlist is default for it's customer
     *
     * @param \Magento\Wishlist\Model\Wishlist $wishlist
     * @return bool
     */
    public function isWishlistDefault(\Magento\Wishlist\Model\Wishlist $wishlist)
    {
        return $this->_multiplewishlistHelperData->isWishlistDefault($wishlist);
    }
}
