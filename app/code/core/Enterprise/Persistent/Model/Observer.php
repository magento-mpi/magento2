<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Persistent
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Persistent_Model_Observer
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
     * @param Varien_Event_Observer $observer
     * @return Enterprise_Persistent_Model_Observer
     */
    public function emulateCustomer($observer)
    {
        if (!Mage::helper('Mage_Persistent_Helper_Data')->canProcess($observer)
            || !Mage::helper('Enterprise_Persistent_Helper_Data')->isCustomerAndSegmentsPersist()
        ) {
            return $this;
        }

        if ($this->_isLoggedOut()) {
            /** @var $customer Mage_Customer_Model_Customer */
            $customer = Mage::getModel('Mage_Customer_Model_Customer')->load(
                $this->_getPersistentHelper()->getSession()->getCustomerId()
            );
            Mage::getSingleton('Mage_Customer_Model_Session')
                ->setCustomerId($customer->getId())
                ->setCustomerGroupId($customer->getGroupId());

            // apply persistent data to segments
            Mage::register('segment_customer', $customer, true);
        }
        return $this;
    }

    /**
     * Modify expired quotes cleanup
     *
     * @param Varien_Event_Observer $observer
     */
    public function modifyExpiredQuotesCleanup($observer)
    {
        /** @var $salesObserver Mage_Sales_Model_Observer */
        $salesObserver = $observer->getEvent()->getSalesObserver();
        $salesObserver->setExpireQuotesAdditionalFilterFields(array(
            'is_persistent' => 0
        ));
    }

    /**
     * Apply persistent data
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function applyPersistentData($observer)
    {
        if (!Mage::helper('Mage_Persistent_Helper_Data')->canProcess($observer)
            || !$this->_isPersistent() || Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()
        ) {
            return;
        }
        Mage::getModel('Mage_Persistent_Model_Persistent_Config')
            ->setConfigFilePath(Mage::helper('Enterprise_Persistent_Helper_Data')->getPersistentConfigFilePath())
            ->fire();
    }

    public function applyBlockPersistentData($observer)
    {
        $observer->getEvent()->setConfigFilePath(Mage::helper('Enterprise_Persistent_Helper_Data')->getPersistentConfigFilePath());
        return Mage::getSingleton('Mage_Persistent_Model_Observer')->applyBlockPersistentData($observer);
    }

    /**
     * Set whislist items count in top wishlist link block
     *
     * @param Mage_Core_Block_Abstract $block
     * @return null
     */
    public function initWishlist($block)
    {
        if (!$this->_isWishlistPersist()) {
            return;
        }
        $block->setItemCount($this->_initWishlist()->getItemsCount());
        $block->initLinkProperties();
    }

    /**
     * Set persistent wishlist to wishlist sidebar block
     *
     * @param Mage_Core_Block_Abstract $block
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
     * @param Mage_Core_Block_Abstract $block
     * @return null
     */
    public function initReorderSidebar($block)
    {
        if (!Mage::helper('Enterprise_Persistent_Helper_Data')->isOrderedItemsPersist()) {
            return;
        }
        $block->setCustomerId($this->_getCustomerId());
        $block->initOrders();
    }

    /**
     * Emulate 'viewed products' block with persistent data
     *
     * @param Mage_Reports_Block_Product_Viewed $block
     * @return null
     */
    public function emulateViewedProductsBlock(Mage_Reports_Block_Product_Viewed $block)
    {
        if (!Mage::helper('Enterprise_Persistent_Helper_Data')->isViewedProductsPersist()) {
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
     * @param Varien_Event_Observer $observer
     */
    public function removeCartLink($observer)
    {
        $block =  Mage::getSingleton('Mage_Core_Model_Layout')->getBlock('checkout.links');
        if ($block) {
            $block->removeLinkByUrl(Mage::getUrl('checkout/cart'));
        }
    }

    /**
     * Emulate 'compared products' block with persistent data
     *
     * @param Mage_Reports_Block_Product_Compared $block
     * @return null
     */
    public function emulateComparedProductsBlock(Mage_Reports_Block_Product_Compared $block)
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
     * @param Mage_Catalog_Block_Product_Compare_Sidebar $block
     * @return null
     */
    public function emulateCompareProductsBlock(Mage_Catalog_Block_Product_Compare_Sidebar $block)
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
     * @param Mage_Catalog_Block_Product_Compare_List $block
     * @return null
     */
    public function emulateCompareProductsListBlock(Mage_Catalog_Block_Product_Compare_List $block)
    {
        if (!$this->_isCompareProductsPersist()) {
            return;
        }
        $block->setCustomerId($this->_getCustomerId());
    }

    /**
     * Apply persistent customer id
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function applyCustomerId($observer)
    {
        if (!Mage::helper('Mage_Persistent_Helper_Data')->canProcess($observer) || !$this->_isCompareProductsPersist()) {
            return;
        }
        $instance = $observer->getEvent()->getControllerAction();
        $instance->setCustomerId($this->_getCustomerId());
    }

    /**
     * Emulate customer wishlist (add, delete, etc)
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function emulateWishlist($observer)
    {
        if (!Mage::helper('Mage_Persistent_Helper_Data')->canProcess($observer)
            || !$this->_isPersistent() || !$this->_isWishlistPersist()
        ) {
            return;
        }

        $wishlist = $this->_initWishlist();
        if ($wishlist->getId()) {
            /** @var $controller Mage_Wishlist_IndexController */
            $controller = $observer->getEvent()->getControllerAction();
            if ($controller instanceof Mage_Wishlist_IndexController) {
                Mage::register('wishlist', $wishlist);
                $controller->skipAuthentication();
            }
        }
    }

    /**
     * Set persistent data into quote
     *
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function setQuotePersistentData($observer)
    {
        if (!Mage::helper('Mage_Persistent_Helper_Data')->canProcess($observer) || !$this->_isPersistent()) {
            return;
        }

        /** @var $quote Mage_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        /** @var $customerSession Mage_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Mage_Customer_Model_Session');

        if (Mage::helper('Enterprise_Persistent_Helper_Data')->isCustomerAndSegmentsPersist() && $this->_setQuotePersistent) {
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
     * @see Enterprise_Persistent_Model_Observer::setQuotePersistentData
     */
    public function preventSettingQuotePersistent($observer)
    {
        $this->_setQuotePersistent = false;
    }

    /**
     * Update Option "Persist Customer Group Membership and Segmentation"
     * set value "Yes" if option "Persist Shopping Cart" equals "Yes"
     *
     * @param  $observer Enterprise_Persistent_Model_Observer
     * @return void
     */
    public function updateOptionCustomerSegmentation($observer)
    {
        $eventDataObject = $observer->getEvent()->getDataObject();

        if ($eventDataObject->getValue()) {
            $optionCustomerSegm = Mage::getModel('Mage_Core_Model_Config_Data')
                ->setScope($eventDataObject->getScope())
                ->setScopeId($eventDataObject->getScopeId())
                ->setPath(Enterprise_Persistent_Helper_Data::XML_PATH_PERSIST_CUSTOMER_AND_SEGM)
                ->setValue(true)
                ->save();
        }
    }

    /**
     * Expire data of Sidebars
     *
     * @param Varien_Event_Observer $observer
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
        Mage::getSingleton('Mage_Catalog_Model_Product_Compare_Item')->bindCustomerLogout();
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
        Mage::getModel('Mage_Reports_Model_Product_Index_Compared')
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
        Mage::getModel('Mage_Reports_Model_Product_Index_Viewed')
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
     * @return Mage_Persistent_Helper_Session
     */
    protected function _getPersistentHelper()
    {
        return Mage::helper('Mage_Persistent_Helper_Session');
    }

    /**
     * Init persistent wishlist
     *
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _initWishlist()
    {
        return Mage::getModel('Mage_Wishlist_Model_Wishlist')->loadByCustomer($this->_getCustomerId() ,true);
    }

    /**
     * Check whether wishlist is persist
     *
     * @return bool
     */
    protected function _isWishlistPersist()
    {
        return Mage::helper('Enterprise_Persistent_Helper_Data')->isWishlistPersist();
    }

    /**
     * Check whether compare products is persist
     *
     * @return bool
     */
    protected function _isCompareProductsPersist()
    {
        return Mage::helper('Enterprise_Persistent_Helper_Data')->isCompareProductsPersist();
    }

    /**
     * Check whether compared products is persist
     *
     * @return bool
     */
    protected function _isComparedProductsPersist()
    {
        return Mage::helper('Enterprise_Persistent_Helper_Data')->isComparedProductsPersist();
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
        return $this->_isPersistent() && !Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();
    }

    /**
     * Check if shopping cart is guest while persistent session and user is logged out
     *
     * @return bool
     */
    protected function _isGuestShoppingCart()
    {
        return $this->_isLoggedOut() && !Mage::helper('Mage_Persistent_Helper_Data')->isShoppingCartPersist();
    }

    /**
     * Skip website restriction and allow access for persistent customers
     *
     * @param Varien_Event_Observer $observer
     */
    public function skipWebsiteRestriction(Varien_Event_Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        if ($result->getShouldProceed() && $this->_isPersistent()) {
            $result->setCustomerLoggedIn(true);
        }
    }
}
