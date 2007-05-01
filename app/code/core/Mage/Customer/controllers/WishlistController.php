<?php
/**
 * Customer wishlist controller
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_WishlistController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::getConfig()->getModule('Mage_Customer')->is('wishlistActive')) {
            $this->_redirect('noRoute');
        }
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        
        $collection = Mage::getSingleton('customer', 'session')->getCustomer()
            ->getWishlistCollection();
        
        $collection->getProductCollection()
            ->addAttributeToSelect('name');
        $collection->load();    
        
        $block = $this->getLayout()->createBlock('tpl', 'wishlist')
            ->setTemplate('customer/wishlist.phtml')
            ->assign('wishlist', $collection);
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function updatePostAction()
    {
        $p = $this->getRequest()->getPost();
        if (!empty($p['wishlist'])) {
            foreach ($p['wishlist'] as $itemId) {
                if (isset($p['to_cart'][$itemId])) {
                    $wishlist = Mage::getModel('customer', 'wishlist')->load($itemId);
                    //TODO: add to cart (add method to quote model)
                    $wishlist->delete();
                }
                if (isset($p['remove'][$itemId])) {
                    $wishlist = Mage::getModel('customer', 'wishlist')->load($itemId);
                    $wishlist->delete();
                }
            }
            if (isset($p['to_cart'])) {
                $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
            }
        }
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'wishlist')));
    }
    
    public function addAction()
    {
        $productId = $this->getRequest()->getParam('product');
        try {
            Mage::getModel('customer', 'wishlist')->setProductId($productId)->save();
        }
        catch (Exception $e){
            
        }
        if (false && $url = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->_redirect($url);
        }
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'wishlist')));
    }
}