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
 * Wishlist shared items controllers
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Wishlist\Controller;

class Shared extends \Magento\Wishlist\Controller\AbstractController
{
    /**
     * Retrieve wishlist instance by requested code
     *
     * @return \Magento\Wishlist\Model\Wishlist|false
     */
    protected function _getWishlist()
    {
        $code     = (string)$this->getRequest()->getParam('code');
        if (empty($code)) {
            return false;
        }

        $wishlist = \Mage::getModel('Magento\Wishlist\Model\Wishlist')->loadByCode($code);
        if (!$wishlist->getId()) {
            return false;
        }

        \Mage::getSingleton('Magento\Checkout\Model\Session')->setSharedWishlist($code);

        return $wishlist;
    }

    /**
     * Shared wishlist view page
     *
     */
    public function indexAction()
    {
        $wishlist   = $this->_getWishlist();
        $customerId = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId();

        if ($wishlist && $wishlist->getCustomerId() && $wishlist->getCustomerId() == $customerId) {
            $this->_redirectUrl(\Mage::helper('Magento\Wishlist\Helper\Data')->getListUrl($wishlist->getId()));
            return;
        }

        \Mage::register('shared_wishlist', $wishlist);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento\Checkout\Model\Session');
        $this->_initLayoutMessages('Magento\Wishlist\Model\Session');
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

        /* @var $item \Magento\Wishlist\Model\Item */
        $item = \Mage::getModel('Magento\Wishlist\Model\Item')->load($itemId);


        /* @var $session \Magento\Core\Model\Session\Generic */
        $session    = \Mage::getSingleton('Magento\Wishlist\Model\Session');
        $cart       = \Mage::getSingleton('Magento\Checkout\Model\Cart');

        $redirectUrl = $this->_getRefererUrl();

        try {
            $options = \Mage::getModel('Magento\Wishlist\Model\Item\Option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $item->addToCart($cart);
            $cart->save()->getQuote()->collectTotals();

            if (\Mage::helper('Magento\Checkout\Helper\Cart')->getShouldRedirectToCart()) {
                $redirectUrl = \Mage::helper('Magento\Checkout\Helper\Cart')->getCartUrl();
            }
        } catch (\Magento\Core\Exception $e) {
            if ($e->getCode() == \Magento\Wishlist\Model\Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(__('This product(s) is out of stock.'));
            } else {
                \Mage::getSingleton('Magento\Catalog\Model\Session')->addNotice($e->getMessage());
                $redirectUrl = $item->getProductUrl();
            }
        } catch (\Exception $e) {
            $session->addException($e, __('Cannot add item to shopping cart'));
        }

        return $this->_redirectUrl($redirectUrl);
    }
}
