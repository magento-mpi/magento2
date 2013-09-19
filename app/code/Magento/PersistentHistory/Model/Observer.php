<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_PersistentHistory_Model_Observer
{
    /**
     * Whether set quote to be persistent in workflow
     *
     * @var bool
     */
    protected $_setQuotePersistent = true;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;
    
    /**
     * Persistent data
     *
     * @var Magento_Persistent_Helper_Data
     */
    protected $_mPersistentData = null;

    /**
     * Persistent data
     *
     * @var Magento_PersistentHistory_Helper_Data
     */
    protected $_ePersistentData = null;

    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * Persistent session
     *
     * @var Magento_Persistent_Helper_Session
     */
    protected $_persistentSession = null;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_Core_Model_Layout
     */
    protected $_layout;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Persistent_Model_Observer
     */
    protected $_observer;

    /**
     * @var Magento_Catalog_Model_Product_Compare_Item
     */
    protected $_compareItem;

    /**
     * @var Magento_Persistent_Model_Persistent_ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var Magento_Core_Model_Config_ValueFactory
     */
    protected $_valueFactory;

    /**
     * @var Magento_Reports_Model_Product_Index_ComparedFactory
     */
    protected $_comparedFactory;

    /**
     * @var Magento_Reports_Model_Product_Index_ViewedFactory
     */
    protected $_viewedFactory;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishListFactory;


    /**
     * @param Magento_Persistent_Helper_Session $persistentSession
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_PersistentHistory_Helper_Data $ePersistentData
     * @param Magento_Persistent_Helper_Data $mPersistentData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_Layout $layout
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Persistent_Model_Observer $observer
     * @param Magento_Catalog_Model_Product_Compare_Item $compareItem
     * @param Magento_Persistent_Model_Persistent_ConfigFactory $configFactory
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param Magento_Core_Model_Config_ValueFactory $valueFactory
     * @param Magento_Reports_Model_Product_Index_ComparedFactory $comparedFactory
     * @param Magento_Reports_Model_Product_Index_ViewedFactory $viewedFactory
     */
    public function __construct(
        Magento_Persistent_Helper_Session $persistentSession,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_PersistentHistory_Helper_Data $ePersistentData,
        Magento_Persistent_Helper_Data $mPersistentData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_Layout $layout,
        Magento_Customer_Model_Session $customerSession,
        Magento_Persistent_Model_Observer $observer,
        Magento_Catalog_Model_Product_Compare_Item $compareItem,
        Magento_Persistent_Model_Persistent_ConfigFactory $configFactory,
        Magento_Core_Model_UrlFactory $urlFactory,
        Magento_Core_Model_Config_ValueFactory $valueFactory,
        Magento_Reports_Model_Product_Index_ComparedFactory $comparedFactory,
        Magento_Reports_Model_Product_Index_ViewedFactory $viewedFactory,
        Magento_Wishlist_Model_WishlistFactory $wishListFactory
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_wishlistData = $wishlistData;
        $this->_mPersistentData = $mPersistentData;
        $this->_ePersistentData = $ePersistentData;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        $this->_layout = $layout;
        $this->_customerSession = $customerSession;
        $this->_observer = $observer;
        $this->_configFactory = $configFactory;
        $this->_urlFactory = $urlFactory;
        $this->_valueFactory = $valueFactory;
        $this->_compareItem = $compareItem;
        $this->_comparedFactory = $comparedFactory;
        $this->_viewedFactory = $viewedFactory;
        $this->_wishListFactory = $wishListFactory;
    }

    /**
     * Set persistent data to customer session
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PersistentHistory_Model_Observer
     */
    public function emulateCustomer($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer)
            || !$this->_ePersistentData->isCustomerAndSegmentsPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer Magento_Customer_Model_Customer */
            $customer = $this->_customerFactory->create()->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            $this->_customerSession->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());

            // apply persistent data to segments
            $this->_coreRegistry->register('segment_customer', $customer, true);
            if ($this->_isWishlistPersist()) {
                $this->_wishlistData->setCustomer($customer);
            }
        }
        return $this;
    }

    /**
     * Modify expired quotes cleanup
     *
     * @param Magento_Event_Observer $observer
     */
    public function modifyExpiredQuotesCleanup($observer)
    {
        /** @var $salesObserver Magento_Sales_Model_Observer */
        $salesObserver = $observer->getEvent()->getSalesObserver();
        $salesObserver->setExpireQuotesAdditionalFilterFields(array(
            'is_persistent' => 0
        ));
    }

    /**
     * Apply persistent data
     *
     * @param Magento_Event_Observer $observer
     * @return null
     */
    public function applyPersistentData($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer)
            || !$this->_isPersistent() || $this->_customerSession->isLoggedIn()
        ) {
            return;
        }
        $this->_configFactory->create()
            ->setConfigFilePath($this->_ePersistentData->getPersistentConfigFilePath())
            ->fire();
    }

    public function applyBlockPersistentData($observer)
    {
        $observer->getEvent()->setConfigFilePath(
            $this->_ePersistentData->getPersistentConfigFilePath()
        );
        return $this->_observer->applyBlockPersistentData($observer);
    }

    /**
     * Set whislist items count in top wishlist link block
     *
     * @deprecated after 1.11.2.0
     * @param Magento_Core_Block_Abstract $block
     * @return null
     */
    public function initWishlist($block)
    {
        if (!$this->_isWishlistPersist()) {
            return;
        }
        $block->setCustomWishlist($this->_initWishlist());
    }

    /**
     * Set persistent wishlist to wishlist sidebar block
     *
     * @deprecated after 1.11.2.0
     * @param Magento_Core_Block_Abstract $block
     * @return null
     */
    public function initWishlistSidebar($block)
    {
        if (!$this->_isWishlistPersist()) {
            return;
        }
        $block->setCustomWishlist($this->_initWishlist());
    }

    /**
     * Set persistent orders to recently orders block
     *
     * @param Magento_Core_Block_Abstract $block
     * @return null
     */
    public function initReorderSidebar($block)
    {
        if (!$this->_ePersistentData->isOrderedItemsPersist()) {
            return;
        }
        $block->setCustomerId($this->_getCustomerId());
        $block->initOrders();
    }

    /**
     * Emulate 'viewed products' block with persistent data
     *
     * @param Magento_Reports_Block_Product_Viewed $block
     * @return null
     */
    public function emulateViewedProductsBlock(Magento_Reports_Block_Product_Viewed $block)
    {
        if (!$this->_ePersistentData->isViewedProductsPersist()) {
            return;
        }
        $customerId = $this->_getCustomerId();
        $block->getModel()
            ->setCustomerId($customerId)
            ->calculate();
        $block->setCustomerId($customerId);
    }

    /**
     * Remove cart link
     *
     * @param Magento_Event_Observer $observer
     */
    public function removeCartLink($observer)
    {
        $block =  $this->_layout->getBlock('checkout.links');
        if ($block) {
            $block->removeLinkByUrl($this->_urlFactory->create()->getUrl('checkout/cart'));
        }
    }

    /**
     * Emulate 'compared products' block with persistent data
     *
     * @param Magento_Reports_Block_Product_Compared $block
     * @return null
     */
    public function emulateComparedProductsBlock(Magento_Reports_Block_Product_Compared $block)
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $customerId = $this->_getCustomerId();
        $block->setCustomerId($customerId);
        $block->getModel()
            ->setCustomerId($customerId)
            ->calculate();
    }

    /**
     * Emulate 'compare products' block with persistent data
     *
     * @param Magento_Catalog_Block_Product_Compare_Sidebar $block
     * @return null
     */
    public function emulateCompareProductsBlock(Magento_Catalog_Block_Product_Compare_Sidebar $block)
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $collection = $block->getCompareProductHelper()
            ->setCustomerId($this->_getCustomerId())
            ->getItemCollection();
        $block->setItems($collection);
    }

    /**
     * Emulate 'compare products list' block with persistent data
     *
     * @param Magento_Catalog_Block_Product_Compare_List $block
     * @return null
     */
    public function emulateCompareProductsListBlock(Magento_Catalog_Block_Product_Compare_List $block)
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $block->setCustomerId($this->_getCustomerId());
    }

    /**
     * Apply persistent customer id
     *
     * @param Magento_Event_Observer $observer
     * @return null
     */
    public function applyCustomerId($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_isCompareProductsPersist()) {
            return;
        }
        $instance = $observer->getEvent()->getControllerAction();
        $instance->setCustomerId($this->_getCustomerId());
    }

    /**
     * Emulate customer wishlist (add, delete, etc)
     *
     * @param Magento_Event_Observer $observer
     * @return null
     */
    public function emulateWishlist($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer)
            || !$this->_isPersistent() || !$this->_isWishlistPersist()
        ) {
            return;
        }

        $controller = $observer->getEvent()->getControllerAction();
        if ($controller instanceof Magento_Wishlist_Controller_Index) {
            $controller->skipAuthentication();
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param Magento_Event_Observer $observer
     * @return null
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession Magento_Customer_Model_Session */
        $customerSession = $this->_customerSession;

        $helper = $this->_ePersistentData;
        if ($helper->isCustomerAndSegmentsPersist() && $this->_setQuotePersistent) {
            $customerId = $customerSession->getCustomerId();
            if ($customerId) {
                $quote->setCustomerId($customerId);
            }
            $customerGroupId = $customerSession->getCustomerGroupId();
            if ($customerGroupId) {
                $quote->setCustomerGroupId($customerGroupId);
            }
        }
    }

    /**
     * Prevent setting persistent data into quote
     *
     * @param  $observer
     * @see Magento_PersistentHistory_Model_Observer::setQuotePersistentData
     */
    public function preventSettingQuotePersistent($observer)
    {
        $this->_setQuotePersistent = false;
    }

    /**
     * Update Option "Persist Customer Group Membership and Segmentation"
     * set value "Yes" if option "Persist Shopping Cart" equals "Yes"
     *
     * @param  $observer Magento_PersistentHistory_Model_Observer
     * @return void
     */
    public function updateOptionCustomerSegmentation($observer)
    {
        $eventDataObject = $observer->getEvent()->getDataObject();

        if ($eventDataObject->getValue()) {
            $optionCustomerSegm = $this->_valueFactory->create()
                ->setScope($eventDataObject->getScope())
                ->setScopeId($eventDataObject->getScopeId())
                ->setPath(Magento_PersistentHistory_Helper_Data::XML_PATH_PERSIST_CUSTOMER_AND_SEGM)
                ->setValue(true)
                ->save();
        }
    }

    /**
     * Expire data of Sidebars
     *
     * @param Magento_Event_Observer $observer
     */
    public function expireSidebars($observer)
    {
        $this->_expireCompareProducts();
        $this->_expireComparedProducts();
        $this->_expireViewedProducts();
    }

    /**
     * Expire data of Compare products sidebar
     *
     */
    public function _expireCompareProducts()
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $this->_compareItem->bindCustomerLogout();
    }

    /**
     * Expire data of Compared products sidebar
     *
     */
    public function _expireComparedProducts()
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $this->_comparedFactory->create()
            ->purgeVisitorByCustomer()
            ->calculate();
    }

    /**
     * Expire data of Viewed products sidebar
     *
     */
    public function _expireViewedProducts()
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $this->_viewedFactory->create()
            ->purgeVisitorByCustomer()
            ->calculate();
    }

    /**
     * Return persistent customer id
     *
     * @return int
     */
    protected function _getCustomerId()
    {
        return $this->_getPersistentHelper()->getSession()->getCustomerId();
    }

    /**
     * Retrieve persistent helper
     *
     * @return Magento_Persistent_Helper_Session
     */
    protected function _getPersistentHelper()
    {
        return $this->_persistentSession;
    }

    /**
     * Init persistent wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _initWishlist()
    {
        return $this->_wishListFactory->create()->loadByCustomer($this->_getCustomerId() ,true);
    }

    /**
     * Check whether wishlist is persist
     *
     * @return bool
     */
    protected function _isWishlistPersist()
    {
        return $this->_ePersistentData->isWishlistPersist();
    }

    /**
     * Check whether compare products is persist
     *
     * @return bool
     */
    protected function _isCompareProductsPersist()
    {
        return $this->_ePersistentData->isCompareProductsPersist();
    }

    /**
     * Check whether compared products is persist
     *
     * @return bool
     */
    protected function _isComparedProductsPersist()
    {
        return $this->_ePersistentData->isComparedProductsPersist();
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    protected function _isPersistent()
    {
        return $this->_getPersistentHelper()->isPersistent();
    }

    /**
     * Check if persistent mode is running and customer is logged out
     *
     * @return bool
     */
    protected function _isLoggedOut()
    {
        return $this->_isPersistent() && !$this->_customerSession->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !$this->_mPersistentData->isShoppingCartPersist();
    }

    /**
     * Skip website restriction and allow access for persistent customers
     *
     * @param Magento_Event_Observer $observer
     */
    public function skipWebsiteRestriction(Magento_Event_Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
