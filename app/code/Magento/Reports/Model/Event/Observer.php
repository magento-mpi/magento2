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
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Reports_Model_EventFactory
     */
    protected $_eventFactory;

    /**
     * @var Magento_Reports_Model_Product_Index_ComparedFactory
     */
    protected $_productCompFactory;

    /**
     * @var Magento_Reports_Model_Product_Index_ViewedFactory
     */
    protected $_productIndxFactory;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Log_Model_Visitor
     */
    protected $_logVisitor;

    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Reports_Model_EventFactory $event,
        Magento_Reports_Model_Product_Index_ComparedFactory $productCompFactory,
        Magento_Reports_Model_Product_Index_ViewedFactory $productIndxFactory,
        Magento_Customer_Model_Session $customerSession,
        Magento_Log_Model_Visitor $logVisitor
    ) {
        $this->_storeManager = $storeManager;
        $this->_eventFactory = $event;
        $this->_productCompFactory = $productCompFactory;
        $this->_productIndxFactory = $productIndxFactory;
        $this->_customerSession = $customerSession;
        $this->_logVisitor = $logVisitor;
    }
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
            if ($this->_customerSession->isLoggedIn()) {
                $customer = $this->_customerSession->getCustomer();
                $subjectId = $customer->getId();
            }
            else {
                $subjectId = $this->_logVisitor->getId();
                $subtype = 1;
            }
        }

        $eventModel = $this->_eventFactory->create();
        $storeId    = $this->_storeManager->getStore()->getId();
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
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function customerLogin(Magento_Event_Observer $observer)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return $this;
        }

        $visitorId  = $this->_logVisitor->getId();
        $customerId = $this->_customerSession->getCustomerId();
        $eventModel = $this->_eventFactory->create();
        $eventModel->updateCustomerType($visitorId, $customerId);

        $this->_productCompFactory
            ->create()
            ->updateCustomerFromVisitor()
            ->calculate();
        $this->_productIndxFactory
            ->create()
            ->updateCustomerFromVisitor()
            ->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function customerLogout(Magento_Event_Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->purgeVisitorByCustomer()
            ->calculate();
        $this->_productIndxFactory
            ->create()
            ->purgeVisitorByCustomer()
            ->calculate();
        return $this;
    }

    /**
     * View Catalog Product action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductView(Magento_Event_Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        $this->_productIndxFactory
            ->create()
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW, $productId);
    }

    /**
     * Send Product link to friends action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function sendfriendProduct(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductCompareRemoveProduct(Magento_Event_Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->calculate();

        return $this;
    }

    /**
     * Remove All Products from Compare Products
     *
     * Reset count of compared products cache
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function catalogProductCompareClear(Magento_Event_Observer $observer)
    {
        $this->_productCompFactory
            ->create()
            ->calculate();

        return $this;
    }

    /**
     * Add Product to Compare Products List action
     *
     * Reset count of compared products cache
     *
     * @param Magento_Event_Observer $observer
     * @return unknown
     */
    public function catalogProductCompareAddProduct(Magento_Event_Observer $observer)
    {
        $productId = $observer->getEvent()->getProduct()->getId();

        $this->_productCompFactory
            ->create()
            ->setProductId($productId)
            ->save()
            ->calculate();

        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_COMPARE, $productId);
    }

    /**
     * Add product to shopping cart action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function checkoutCartAddProduct(Magento_Event_Observer $observer)
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
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function wishlistAddProduct(Magento_Event_Observer $observer)
    {
        return $this->_event(Magento_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
            $observer->getEvent()->getProduct()->getId()
        );
    }

    /**
     * Share customer wishlist action
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Reports_Model_Event_Observer
     */
    public function wishlistShare(Magento_Event_Observer $observer)
    {
        return $this->_event(Magento_Reports_Model_Event::EVENT_WISHLIST_SHARE,
            $observer->getEvent()->getWishlist()->getId()
        );
    }
}
