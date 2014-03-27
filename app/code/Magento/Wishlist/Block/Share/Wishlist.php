<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Wishlist block shared items
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Block\Share;

class Wishlist extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Customer instance
     *
     * @var \Magento\Customer\Service\V1\Data\Customer
     */
    protected $_customer = null;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\App\Http\Context $httpContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     * @param array $priceBlockTypes
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\App\Http\Context $httpContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService,
        array $data = array(),
        array $priceBlockTypes = array()
    ) {
        $this->_customerAccountService = $customerAccountService;
        parent::__construct(
            $context,
            $httpContext,
            $productFactory,
            $data,
            $priceBlockTypes
        );
    }

    /**
     * Prepare global layout
     *
     * @return $this
     *
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->getHeader());
        }
        return $this;
    }

    /**
     * Retrieve Shared Wishlist Customer instance
     *
     * @return \Magento\Customer\Service\V1\Data\Customer
     */
    public function getWishlistCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = $this->_customerAccountService->getCustomer($this->_getWishlist()->getCustomerId());
        }

        return $this->_customer;
    }

    /**
     * Retrieve Page Header
     *
     * @return string
     */
    public function getHeader()
    {
        return __("%1's Wish List", $this->escapeHtml($this->getWishlistCustomer()->getFirstname()));
    }
}
