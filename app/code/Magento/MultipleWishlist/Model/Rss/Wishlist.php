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
namespace Magento\MultipleWishlist\Model\Rss;

class Wishlist extends \Magento\Wishlist\Model\Rss\Wishlist
{
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Pricing\Render $priceRender
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Render $priceRender,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
    ) {
        $this->_customerViewHelper = $customerViewHelper;
        $this->_customerAccountService = $customerAccountService;

        parent::__construct(
            $wishlistHelper,
            $outputHelper,
            $imageHelper,
            $priceRender,
            $urlBuilder,
            $scopeConfig,
            $eventManager,
            $layout
        );
    }

    /**
     * Build feed title
     *
     * @return string
     */
    protected function getHeader()
    {
        $customer = $this->wishlistHelper->getCustomer();
        if ($this->getWishlist()->getCustomerId() !== $customer->getId()) {
            /** @var \Magento\Customer\Service\V1\Data\Customer $customer */
            $customer = $this->_customerAccountService->getCustomer($this->getWishlist()->getCustomerId());
        }
        if ($this->wishlistHelper->isWishlistDefault($this->getWishlist())
            && $this->getWishlist()->getName() == $this->wishlistHelper->getDefaultWishlistName()
        ) {
            $title = __("%1's Wish List", $this->_customerViewHelper->getCustomerName($customer));
        } else {
            $title = __("%1's Wish List (%2)", $this->_customerViewHelper->getCustomerName($customer), $this->getWishlist()->getName());
        }

        $newUrl = $this->urlBuilder->getUrl(
            'wishlist/shared/index',
            array('code' => $this->getWishlist()->getSharingCode())
        );

        return array('title' => $title, 'description' => $title, 'link' => $newUrl, 'charset' => 'UTF-8');

    }
}
