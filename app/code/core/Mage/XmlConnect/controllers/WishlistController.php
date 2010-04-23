<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect wishlist controller
 *
 * @author  Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_WishlistController extends Mage_XmlConnect_Controller_Action
{

    /**
     * Check if customer is logged in
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            $this->setFlag('', 'no-dispatch', true);
            $this->_message($this->__('Customer not loggined.'), self::MESSAGE_STATUS_ERROR);
            return ;
        }
    }

    /**
     * Get customer session model
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Retrieve wishlist object
     *
     * @return Mage_Wishlist_Model_Wishlist|false
     */
    protected function _getWishlist()
    {
        try {
            $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer($this->_getCustomerSession()->getCustomer(), true);
            Mage::register('wishlist', $wishlist);
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
            return false;
        }
        catch (Exception $e) {
            $this->_message($this->__('Cannot create wishlist.'), self::MESSAGE_STATUS_ERROR);
            return false;
        }
        return $wishlist;
    }

    /**
     * Display customer wishlist
     */
    public function indexAction()
    {
        $this->_getWishlist();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Adding new item
     */
    public function addAction()
    {
        $session = $this->_getCustomerSession();
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            return;
        }

        $request = $this->getRequest();
        $productId = (int)$request->getParam('product');
        if (!$productId) {
            $this->_message($this->__('Product was not specified.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->_message($this->__('Cannot specify product'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        try {
            $item = $wishlist->addNewItem($product->getId());
            if (strlen(trim((string)$request->getParam('description')))) {
            	$item->setDescription($request->getParam('description'))
            	   ->save();
            }
            $wishlist->save();

            Mage::dispatchEvent('wishlist_add_product', array('wishlist'=>$wishlist, 'product'=>$product));

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist.', $product->getName());
            $this->_message($message, self::MESSAGE_STATUS_SUCCESS);
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message('An error occurred while adding item to wishlist.', self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Remove item
     */
    public function removeAction()
    {
        $wishlist = $this->_getWishlist();
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);

        if($item->getWishlistId() == $wishlist->getId()) {
            try {
                $item->delete();
                $wishlist->save();
                $this->_message($this->__('Item was successfully removed from wishlist.'), self::MESSAGE_STATUS_SUCCESS);
            }
            catch (Mage_Core_Exception $e) {
                $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
            }
            catch(Exception $e) {
                $this->_message('An error occurred while removing item from wishlist.', self::MESSAGE_STATUS_ERROR);
            }
        }
        else {
            $this->_message('Specified item down not exist in wishlist.', self::MESSAGE_STATUS_ERROR);
        }

        Mage::helper('wishlist')->calculate();
    }
}