<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wishlist rss feed block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Model\Plugin\Rss;

class Wishlist
{
    /**
     * @var \Magento\MultipleWishlist\Helper\Rss
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $customerViewHelper;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $customerAccountService;

    /**
     * @param \Magento\MultipleWishlist\Helper\Rss $wishlistHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\MultipleWishlist\Helper\Rss $wishlistHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->wishlistHelper = $wishlistHelper;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerAccountService = $customerAccountService;
    }

    /**
    /**
     * @param \Magento\Wishlist\Model\Rss\Wishlist $subject
     * @param \Closure $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetHeader(\Magento\Wishlist\Model\Rss\Wishlist $subject, \Closure $proceed)
    {
        if (!(bool)$this->scopeConfig->getValue(
            'wishlist/general/multiple_active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return $proceed();
        }

        $customer = $this->wishlistHelper->getCustomer();
        $wishlist = $this->wishlistHelper->getWishlist();
        if ($wishlist->getCustomerId() !== $customer->getId()) {
            /** @var \Magento\Customer\Service\V1\Data\Customer $customer */
            $customer = $this->customerAccountService->getCustomer($wishlist->getCustomerId());
        }
        if ($this->wishlistHelper->isWishlistDefault($wishlist)
            && $wishlist->getName() == $this->wishlistHelper->getDefaultWishlistName()
        ) {
            $title = __("%1's Wish List", $this->customerViewHelper->getCustomerName($customer));
        } else {
            $title = __("%1's Wish List (%2)", $this->customerViewHelper->getCustomerName($customer), $wishlist->getName());
        }

        $newUrl = $this->urlBuilder->getUrl(
            'wishlist/shared/index',
            array('code' => $wishlist->getSharingCode())
        );

        return array('title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8');
    }
}
