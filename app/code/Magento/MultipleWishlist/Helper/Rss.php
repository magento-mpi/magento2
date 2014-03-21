<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Helper;

class Rss extends \Magento\Rss\Helper\WishlistRss
{

    /**
     * @var Data
     */
    protected $_multiplewishlistHelperData;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\PostData $postDataHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param Data $multiplewishlistHelperData
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Registry $coreRegistry,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\PostData $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        Data $multiplewishlistHelperData
    ) {
        /* TODO this helper must be eliminated after refactoring of Magento\Wishlist\Helper\Data */
        $this->_multiplewishlistHelperData = $multiplewishlistHelperData;

        parent::__construct(
            $context,
            $coreData,
            $coreRegistry,
            $coreStoreConfig,
            $customerSession,
            $wishlistFactory,
            $storeManager,
            $postDataHelper,
            $customerViewHelper,
            $customerBuilder,
            $customerAccountService
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
