<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected function _backToCart()
    {
        $this->_redirect('checkout/cart');
        return $this;
    }
    
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }
    
    public function indexAction()
    {
        Mage::getSingleton('checkout/session')->resetCheckout();
        $this->loadLayout(array('default', 'cart'), 'cart');
        
        $this->renderLayout();
    }
    
    public function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $productId = $intFilter->filter($this->getRequest()->getParam('product'));
        
        if (empty($productId)) {
            $this->_backToCart();
            return;
        }
        
        $qty = $intFilter->filter($this->getRequest()->getParam('qty', 1));

        $product = Mage::getModel('catalog/product')->load($productId);
        if ($product->getId()) {
            $this->getQuote()->addCatalogProduct($product->setQty($qty));
            $this->getQuote()->getShippingAddress()->collectTotals();
            $this->getQuote()->save();
        }
        
        Mage::getSingleton('checkout/session')->setQuoteId($this->getQuote()->getId());
                
        $this->_backToCart();
    }
    
    public function updatePostAction()
    {
        $cart = $this->getRequest()->getParam('cart');
        $customer = Mage::getSingleton('customer/session')->getCustomer();

        foreach ($cart as $id=>$itemUpd) {
            if (empty($itemUpd['qty']) || !is_numeric($itemUpd['qty']) || intval($itemUpd['qty'])<=0) {
                continue;
            }
            
            $itemUpd['qty'] = (int) $itemUpd['qty'];
            
            if (!empty($itemUpd['remove'])) {
                $this->getQuote()->removeItem($id);
            } else {
                $item = $this->getQuote()->getItemById($id);
                if (!$item) {
                    continue;
                }
                if (!empty($itemUpd['wishlist']) && !empty($customer)) {
                    if (empty($wishlist)) {
                        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
                    }
                    $wishlist->addNewItem($item->getProductId())->save();
                    $this->getQuote()->removeItem($id);
                    continue;
                }
                
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $item->setQty($itemUpd['qty']);
                $item->setPrice($product->getFinalPrice($item->getQty()));
            }
        }
        $this->getQuote()->getShippingAddress()->collectTotals();
        $this->getQuote()->save();

        $this->_backToCart();
    }
    
    public function cleanAction()
    {
        
    }
    
    public function estimatePostAction()
    {
        $postcode = $this->getRequest()->getParam('estimate_postcode');

        $this->getQuote()->getShippingAddress()
            ->setPostcode($postcode)->collectShippingRates();
            
        $this->getQuote()->save();
        
        $this->_backToCart();
    }
    
    public function estimateUpdatePostAction()
    {
        $code = $this->getRequest()->getParam('estimate_method');
        
        $this->getQuote()->getShippingAddress()->setShippingMethod($code)->collectTotals()->save();
        
        $this->_backToCart();
    }
    
    public function couponPostAction()
    {
        if ($this->getRequest()->getParam('do')==__('Clear')) {
            $couponCode = '';
        } else {
            $couponCode = $this->getRequest()->getParam('coupon_code');
        }
        
        $this->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
    
    public function giftCertPostAction()
    {
        if ($this->getRequest()->getParam('do')==__('Clear')) {
            $giftCode = '';
        } else {
            $giftCode = $this->getRequest()->getParam('giftcert_code');
        }
        
        $this->getQuote()->setGiftcertCode($giftCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
}