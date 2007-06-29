<?php
/**
 * Customer wishlist controller
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_WishlistController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        
        if (!Mage::getConfig()->getModuleConfig('Mage_Customer')->is('wishlistActive')) {
            $this->getResponse()->setRedirect('noRoute');
        }
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }
    
    public function indexAction()
    {
        $this->loadLayout();
        
        $collection = Mage::getSingleton('customer/session')->getCustomer()
            ->getWishlistCollection();
        
        $collection->getProductCollection()
            ->addAttributeToSelect('name');
        $collection->load();    
        
        $block = $this->getLayout()->createBlock('core/template', 'wishlist')
            ->setTemplate('customer/wishlist.phtml')
            ->assign('wishlist', $collection);
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    public function updatePostAction()
    {
        $p = $this->getRequest()->getPost();
        if (!empty($p['wishlist'])) {
            foreach ($p['wishlist'] as $itemId=>$dummy) {
                if (isset($p['to_cart'][$itemId])) {
                    $wishlist = Mage::getModel('customer/wishlist')->load($itemId);
                    
                    $product = Mage::getModel('catalog/product')->load($wishlist->getProductId())->setQty(1);
                    
                    $quote = Mage::getSingleton('checkout/session')->getQuote();
                    $quote->addProduct($product)->save();
                    
                    $wishlist->delete();
                }
                if (isset($p['remove'][$itemId])) {
                    $wishlist = Mage::getModel('customer/wishlist')->load($itemId);
                    $wishlist->delete();
                }
            }
            if (isset($p['to_cart'])) {
                $this->getResponse()->setRedirect(Mage::getUrl('checkout/cart'));
                return;
            }
        }
        $this->getResponse()->setRedirect(Mage::getUrl('customer/wishlist'));
    }
    
    public function addAction()
    {
        $productId = $this->getRequest()->getParam('product');
        try {
            Mage::getModel('customer/wishlist')->setProductId($productId)->save();
        }
        catch (Exception $e){
            
        }
        if (false && $url = $this->getRequest()->getServer('HTTP_REFERER')) {
            $this->getResponse()->setRedirect($url);
            return;
        }
        $this->getResponse()->setRedirect(Mage::getUrl('customer/wishlist'));
    }
}