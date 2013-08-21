<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll index controller
 *
 * @file        IndexController.php
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rss_Controller_Index extends Magento_Core_Controller_Front_Action
{
    /**
     * Current wishlist
     *
     * @var Magento_Wishlist_Model_Wishlist
     */
    protected $_wishlist;

    /**
     * Current customer
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Index action
     */
    public function indexAction()
    {
        if (Mage::getStoreConfig('rss/config/active')) {
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->norouteAction();
        }
    }

    /**
     * Display feed not found message
     */
    public function nofeedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found')
            ->setHeader('Status', '404 File not found')
            ->setHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->setBody(__('There was no RSS feed enabled.'))
        ;
    }

    /**
     * Wishlist rss feed action
     * Show all public wishlists and private wishlists that belong to current user
     *
     * @return mixed
     */
    public function wishlistAction()
    {
        if (Mage::getStoreConfig('rss/wishlist/active')) {
            $wishlist = $this->_getWishlist();
            if ($wishlist && ($wishlist->getVisibility()
                || Mage::getSingleton('Magento_Customer_Model_Session')->authenticate($this)
                    && $wishlist->getCustomerId() == $this->_getCustomer()->getId())
            ) {
                $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
                $this->loadLayout(false);
                $this->renderLayout();
                return;
            }
        }
        $this->nofeedAction();
    }

    /**
     * Retrieve Wishlist model
     *
     * @return Magento_Wishlist_Model_Wishlist
     */
    protected function _getWishlist()
    {
        if (is_null($this->_wishlist)) {
            $this->_wishlist = Mage::getModel('Magento_Wishlist_Model_Wishlist');
            $wishlistId = $this->getRequest()->getParam('wishlist_id');
            if ($wishlistId) {
                $this->_wishlist->load($wishlistId);
            } else {
                if($this->_getCustomer()->getId()) {
                    $this->_wishlist->loadByCustomer($this->_getCustomer());
                }
            }
        }
        return $this->_wishlist;
    }

    /**
     * Retrieve Customer instance
     *
     * @return Magento_Customer_Model_Customer
     */
    protected function _getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('Magento_Customer_Model_Customer');

            $params = Mage::helper('Magento_Core_Helper_Data')->urlDecode($this->getRequest()->getParam('data'));
            $data   = explode(',', $params);
            $customerId    = abs(intval($data[0]));
            if ($customerId && ($customerId == Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerId()) ) {
                $this->_customer->load($customerId);
            }
        }

        return $this->_customer;
    }
}
