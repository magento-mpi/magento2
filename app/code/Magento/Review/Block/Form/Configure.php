<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Form;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Configure extends \Magento\Review\Block\Form
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Framework\Session\Generic $reviewSession
     * @param \Magento\Review\Helper\Data $reviewData
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Review\Helper\Data $reviewData,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_cart = $cart;
        parent::__construct(
            $context,
            $coreData,
            $reviewSession,
            $reviewData,
            $customerSession,
            $productRepository,
            $ratingFactory,
            $messageManager,
            $httpContext,
            $customerUrl,
            $data
        );
    }

    /**
     * Get review product id
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * Get product
     *
     * @return Product
     */
    protected function getProduct()
    {
        $product = $this->_coreRegistry->registry('product');
        if (!$product) {
            $product = $this->_cart->getQuote()->getItemById($this->getRequest()->getParam('id'))->getProduct();
        }
        return $product;
    }
}
