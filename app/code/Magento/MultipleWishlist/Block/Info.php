<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Wishlist view block
 */
namespace Magento\MultipleWishlist\Block;

class Info extends \Magento\Wishlist\Block\AbstractBlock
{
    /**
     * Customer model factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $httpContext,
            $productRepository,
            $data
        );
    }

    /**
     * Add form submission url
     *
     * @return string
     */
    public function getToCartUrl()
    {
        return $this->getUrl('wishlist/search/addtocart');
    }

    /**
     * Retrieve wishlist owner instance
     *
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getWishlistOwner()
    {
        /** @var \Magento\Customer\Model\Customer $owner */
        $owner = $this->_customerFactory->create();
        $owner->load($this->_getWishlist()->getCustomerId());
        return $owner;
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            'wishlist/search/results',
            ['_query' => ['params' => $this->_customerSession->getLastWishlistSearchParams()]]
        );
    }
}
