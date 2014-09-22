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
namespace Magento\MultipleWishlist\Block;

class Rss extends \Magento\Wishlist\Block\Rss
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
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_customerViewHelper = $customerViewHelper;
        $this->_customerAccountService = $customerAccountService;

        parent::__construct(
            $context,
            $httpContext,
            $productFactory,
            $coreData,
            $wishlistFactory,
            $rssFactory,
            $outputHelper,
            $data
        );
    }

    /**
     * Retrieve Wishlist model
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _getWishlist()
    {
        return $this->_getHelper()->getWishlist();
    }

    /**
     * Build feed title
     *
     * @return string
     */
    protected function _getTitle()
    {
        $customer = $this->_getHelper()->getCustomer();
        if ($this->_getWishlist()->getCustomerId() !== $customer->getId()) {
            /** @var \Magento\Customer\Service\V1\Data\Customer $customer */
            $customer = $this->_customerAccountService->getCustomer($this->_getWishlist()->getCustomerId());
        }
        if ($this->_getHelper()->isWishlistDefault($this->_getWishlist())
            && $this->_getWishlist()->getName() == $this->_getHelper()->getDefaultWishlistName()
        ) {
            return __("%1's Wish List", $this->_customerViewHelper->getCustomerName($customer));
        } else {
            return __("%1's Wish List (%2)", $this->_customerViewHelper->getCustomerName($customer), $this->_getWishlist()->getName());
        }
    }
}
