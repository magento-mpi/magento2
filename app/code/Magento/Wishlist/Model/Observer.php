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
 * Shopping cart operation observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Wishlist_Model_Observer extends Magento_Core_Model_Abstract
{
    /**
     * Wishlist data
     *
     * @var Magento_Wishlist_Helper_Data
     */
    protected $_wishlistData = null;

    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Wishlist_Model_WishlistFactory
     */
    protected $_wishlistFactory;

    /**
     * @param Magento_Wishlist_Helper_Data $wishlistData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Wishlist_Model_WishlistFactory $wishlistFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Wishlist_Helper_Data $wishlistData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Wishlist_Model_WishlistFactory $wishlistFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_wishlistData = $wishlistData;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_wishlistFactory = $wishlistFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get customer wishlist model instance
     *
     * @param   int $customerId
     * @return  Magento_Wishlist_Model_Wishlist || false
     */
    protected function _getWishlist($customerId)
    {
        if (!$customerId) {
            return false;
        }
        return $this->_wishlistFactory->create()->loadByCustomer($customerId, true);
    }

    /**
     * Check move quote item to wishlist request
     *
     * @param   Magento_Event_Observer $observer
     * @return  Magento_Wishlist_Model_Observer
     */
    public function processCartUpdateBefore($observer)
    {
        $cart = $observer->getEvent()->getCart();
        $data = $observer->getEvent()->getInfo();
        $productIds = array();

        $wishlist = $this->_getWishlist($cart->getQuote()->getCustomerId());
        if (!$wishlist) {
            return $this;
        }

        /**
         * Collect product ids marked for move to wishlist
         */
        foreach ($data as $itemId => $itemInfo) {
            if (!empty($itemInfo['wishlist'])) {
                if ($item = $cart->getQuote()->getItemById($itemId)) {
                    $productId  = $item->getProductId();
                    $buyRequest = $item->getBuyRequest();

                    if (isset($itemInfo['qty']) && is_numeric($itemInfo['qty'])) {
                        $buyRequest->setQty($itemInfo['qty']);
                    }
                    $wishlist->addNewItem($productId, $buyRequest);

                    $productIds[] = $productId;
                    $cart->getQuote()->removeItem($itemId);
                }
            }
        }

        if (!empty($productIds)) {
            $wishlist->save();
            $this->_wishlistData->calculate();
        }
        return $this;
    }

    public function processAddToCart($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist = $this->_checkoutSession->getSharedWishlist();
        $messages = $this->_checkoutSession->getWishlistPendingMessages();
        $urls = $this->_checkoutSession->getWishlistPendingUrls();
        $wishlistIds = $this->_checkoutSession->getWishlistIds();
        $singleWishlistId = $this->_checkoutSession->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = array($singleWishlistId);
        }

        if (count($wishlistIds) && $request->getParam('wishlist_next')) {
            $wishlistId = array_shift($wishlistIds);

            if ($this->_customerSession->isLoggedIn()) {
                $wishlist = $this->_wishlistFactory->create()
                        ->loadByCustomer($this->_customerSession->getCustomer(), true);
            } else if ($sharedWishlist) {
                $wishlist = $this->_wishlistFactory->create()->loadByCode($sharedWishlist);
            } else {
                return;
            }

            $wishlist->getItemCollection()->load();

            foreach ($wishlist->getItemCollection() as $wishlistItem) {
                if ($wishlistItem->getId() == $wishlistId) {
                    $wishlistItem->delete();
                }
            }
            $this->_checkoutSession->setWishlistIds($wishlistIds);
            $this->_checkoutSession->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && count($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            $this->_checkoutSession->setWishlistPendingUrls($urls);
            $this->_checkoutSession->setWishlistPendingMessages($messages);

            $this->_checkoutSession->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            $this->_checkoutSession->setNoCartRedirect(true);
        }
    }

    /**
     * Customer login processing
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Wishlist_Model_Observer
     */
    public function customerLogin(Magento_Event_Observer $observer)
    {
        $this->_wishlistData->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Magento_Event_Observer $observer
     * @return Magento_Wishlist_Model_Observer
     */
    public function customerLogout(Magento_Event_Observer $observer)
    {
        $this->_customerSession->setWishlistItemCount(0);

        return $this;
    }
}
