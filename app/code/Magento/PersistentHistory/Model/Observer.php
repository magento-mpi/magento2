<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Model;

use Magento\Framework\Event\Observer as EventObserver;

class Observer
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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Persistent data
     *
     * @var \Magento\Persistent\Helper\Data
     */
    protected $_mPersistentData = null;

    /**
     * Persistent data
     *
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $_ePersistentData = null;

    /**
     * Wishlist data
     *
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $_wishlistData = null;

    /**
     * Persistent session
     *
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSession = null;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Persistent\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\Catalog\Model\Product\Compare\Item
     */
    protected $_compareItem;

    /**
     * @var \Magento\Persistent\Model\Persistent\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Framework\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_valueFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ComparedFactory
     */
    protected $_comparedFactory;

    /**
     * @var \Magento\Reports\Model\Product\Index\ViewedFactory
     */
    protected $_viewedFactory;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $_wishListFactory;

    /**
     * @var \Magento\Customer\Service\V1\CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Persistent\Helper\Session $persistentSession
     * @param \Magento\Wishlist\Helper\Data $wishlistData
     * @param \Magento\PersistentHistory\Helper\Data $ePersistentData
     * @param \Magento\Persistent\Helper\Data $mPersistentData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Persistent\Model\Observer $observer
     * @param \Magento\Catalog\Model\Product\Compare\Item $compareItem
     * @param \Magento\Persistent\Model\Persistent\ConfigFactory $configFactory
     * @param \Magento\Framework\UrlFactory $urlFactory
     * @param \Magento\Framework\App\Config\ValueFactory $valueFactory
     * @param \Magento\Reports\Model\Product\Index\ComparedFactory $comparedFactory
     * @param \Magento\Reports\Model\Product\Index\ViewedFactory $viewedFactory
     * @param \Magento\Wishlist\Model\WishlistFactory $wishListFactory
     * @param \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Persistent\Helper\Session $persistentSession,
        \Magento\Wishlist\Helper\Data $wishlistData,
        \Magento\PersistentHistory\Helper\Data $ePersistentData,
        \Magento\Persistent\Helper\Data $mPersistentData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Persistent\Model\Observer $observer,
        \Magento\Catalog\Model\Product\Compare\Item $compareItem,
        \Magento\Persistent\Model\Persistent\ConfigFactory $configFactory,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Config\ValueFactory $valueFactory,
        \Magento\Reports\Model\Product\Index\ComparedFactory $comparedFactory,
        \Magento\Reports\Model\Product\Index\ViewedFactory $viewedFactory,
        \Magento\Wishlist\Model\WishlistFactory $wishListFactory,
        \Magento\Customer\Service\V1\CustomerAccountServiceInterface $customerAccountService
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
        $this->_customerAccountService = $customerAccountService;
    }

    /**
     * Set persistent data to customer session
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function emulateCustomer($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_ePersistentData->isCustomerAndSegmentsPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** TODO DataObject should be initialized instead of CustomerModel after refactoring of segment_customer */
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $this->_customerFactory->create()->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            $this->_customerSession->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId())
                ->setIsCustomerEmulated(true);

            // apply persistent data to segments
            $this->_coreRegistry->register('segment_customer', $customer, true);
            if ($this->_isWishlistPersist()) {
                /** @var \Magento\Customer\Service\V1\Data\Customer $customerDataObject */
                $customerDataObject = $this->_customerAccountService->getCustomer(
                    $this->_getPersistentHelper()->getSession()->getCustomerId()
                );
                $this->_wishlistData->setCustomer($customerDataObject);
            }
        }
        return $this;
    }

    /**
     * Apply persistent data
     *
     * @param EventObserver $observer
     * @return void
     */
    public function applyPersistentData($observer)
    {
        if (!$this->_mPersistentData->canProcess(
            $observer
        ) || !$this->_isPersistent() || $this->_customerSession->isLoggedIn()
        ) {
            return;
        }
        $this->_configFactory->create()->setConfigFilePath(
            $this->_ePersistentData->getPersistentConfigFilePath()
        )->fire();
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function applyBlockPersistentData($observer)
    {
        $observer->getEvent()->setConfigFilePath($this->_ePersistentData->getPersistentConfigFilePath());
        return $this->_observer->applyBlockPersistentData($observer);
    }

    /**
     * Set whislist items count in top wishlist link block
     *
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return void
     * @deprecated after 1.11.2.0
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
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return void
     * @deprecated after 1.11.2.0
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
     * @param \Magento\Framework\View\Element\AbstractBlock $block
     * @return void
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
     * @param \Magento\Reports\Block\Product\Viewed $block
     * @return void
     */
    public function emulateViewedProductsBlock(\Magento\Reports\Block\Product\Viewed $block)
    {
        if (!$this->_ePersistentData->isViewedProductsPersist()) {
            return;
        }
        $customerId = $this->_getCustomerId();
        $block->getModel()->setCustomerId($customerId)->calculate();
        $block->setCustomerId($customerId);
    }

    /**
     * Emulate 'compared products' block with persistent data
     *
     * @param \Magento\Reports\Block\Product\Compared $block
     * @return void
     */
    public function emulateComparedProductsBlock(\Magento\Reports\Block\Product\Compared $block)
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $customerId = $this->_getCustomerId();
        $block->setCustomerId($customerId);
        $block->getModel()->setCustomerId($customerId)->calculate();
    }

    /**
     * Emulate 'compare products' block with persistent data
     *
     * @param \Magento\Catalog\Block\Product\Compare\Sidebar $block
     * @return void
     */
    public function emulateCompareProductsBlock(\Magento\Catalog\Block\Product\Compare\Sidebar $block)
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $collection = $block->getCompareProductHelper()->setCustomerId($this->_getCustomerId())->getItemCollection();
        $block->setItems($collection);
    }

    /**
     * Emulate 'compare products list' block with persistent data
     *
     * @param \Magento\Catalog\Block\Product\Compare\ListCompare $block
     * @return void
     */
    public function emulateCompareProductsListBlock(\Magento\Catalog\Block\Product\Compare\ListCompare $block)
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $block->setCustomerId($this->_getCustomerId());
    }

    /**
     * Apply persistent customer id
     *
     * @param EventObserver $observer
     * @return void
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
     * Set persistent data into quote
     *
     * @param EventObserver $observer
     * @return void
     */
    public function setQuotePersistentData($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession \Magento\Customer\Model\Session */
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
     * @param EventObserver $observer
     * @return void
     * 
     * @see Observer::setQuotePersistentData
     */
    public function preventSettingQuotePersistent($observer)
    {
        $this->_setQuotePersistent = false;
    }

    /**
     * Update Option "Persist Customer Group Membership and Segmentation"
     * set value "Yes" if option "Persist Shopping Cart" equals "Yes"
     *
     * @param Observer $observer
     * @return void
     */
    public function updateOptionCustomerSegmentation($observer)
    {
        $eventDataObject = $observer->getEvent()->getDataObject();

        if ($eventDataObject->getValue()) {
            $optionCustomerSegm = $this->_valueFactory->create()->setScope(
                $eventDataObject->getScope()
            )->setScopeId(
                $eventDataObject->getScopeId()
            )->setPath(
                \Magento\PersistentHistory\Helper\Data::XML_PATH_PERSIST_CUSTOMER_AND_SEGM
            )->setValue(
                true
            )->save();
        }
    }

    /**
     * Expire data of Sidebars
     *
     * @param EventObserver $observer
     * @return void
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
     * @return void
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
     * @return void
     */
    public function _expireComparedProducts()
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $this->_comparedFactory->create()->purgeVisitorByCustomer()->calculate();
    }

    /**
     * Expire data of Viewed products sidebar
     *
     * @return void
     */
    public function _expireViewedProducts()
    {
        if (!$this->_isComparedProductsPersist()) {
            return;
        }
        $this->_viewedFactory->create()->purgeVisitorByCustomer()->calculate();
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
     * @return \Magento\Persistent\Helper\Session
     */
    protected function _getPersistentHelper()
    {
        return $this->_persistentSession;
    }

    /**
     * Init persistent wishlist
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    protected function _initWishlist()
    {
        return $this->_wishListFactory->create()->loadByCustomerId($this->_getCustomerId(), true);
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
     * @param EventObserver $observer
     * @return void
     */
    public function skipWebsiteRestriction(EventObserver $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
