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
 * Wishlist Data Helper
 *
 * @category   Magento
 * @package    Magento_Wishlist
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * Config key 'Display Wishlist Summary'
     */
    const XML_PATH_WISHLIST_LINK_USE_QTY = 'wishlist/wishlist_link/use_qty';

    /**
     * Config key 'Display Out of Stock Products'
     */
    const XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK = 'cataloginventory/options/show_out_of_stock';

    /**
     * Currently logged in customer
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_currentCustomer;

    /**
     * Customer Wishlist instance
     *
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Wishlist Product Items Collection
     *
     * @var Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected $_productCollection;

    /**
     * Wishlist Items Collection
     *
     * @var Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected $_wishlistItemCollection;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry;

    /**
     * Core store config
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_coreStoreConfig;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Customer_Model_SessionProxy $customerSession
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Customer_Model_SessionProxy $customerSession,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_coreStoreConfig = $coreStoreConfig;
        $this->_customerSession = $customerSession;
        $this->_wishlistFactory = $wishlistFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Retrieve customer login status
     *
     * @return bool
     */
    protected function _isCustomerLogIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Retrieve logged in customer
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCurrentCustomer()
    {
        return $this->getCustomer();
    }

    /**
     * Set current customer
     *
     * @param Magento_Customer_Model_Customer $customer
     */
    public function setCustomer(Magento_Customer_Model_Customer $customer)
    {
        $this->_currentCustomer = $customer;
    }

    /**
     * Retrieve current customer
     *
     * @return Magento_Customer_Model_Customer|null
     */
    public function getCustomer()
    {
        if (!$this->_currentCustomer && $this->_customerSession->isLoggedIn()) {
            $this->_currentCustomer = $this->_customerSession->getCustomer();
        }
        return $this->_currentCustomer;
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    public function getWishlist()
    {
        if (is_null($this->_wishlist)) {
            if ($this->_coreRegistry->registry('shared_wishlist')) {
                $this->_wishlist = $this->_coreRegistry->registry('shared_wishlist');
            } elseif ($this->_coreRegistry->registry('wishlist')) {
                $this->_wishlist = $this->_coreRegistry->registry('wishlist');
            } else {
                $this->_wishlist = $this->_wishlistFactory->create();
                if ($this->getCustomer()) {
                    $this->_wishlist->loadByCustomer($this->getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve wishlist item count (include config settings)
     * Used in top link menu only
     *
     * @return int
     */
    public function getItemCount()
    {
        $storedDisplayType = $this->_customerSession->getWishlistDisplayType();
        $currentDisplayType = $this->_coreStoreConfig->getConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY);

        $storedDisplayOutOfStockProducts = $this->_customerSession->getDisplayOutOfStockProducts();
        $currentDisplayOutOfStockProducts = $this->_coreStoreConfig->getConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK);
        if (!$this->_customerSession->hasWishlistItemCount()
                || ($currentDisplayType != $storedDisplayType)
                || $this->_customerSession->hasDisplayOutOfStockProducts()
                || ($currentDisplayOutOfStockProducts != $storedDisplayOutOfStockProducts)) {
            $this->calculate();
        }

        return $this->_customerSession->getWishlistItemCount();
    }

    /**
     * Create wishlist item collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createWishlistItemCollection()
    {
        return $this->getWishlist()->getItemCollection();
    }

    /**
     * Retrieve wishlist items collection
     *
     * @return Magento_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItemCollection()
    {
        if (is_null($this->_wishlistItemCollection)) {
            $this->_wishlistItemCollection = $this->_createWishlistItemCollection();
        }
        return $this->_wishlistItemCollection;
    }

    /**
     * Retrieve Item Store for URL
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return Magento_Core_Model_Store
     */
    protected function _getUrlStore($item)
    {
        $storeId = null;
        $product = null;
        if ($item instanceof Magento_Wishlist_Model_Item) {
            $product = $item->getProduct();
        } elseif ($item instanceof Magento_Catalog_Model_Product) {
            $product = $item;
        }
        if ($product) {
            if ($product->isVisibleInSiteVisibility()) {
                $storeId = $product->getStoreId();
            } else if ($product->hasUrlDataObject()) {
                $storeId = $product->getUrlDataObject()->getStoreId();
            }
        }
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getUrl('wishlist/index/remove',
            array('item' => $item->getWishlistItemId())
        );
    }

    /**
     * Retrieve URL for removing item from wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return string
     */
    public function getConfigureUrl($item)
    {
        return $this->_getUrl('wishlist/index/configure', array(
            'item' => $item->getWishlistItemId()
        ));
    }

    /**
     * Retrieve url for adding product to wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     *
     * @return  string|bool
     */
    public function getAddUrl($item)
    {
        return $this->getAddUrlWithParams($item);
    }

    /**
     * Retrieve url for adding product to wishlist
     *
     * @param int $itemId
     *
     * @return  string
     */
    public function getMoveFromCartUrl($itemId)
    {
        return $this->_getUrl('wishlist/index/fromcart', array('item' => $itemId));
    }

    /**
     * Retrieve url for updating product in wishlist
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     *
     * @return  string|bool
     */
    public function getUpdateUrl($item)
    {
        $itemId = null;
        if ($item instanceof Magento_Catalog_Model_Product) {
            $itemId = $item->getWishlistItemId();
        }
        if ($item instanceof Magento_Wishlist_Model_Item) {
            $itemId = $item->getId();
        }

        if ($itemId) {
            return $this->_getUrl('wishlist/index/updateItemOptions', array('id' => $itemId));
        }

        return false;
    }

    /**
     * Retrieve url for adding product to wishlist with params
     *
     * @param Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @param array $params
     *
     * @return  string|bool
     */
    public function getAddUrlWithParams($item, array $params = array())
    {
        $productId = null;
        if ($item instanceof Magento_Catalog_Model_Product) {
            $productId = $item->getEntityId();
        }
        if ($item instanceof Magento_Wishlist_Model_Item) {
            $productId = $item->getProductId();
        }

        if ($productId) {
            $params['product'] = $productId;
            return $this->_getUrlStore($item)->getUrl('wishlist/index/add', $params);
        }

        return false;
    }

    /**
     * Retrieve URL for adding item to shoping cart
     *
     * @param string|Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return  string
     */
    public function getAddToCartUrl($item)
    {
        $continueUrl  = $this->_coreData->urlEncode(
            $this->_getUrl('*/*/*', array(
                '_current'      => true,
                '_use_rewrite'  => true,
                '_store_to_url' => true,
            ))
        );

        $urlParamName = Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $params = array(
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
            $urlParamName => $continueUrl
        );
        return $this->_getUrlStore($item)->getUrl('wishlist/index/cart', $params);
    }

    /**
     * Retrieve URL for adding item to shoping cart from shared wishlist
     *
     * @param string|Magento_Catalog_Model_Product|Magento_Wishlist_Model_Item $item
     * @return  string
     */
    public function getSharedAddToCartUrl($item)
    {
        $continueUrl  = $this->_coreData->urlEncode($this->_getUrl('*/*/*', array(
            '_current'      => true,
            '_use_rewrite'  => true,
            '_store_to_url' => true,
        )));

        $urlParamName = Magento_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $params = array(
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
            $urlParamName => $continueUrl
        );
        return $this->_getUrlStore($item)->getUrl('wishlist/shared/cart', $params);
    }

    /**
     * Retrieve customer wishlist url
     *
     * @param int $wishlistId
     * @return string
     */
    public function getListUrl($wishlistId = null)
    {
        $params = array();
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }
        return $this->_getUrl('wishlist', $params);
    }

    /**
     * Check is allow wishlist module
     *
     * @return bool
     */
    public function isAllow()
    {
        if ($this->isModuleOutputEnabled() && $this->_coreStoreConfig->getConfig('wishlist/general/active')) {
            return true;
        }
        return false;
    }

    /**
     * Check is allow wishlist action in shopping cart
     *
     * @return bool
     */
    public function isAllowInCart()
    {
        return $this->isAllow() && $this->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            return $customer->getName();
        }
    }

    /**
     * Retrieve RSS URL
     *
     * @param $wishlistId
     * @return string
     */
    public function getRssUrl($wishlistId = null)
    {
        $customer = $this->_getCurrentCustomer();
        if ($customer) {
            $key = $customer->getId() . ',' . $customer->getEmail();
            $params = array(
                'data' => $this->_coreData->urlEncode($key),
                '_secure' => false,
            );
        }
        if ($wishlistId) {
            $params['wishlist_id'] = $wishlistId;
        }
        return $this->_getUrl(
            'rss/index/wishlist',
            $params
        );
    }

    /**
     * Is allow RSS
     *
     * @return bool
     */
    public function isRssAllow()
    {
        return $this->_coreStoreConfig->getConfigFlag('rss/wishlist/active');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return string
     */
    public function defaultCommentString()
    {
        return __('Please enter your comments.');
    }

    /**
     * Retrieve default empty comment message
     *
     * @return string
     */
    public function getDefaultWishlistName()
    {
        return __('Wish List');
    }

    /**
     * Calculate count of wishlist items and put value to customer session.
     * Method called after wishlist modifications and trigger 'wishlist_items_renewed' event.
     * Depends from configuration.
     *
     * @return Magento_Wishlist_Helper_Data
     */
    public function calculate()
    {
        $count = 0;
        if ($this->getCustomer()) {
            $collection = $this->getWishlistItemCollection()->setInStockFilter(true);
            if ($this->_coreStoreConfig->getConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY)) {
                $count = $collection->getItemsQty();
            } else {
                $count = $collection->getSize();
            }
            $this->_customerSession
                ->setWishlistDisplayType($this->_coreStoreConfig->getConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY));
            $this->_customerSession->setDisplayOutOfStockProducts(
                $this->_coreStoreConfig->getConfig(self::XML_PATH_CATALOGINVENTORY_SHOW_OUT_OF_STOCK)
            );
        }
        $this->_customerSession->setWishlistItemCount($count);
        $this->_eventManager->dispatch('wishlist_items_renewed');
        return $this;
    }

    /**
     * Should display item quantities in my wishlist link
     *
     * @return bool
     */
    public function isDisplayQty()
    {
        return $this->_coreStoreConfig->getConfig(self::XML_PATH_WISHLIST_LINK_USE_QTY);
    }
}
