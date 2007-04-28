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
        
        $block = Mage::createBlock('tpl', 'wishlist')
            ->setTemplate('customer/wishlist.phtml')
            ->assign('wishlist', $collection);
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function updatePostAction()
    {
        $items = $this->getRequest()->getPost('to_cart', array());
        if (!empty($items)) {
            foreach ($items as $itemId) {
                $wishlist = Mage::getModel('customer', 'wishlist')->load($itemId);
                //TODO: add to cart (add method to quote model)
                
                $wishlist->delete();
            }
            $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
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
        if ($url = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->_redirect($url);
        }
        $this->_redirect(Mage::getUrl('customer', array('controller'=>'wishlist')));
    }
}