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
 * Wishlist shared items controllers
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Wishlist_Controller_Shared extends Mage_Wishlist_Controller_Abstract
{
    /**
     * Retrieve wishlist instance by requested code
     *
     * @return Mage_Wishlist_Model_Wishlist|false
     */
    protected function _getWishlist()
    {
        $code     = (string)$this->getRequest()->getParam('code');
        if (empty($code)) {
            return false;
        }

        $wishlist = Mage::getModel('Mage_Wishlist_Model_Wishlist')->loadByCode($code);
        if (!$wishlist->getId()) {
            return false;
        }

        Mage::getSingleton('Mage_Checkout_Model_Session')->setSharedWishlist($code);

        return $wishlist;
    }

    /**
     * Shared wishlist view page
     *
     */
    public function indexAction()
    {
        $wishlist   = $this->_getWishlist();
        $customerId = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerId();

        if ($wishlist && $wishlist->getCustomerId() && $wishlist->getCustomerId() == $customerId) {
            $this->_redirectUrl(Mage::helper('Mage_Wishlist_Helper_Data')->getListUrl($wishlist->getId()));
            return;
        }

        Mage::register('shared_wishlist', $wishlist);

        $this->loadLayout();
        $this->_initLayoutMessages('Mage_Checkout_Model_Session');
        $this->_initLayoutMessages('Mage_Wishlist_Model_Session');
        $this->renderLayout();
    }

    /**
     * Add shared wishlist item to shopping cart
     *
     * If Product has required options - redirect
     * to product view page with message about needed defined required options
     *
     */
    public function cartAction()
    {
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('Mage_Wishlist_Model_Item')->load($itemId);


        /* @var $session Mage_Core_Model_Session_Generic */
        $session    = Mage::getSingleton('Mage_Wishlist_Model_Session');
        $cart       = Mage::getSingleton('Mage_Checkout_Model_Cart');

        $redirectUrl = $this->_getRefererUrl();

        try {
            $options = Mage::getModel('Mage_Wishlist_Model_Item_Option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $item->addToCart($cart);
            $cart->save()->getQuote()->collectTotals();

            if (Mage::helper('Mage_Checkout_Helper_Cart')->getShouldRedirectToCart()) {
                $redirectUrl = Mage::helper('Mage_Checkout_Helper_Cart')->getCartUrl();
            }
        } catch (Mage_Core_Exception $e) {
            if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(__('This product(s) is out of stock.'));
            } else {
                Mage::getSingleton('Mage_Catalog_Model_Session')->addNotice($e->getMessage());
                $redirectUrl = $item->getProductUrl();
            }
        } catch (Exception $e) {
            $session->addException($e, __('Cannot add item to shopping cart'));
        }

        return $this->_redirectUrl($redirectUrl);
    }
}
