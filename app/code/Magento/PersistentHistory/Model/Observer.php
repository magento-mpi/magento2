<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PersistentHistory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PersistentHistory\Model;

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
     * @param Magento_Persistent_Helper_Session $persistentSession
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_PersistentHistory_Helper_Data $ePersistentData
     * @param Magento_Persistent_Helper_Data $mPersistentData
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Persistent_Helper_Session $persistentSession,
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_PersistentHistory_Helper_Data $ePersistentData,
        Magento_Persistent_Helper_Data $mPersistentData,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_persistentSession = $persistentSession;
        $this->_wishlistData = $wishlistData;
        $this->_mPersistentData = $mPersistentData;
        $this->_ePersistentData = $ePersistentData;
        $this->_coreRegistry = $coreRegistry;

    }

    /**
     * Set persistent data to customer session
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\PersistentHistory\Model\Observer
     */
    public function emulateCustomer($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer)
            || !$this->_ePersistentData->isCustomerAndSegmentsPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer \Magento\Customer\Model\Customer */
            $customer = \Mage::getModel('Magento\Customer\Model\Customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            \Mage::getSingleton('Magento\Customer\Model\Session')
                ->setCustomerId($customer->getId())
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
     * @param \Magento\Event\Observer $observer
     */
    public function modifyExpiredQuotesCleanup($observer)
    {
        /** @var $salesObserver \Magento\Sales\Model\Observer */
        $salesObserver = $observer->getEvent()->getSalesObserver();
        $salesObserver->setExpireQuotesAdditionalFilterFields(array(
            'is_persistent' => 0
        ));
    }

    /**
     * Apply persistent data
     *
     * @param \Magento\Event\Observer $observer
     * @return null
     */
    public function applyPersistentData($observer)
    {
        if (!$this->_mPersistentData->canProcess($observer)
            || !$this->_isPersistent() || \Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn()
        ) {
            return;
        }
        \Mage::getModel('Magento\Persistent\Model\Persistent\Config')
            ->setConfigFilePath($this->_ePersistentData->getPersistentConfigFilePath())
            ->fire();
    }

    public function applyBlockPersistentData($observer)
    {
        $observer->getEvent()->setConfigFilePath(
            $this->_ePersistentData->getPersistentConfigFilePath()
        );
        return \Mage::getSingleton('Magento\Persistent\Model\Observer')->applyBlockPersistentData($observer);
    }

    /**
     * Set whislist items count in top wishlist link block
     *
     * @deprecated after 1.11.2.0
     * @param \Magento\Core\Block\AbstractBlock $block
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
     * @param \Magento\Core\Block\AbstractBlock $block
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
     * @param \Magento\Core\Block\AbstractBlock $block
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
     * @param \Magento\Reports\Block\Product\Viewed $block
     * @return null
     */
    public function emulateViewedProductsBlock(\Magento\Reports\Block\Product\Viewed $block)
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
     * @param \Magento\Event\Observer $observer
     */
    public function removeCartLink($observer)
    {
        $block =  \Mage::app()->getLayout()->getBlock('checkout.links');
        if ($block) {
            $block->removeLinkByUrl(\Mage::getUrl('checkout/cart'));
        }
    }

    /**
     * Emulate 'compared products' block with persistent data
     *
     * @param \Magento\Reports\Block\Product\Compared $block
     * @return null
     */
    public function emulateComparedProductsBlock(\Magento\Reports\Block\Product\Compared $block)
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
     * @param \Magento\Catalog\Block\Product\Compare\Sidebar $block
     * @return null
     */
    public function emulateCompareProductsBlock(\Magento\Catalog\Block\Product\Compare\Sidebar $block)
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
     * @param \Magento\Catalog\Block\Product\Compare\ListCompare $block
     * @return null
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
     * @param \Magento\Event\Observer $observer
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
     * @param \Magento\Event\Observer $observer
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
        if ($controller instanceof \Magento\Wishlist\Controller\Index) {
            $controller->skipAuthentication();
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param \Magento\Event\Observer $observer
     * @return null
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
        $customerSession = \Mage::getSingleton('Magento\Customer\Model\Session');

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
     * @see \Magento\PersistentHistory\Model\Observer::setQuotePersistentData
     */
    public function preventSettingQuotePersistent($observer)
    {
        $this->_setQuotePersistent = false;
    }

    /**
     * Update Option "Persist Customer Group Membership and Segmentation"
     * set value "Yes" if option "Persist Shopping Cart" equals "Yes"
     *
     * @param  $observer \Magento\PersistentHistory\Model\Observer
     * @return void
     */
    public function updateOptionCustomerSegmentation($observer)
    {
        $eventDataObject = $observer->getEvent()->getDataObject();

        if ($eventDataObject->getValue()) {
            $optionCustomerSegm = \Mage::getModel('Magento\Core\Model\Config\Value')
                ->setScope($eventDataObject->getScope())
                ->setScopeId($eventDataObject->getScopeId())
                ->setPath(\Magento\PersistentHistory\Helper\Data::XML_PATH_PERSIST_CUSTOMER_AND_SEGM)
                ->setValue(true)
                ->save();
        }
    }

    /**
     * Expire data of Sidebars
     *
     * @param \Magento\Event\Observer $observer
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
        \Mage::getSingleton('Magento\Catalog\Model\Product\Compare\Item')->bindCustomerLogout();
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
        \Mage::getModel('Magento\Reports\Model\Product\Index\Compared')
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
        \Mage::getModel('Magento\Reports\Model\Product\Index\Viewed')
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
        return \Mage::getModel('Magento\Wishlist\Model\Wishlist')->loadByCustomer($this->_getCustomerId() ,true);
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
        return $this->_isPersistent() && !\Mage::getSingleton('Magento\Customer\Model\Session')->isLoggedIn();
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
     * @param \Magento\Event\Observer $observer
     */
    public function skipWebsiteRestriction(\Magento\Event\Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
