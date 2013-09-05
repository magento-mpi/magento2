<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports Event observer model
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Model_Event_Observer
{
    /**
     * Abstract Event obeserver logic
     *
     * Save event
     *
     * @param int $eventTypeId
     * @param int $objectId
     * @param int $subjectId
     * @param int $subtype
     * @return Magento_Reports_Model_Event_Observer
     */
    protected function _event($eventTypeId, $objectId, $subjectId = null, $subtype = 0)
    {
        if (is_null($subjectId)) {
            if (Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
                $customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
                $subjectId = $customer->getId();
            }
            else {
                $subjectId = Mage::getSingleton('Magento_Log_Model_Visitor')->getId();
                $subtype = 1;
            }
        }

        $eventModel = Mage::getModel('Magento_Reports_Model_Event');
        $storeId    = Mage::app()->getStore()->getId();
        $eventModel
            ->setEventTypeId($eventTypeId)
            ->setObjectId($objectId)
            ->setSubjectId($subjectId)
            ->setSubtype($subtype)
            ->setStoreId($storeId);
        $eventModel->save();

        return $this;
    }

    /**
     * Customer login action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function customerLogin(\Magento\Event\Observer $observer)
    {
        if (!Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
            return $this;
        }

        $visitorId  = Mage::getSingleton('Magento_Log_Model_Visitor')->getId();
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();
        $eventModel = Mage::getModel('Magento_Reports_Model_Event');
        $eventModel->updateCustomerType($visitorId, $customerId);

        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')
            ->updateCustomerFromVisitor()
            ->calculate();
        Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')
            ->updateCustomerFromVisitor()
            ->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function customerLogout(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')
            ->purgeVisitorByCustomer()
            ->calculate();
        Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')
            ->purgeVisitorByCustomer()
            ->calculate();
        return $this;
    }

    /**
     * View Catalog Product action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductView(\Magento\Event\Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW, $productId);
    }

    /**
     * Send Product link to friends action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function sendfriendProduct(\Magento\Event\Observer $observer)
    {
        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_SEND,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Remove Product from Compare Products action
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductCompareRemoveProduct(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')->calculate();

        return $this;
    }

    /**
     * Remove All Products from Compare Products
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductCompareClear(\Magento\Event\Observer $observer)
    {
        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')->calculate();

        return $this;
    }

    /**
     * Add Product to Compare Products List action
     *
     * Reset count of compared products cache
     *
     * @param \Magento\Event\Observer $observer
     * @return unknown
     */
    public function catalogProductCompareAddProduct(\Magento\Event\Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_COMPARE, $productId);
    }

    /**
     * Add product to shopping cart action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function checkoutCartAddProduct(\Magento\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getItem();
        if (!$quoteItem->getId() && !$quoteItem->getParentItem()) {
            $productId = $quoteItem->getProductId();
            $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_TO_CART, $productId);
        }
        return $this;
    }

    /**
     * Add product to wishlist action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function wishlistAddProduct(\Magento\Event\Observer $observer)
    {
        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Share customer wishlist action
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function wishlistShare(\Magento\Event\Observer $observer)
    {
        return $this->_event(Magento_Reports_Model_Event::EVENT_WISHLIST_SHARE,
            $observer->getEvent()->getWishlist()->getId()
        );
    }

    /**
     * Clean events by old visitors
     *
     * @see Global Log Clean Settings
     *
     * @param \Magento\Event\Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function eventClean(\Magento\Event\Observer $observer)
    {
        /* @var $event Magento_Reports_Model_Event */
        $event = Mage::getModel('Magento_Reports_Model_Event');
        $event->clean();

        Mage::getModel('Magento_Reports_Model_Product_Index_Compared')->clean();
        Mage::getModel('Magento_Reports_Model_Product_Index_Viewed')->clean();

        return $this;
    }
}
