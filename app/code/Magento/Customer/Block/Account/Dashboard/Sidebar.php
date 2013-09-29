<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Account dashboard sidebar
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Customer_Block_Account_Dashboard_Sidebar extends Magento_Core_Block_Template
{
    protected $_cartItemsCount;

    /**
     * Enter description here...
     *
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    protected $_compareItems;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Sales_Model_QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishListFactory;

    /**
     * @var Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory
     */
    protected $_itemsCompareFactory;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Wishlist_Model_WishlistFactory $wishListFactory
     * @param Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemsCompareFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Wishlist_Model_WishlistFactory $wishListFactory,
        Magento_Catalog_Model_Resource_Product_Compare_Item_CollectionFactory $itemsCompareFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_quoteFactory = $quoteFactory;
        $this->_wishListFactory = $wishListFactory;
        $this->_itemsCompareFactory = $itemsCompareFactory;
        parent::__construct($coreData, $context, $data);
    }


    public function getShoppingCartUrl()
    {
        return $this->_urlBuilder->getUrl('checkout/cart');
    }

    public function getCartItemsCount()
    {
        if( !$this->_cartItemsCount ) {
            $this->_cartItemsCount = $this->_createQuote()
                ->setId($this->_checkoutSession->getQuote()->getId())
                ->getItemsCollection()
                ->getSize();
        }

        return $this->_cartItemsCount;
    }

    public function getWishlist()
    {
        if( !$this->_wishlist ) {
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
        if( !$this->_compareItems ) {
            $this->_compareItems =
                $this->_createProductCompareCollection()->setStoreId($this->_storeManager->getStore()->getId());
            $this->_compareItems->setCustomerId(
                $this->_customerSession->getCustomerId()
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
        return $this->getUrl('catalog/product_compare/remove',array('product'=>'#{id}'));
    }

    public function getCompareAddUrlTemplate()
    {
        return $this->getUrl('catalog/product_compare/add',array('product'=>'#{id}'));
    }

    public function getCompareUrl()
    {
        return $this->getUrl('catalog/product_compare');
    }

    /**
     * @return Magento_Sales_Model_Quote
     */
    protected function _createQuote()
    {
        return $this->_quoteFactory->create();
    }

    /**
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _createWishList()
    {
        return $this->_wishListFactory->create();
    }

    /**
     * @return Magento_Catalog_Model_Resource_Product_Compare_Item_Collection
     */
    protected function _createProductCompareCollection()
    {
        return $this->_itemsCompareFactory->create();
    }
}
