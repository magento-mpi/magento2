<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

/**
 * Account dashboard sidebar
 */
class Sidebar extends \Magento\View\Element\Template
{
    protected $_cartItemsCount;

    /**
     * Enter description here...
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    protected $_compareItems;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishListFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory
     */
    protected $_itemsCompareFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerCurrentService
     */
    protected $currentCustomer;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishListFactory
     * @param \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $itemsCompareFactory
     * @param \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishListFactory,
        \Magento\Catalog\Model\Resource\Product\Compare\Item\CollectionFactory $itemsCompareFactory,
        \Magento\Customer\Service\V1\CustomerCurrentService $currentCustomer,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_quoteFactory = $quoteFactory;
        $this->_wishListFactory = $wishListFactory;
        $this->_itemsCompareFactory = $itemsCompareFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->currentCustomer = $currentCustomer;
    }


    public function getShoppingCartUrl()
    {
        return $this->_urlBuilder->getUrl('checkout/cart');
    }

    public function getCartItemsCount()
    {
        if (!$this->_cartItemsCount) {
            $this->_cartItemsCount = $this->_createQuote()
                ->setId($this->_checkoutSession->getQuote()->getId())
                ->getItemsCollection()
                ->getSize();
        }

        return $this->_cartItemsCount;
    }

    public function getWishlist()
    {
        if (!$this->_wishlist) {
            $this->_wishlist = $this->_createWishList()->loadByCustomer($this->_customerSession->getCustomer());
            $this->_wishlist->getItemCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('price')
                ->addAttributeToSelect('small_image')
                ->addAttributeToFilter('store_id', array('in' => $this->_wishlist->getSharedStoreIds()))
                ->addAttributeToSort('added_at', 'desc')
                ->setCurPage(1)
                ->setPageSize(3)
                ->load();
        }

        return $this->_wishlist->getItemCollection();
    }

    public function getWishlistCount()
    {
        return $this->getWishlist()->getSize();
    }

    public function getWishlistAddToCartLink($wishlistItem)
    {
        return $this->_urlBuilder->getUrl('wishlist/index/cart', array('item' => $wishlistItem->getId()));
    }

    public function getCompareItems()
    {
        if ( !$this->_compareItems ) {
            $this->_compareItems =
                $this->_createProductCompareCollection()->setStoreId($this->_storeManager->getStore()->getId());
            $this->_compareItems->setCustomerId(
                $this->currentCustomer->getCustomerId()
            );
            $this->_compareItems
                ->addAttributeToSelect('name')
                ->useProductItem()
                ->load();
        }
        return $this->_compareItems;
    }

    public function getCompareJsObjectName()
    {
        return "dashboardSidebarCompareJsObject";
    }

    public function getCompareRemoveUrlTemplate()
    {
        return $this->getUrl('catalog/product_compare/remove', ['product'=>'#{id}']);
    }

    public function getCompareAddUrlTemplate()
    {
        return $this->getUrl('catalog/product_compare/add');
    }

    public function getCompareUrl()
    {
        return $this->getUrl('catalog/product_compare');
    }

    /**
     * @return \Magento\Sales\Model\Quote
     */
    protected function _createQuote()
    {
        return $this->_quoteFactory->create();
    }

    /**
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _createWishList()
    {
        return $this->_wishListFactory->create();
    }

    /**
     * @return \Magento\Catalog\Model\Resource\Product\Compare\Item\Collection
     */
    protected function _createProductCompareCollection()
    {
        return $this->_itemsCompareFactory->create();
    }
}
