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
class Magento_Wishlist_Controller_Shared extends Magento_Wishlist_Controller_Abstract
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Core_Model_Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Retrieve wishlist instance by requested code
     *
     * @return Magento_Wishlist_Model_Wishlist|false
     */
    protected function _getWishlist()
    {
        $code     = (string)$this->getRequest()->getParam('code');
        if (empty($code)) {
            return false;
        }

        $wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist')->loadByCode($code);
        if (!$wishlist->getId()) {
            return false;
        }

        Mage::getSingleton('Magento_Checkout_Model_Session')->setSharedWishlist($code);

        return $wishlist;
    }

    /**
     * Shared wishlist view page
     *
     */
    public function indexAction()
    {
        $wishlist   = $this->_getWishlist();
        $customerId = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId();

        if ($wishlist && $wishlist->getCustomerId() && $wishlist->getCustomerId() == $customerId) {
            $this->_redirectUrl($this->_objectManager->get('Magento_Wishlist_Helper_Data')->getListUrl($wishlist->getId()));
            return;
        }

        $this->_coreRegistry->register('shared_wishlist', $wishlist);

        $this->loadLayout();
        $this->_initLayoutMessages('Magento_Checkout_Model_Session');
        $this->_initLayoutMessages('Magento_Wishlist_Model_Session');
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

        /* @var $item Magento_Wishlist_Model_Item */
        $item = Mage::getModel('Magento_Wishlist_Model_Item')->load($itemId);


        /* @var $session Magento_Core_Model_Session_Generic */
        $session    = Mage::getSingleton('Magento_Wishlist_Model_Session');
        $cart       = Mage::getSingleton('Magento_Checkout_Model_Cart');

        $redirectUrl = $this->_getRefererUrl();

        try {
            $options = Mage::getModel('Magento_Wishlist_Model_Item_Option')->getCollection()
                    ->addItemFilter(array($itemId));
            $item->setOptions($options->getOptionsByItem($itemId));

            $item->addToCart($cart);
            $cart->save()->getQuote()->collectTotals();

            if ($this->_objectManager->get('Magento_Checkout_Helper_Cart')->getShouldRedirectToCart()) {
                $redirectUrl = $this->_objectManager->get('Magento_Checkout_Helper_Cart')->getCartUrl();
            }
        } catch (Magento_Core_Exception $e) {
            if ($e->getCode() == Magento_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                $session->addError(__('This product(s) is out of stock.'));
            } else {
                Mage::getSingleton('Magento_Catalog_Model_Session')->addNotice($e->getMessage());
                $redirectUrl = $item->getProductUrl();
            }
        } catch (Exception $e) {
            $session->addException($e, __('Cannot add item to shopping cart'));
        }

        return $this->_redirectUrl($redirectUrl);
    }
}
