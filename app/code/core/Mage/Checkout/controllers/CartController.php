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
        $this->loadLayout();
        $data = array();
        
        $this->getLayout()->getBlock('content')->append(
            $this->getLayout()->createBlock('checkout/cart', 'cart.view')
        );
        
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
            $this->getQuote()->addCatalogProduct($product->setQty($qty))->save();
        }
        
        Mage::getSingleton('checkout/session')->setQuoteId($this->getQuote()->getId());
                
        $this->_backToCart();
    }
    
    public function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');

        $this->getQuote()->updateItems($cart)->save();

        $this->_backToCart();
    }
    
    public function cleanAction()
    {
        
    }
    
    public function estimatePostAction()
    {
        $postcode = $this->getRequest()->getPost('estimate_postcode');

        $this->getQuote()->getShippingAddress()
            ->setPostcode($postcode)->collectShippingRates();
            
        $this->getQuote()->save();
        
        $this->_backToCart();
    }
    
    public function estimateUpdatePostAction()
    {
        $code = $this->getRequest()->getPost('estimate_method');
        
        $this->getQuote()->setShippingMethod($code)->save();
        
        $this->_backToCart();
    }
    
    public function couponPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $couponCode = '';
        } else {
            $couponCode = $this->getRequest()->getPost('coupon_code');
        }
        
        $this->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
    
    public function giftCertPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $giftCode = '';
        } else {
            $giftCode = $this->getRequest()->getPost('giftcert_code');
        }
        
        $this->getQuote()->setGiftcertCode($giftCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
}