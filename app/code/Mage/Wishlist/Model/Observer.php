<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart operation observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Get customer wishlist model instance
     *
     * @param   int $customerId
     * @return  Mage_Wishlist_Model_Wishlist || false
     */
    protected function _getWishlist($customerId)
    {
        if (!$customerId) {
            return false;
        }
        return Mage::getModel('Mage_Wishlist_Model_Wishlist')->loadByCustomer($customerId, true);
    }

    /**
     * Check move quote item to wishlist request
     *
     * @param   Magento_Event_Observer $observer
     * @return  Mage_Wishlist_Model_Observer
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
            Mage::helper('Mage_Wishlist_Helper_Data')->calculate();
        }
        return $this;
    }

    public function processAddToCart($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $sharedWishlist = Mage::getSingleton('Mage_Checkout_Model_Session')->getSharedWishlist();
        $messages = Mage::getSingleton('Mage_Checkout_Model_Session')->getWishlistPendingMessages();
        $urls = Mage::getSingleton('Mage_Checkout_Model_Session')->getWishlistPendingUrls();
        $wishlistIds = Mage::getSingleton('Mage_Checkout_Model_Session')->getWishlistIds();
        $singleWishlistId = Mage::getSingleton('Mage_Checkout_Model_Session')->getSingleWishlistId();

        if ($singleWishlistId) {
            $wishlistIds = array($singleWishlistId);
        }

        if (count($wishlistIds) && $request->getParam('wishlist_next')){
            $wishlistId = array_shift($wishlistIds);

            if (Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn()) {
                $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist')
                        ->loadByCustomer(Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer(), true);
            } else if ($sharedWishlist) {
                $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist')->loadByCode($sharedWishlist);
            } else {
                return;
            }


            $wishlist->getItemCollection()->load();

            foreach($wishlist->getItemCollection() as $wishlistItem){
                if ($wishlistItem->getId() == $wishlistId)
                    $wishlistItem->delete();
            }
            Mage::getSingleton('Mage_Checkout_Model_Session')->setWishlistIds($wishlistIds);
            Mage::getSingleton('Mage_Checkout_Model_Session')->setSingleWishlistId(null);
        }

        if ($request->getParam('wishlist_next') && count($urls)) {
            $url = array_shift($urls);
            $message = array_shift($messages);

            Mage::getSingleton('Mage_Checkout_Model_Session')->setWishlistPendingUrls($urls);
            Mage::getSingleton('Mage_Checkout_Model_Session')->setWishlistPendingMessages($messages);

            Mage::getSingleton('Mage_Checkout_Model_Session')->addError($message);

            $observer->getEvent()->getResponse()->setRedirect($url);
            Mage::getSingleton('Mage_Checkout_Model_Session')->setNoCartRedirect(true);
        }
    }

    /**
     * Customer login processing
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Wishlist_Model_Observer
     */
    public function customerLogin(Magento_Event_Observer $observer)
    {
        Mage::helper('Mage_Wishlist_Helper_Data')->calculate();

        return $this;
    }

    /**
     * Customer logout processing
     *
     * @param Magento_Event_Observer $observer
     * @return Mage_Wishlist_Model_Observer
     */
    public function customerLogout(Magento_Event_Observer $observer)
    {
        Mage::getSingleton('Mage_Customer_Model_Session')->setWishlistItemCount(0);

        return $this;
    }

}
