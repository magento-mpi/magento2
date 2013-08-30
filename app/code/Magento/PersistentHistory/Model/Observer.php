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
     * Set persistent data to customer session
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_PersistentHistory_Model_Observer
     */
    public function emulateCustomer($observer)
    {
        if (!Mage::helper('Magento_Persistent_Helper_Data')->canProcess($observer)
            || !Mage::helper('Magento_PersistentHistory_Helper_Data')->isCustomerAndSegmentsPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer Magento_Customer_Model_Customer */
            $customer = Mage::getModel('Magento_Customer_Model_Customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            Mage::getSingleton('Magento_Customer_Model_Session')
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());

            // apply persistent data to segments
            Mage::register('segment_customer', $customer, true);
            if ($this->_isWishlistPersist()) {
                Mage::helper('Magento_Wishlist_Helper_Data')->setCustomer($customer);
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
        if (!Mage::helper('Magento_Persistent_Helper_Data')->canProcess($observer)
            || !$this->_isPersistent() || Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()
        ) {
            return;
        }
        Mage::getModel('Magento_Persistent_Model_Persistent_Config')
            ->setConfigFilePath(Mage::helper('Magento_PersistentHistory_Helper_Data')->getPersistentConfigFilePath())
            ->fire();
    }

    public function applyBlockPersistentData($observer)
    {
        $observer->getEvent()->setConfigFilePath(
            Mage::helper('Magento_PersistentHistory_Helper_Data')->getPersistentConfigFilePath()
        );
        return Mage::getSingleton('Magento_Persistent_Model_Observer')->applyBlockPersistentData($observer);
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
        if (!Mage::helper('Magento_PersistentHistory_Helper_Data')->isOrderedItemsPersist()) {
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
        if (!Mage::helper('Magento_PersistentHistory_Helper_Data')->isViewedProductsPersist()) {
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
        $block =  Mage::app()->getLayout()->getBlock('checkout.links');
        if ($block) {
            $block->removeLinkByUrl(Mage::getUrl('checkout/cart'));
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
        if (!Mage::helper('Magento_Persistent_Helper_Data')->canProcess($observer) || !$this->_isCompareProductsPersist()) {
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
        if (!Mage::helper('Magento_Persistent_Helper_Data')->canProcess($observer)
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
        if (!Mage::helper('Magento_Persistent_Helper_Data')->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession Magento_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');

        $helper = Mage::helper('Magento_PersistentHistory_Helper_Data');
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
            $optionCustomerSegm = Mage::getModel('Mage_Core_Model_Config_Value')
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
        Mage::getSingleton('Magento_Catalog_Model_Product_Compare_Item')->bindCustomerLogout();
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
        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')
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
        Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')
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
        return Mage::helper('Magento_Persistent_Helper_Session');
    }

    /**
     * Init persistent wishlist
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _initWishlist()
    {
        return Mage::getModel('Magento_Wishlist_Model_Wishlist')->loadByCustomer($this->_getCustomerId() ,true);
    }

    /**
     * Check whether wishlist is persist
     *
     * @return bool
     */
    protected function _isWishlistPersist()
    {
        return Mage::helper('Magento_PersistentHistory_Helper_Data')->isWishlistPersist();
    }

    /**
     * Check whether compare products is persist
     *
     * @return bool
     */
    protected function _isCompareProductsPersist()
    {
        return Mage::helper('Magento_PersistentHistory_Helper_Data')->isCompareProductsPersist();
    }

    /**
     * Check whether compared products is persist
     *
     * @return bool
     */
    protected function _isComparedProductsPersist()
    {
        return Mage::helper('Magento_PersistentHistory_Helper_Data')->isComparedProductsPersist();
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
        return $this->_isPersistent() && !Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !Mage::helper('Magento_Persistent_Helper_Data')->isShoppingCartPersist();
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
