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
 * Wishlist rss feed block
 *
 * @category    Magento
 * @package     Magento_MultipleWishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\MultipleWishlist\Block;

class Rss extends \Magento\Rss\Block\Wishlist
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Checkout\Helper\Cart $cartHelper
     * @param \Magento\MultipleWishlist\Helper\Rss $wishlistHelper
     * @param \Magento\Catalog\Helper\Product\Compare $compareProduct
     * @param \Magento\Theme\Helper\Layout $layoutHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Rss\Model\RssFactory $rssFactory
     * @param \Magento\Catalog\Helper\Output $outputHelper
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
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
        \Magento\MultipleWishlist\Helper\Rss $wishlistHelper,
        \Magento\Catalog\Helper\Product\Compare $compareProduct,
        \Magento\Theme\Helper\Layout $layoutHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Rss\Model\RssFactory $rssFactory,
        \Magento\Catalog\Helper\Output $outputHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_customerViewHelper = $customerViewHelper;
        $this->_customerAccountService = $customerAccountService;

        /* TODO this constructor must be eliminated after elimination of \Magento\MultipleWishlist\Helper\Rss */
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
            $productFactory,
            $coreData,
            $wishlistFactory,
            $rssFactory,
            $outputHelper,
            $data,
            $priceBlockTypes
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
