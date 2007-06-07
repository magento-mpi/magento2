<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected function _backToCart()
    {
        $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'cart')));
        return $this;
    }
    
    function indexAction()
    {
        $this->loadLayout();
        
        $quote = Mage::getSingleton('checkout', 'session')->getQuote();
        
        if (!$quote->hasItems()) {
            $cartView = 'checkout/cart/noItems.phtml';
        } else {
            $cartView = 'checkout/cart/view.phtml';
            $itemsFilter = new Varien_Filter_Object_Grid();
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $itemsFilter->addFilter(Mage::registry('website')->getPriceFilter(), 'price');
            $itemsFilter->addFilter(Mage::registry('website')->getPriceFilter(), 'row_total');
            $cartData['items'] = $itemsFilter->filter($quote->getItems());

            $totalsFilter = new Varien_Filter_Array_Grid();
            $totalsFilter->addFilter(Mage::registry('website')->getPriceFilter(), 'value');
            $cartData['totals'] = $totalsFilter->filter($quote->getTotals());
            
            $alnumFilter = new Zend_Filter_Alnum();
            $cartData['estimate_postcode'] = $alnumFilter->filter($quote->getEstimatePostcode());
            $cartData['coupon_code'] = $alnumFilter->filter($quote->getCouponCode());
            $cartData['giftcert_code'] = $alnumFilter->filter($quote->getGiftcertCode());
            
            $estimateFilter = new Varien_Filter_Object_Grid();
            $estimateFilter->addFilter(Mage::registry('website')->getPriceFilter(), 'amount');
            $cartData['estimate_methods'] = $estimateFilter->filter($quote->getEntitiesByType('shipping'));
            $cartData['estimate_method'] = $quote->getShippingMethod();

            $this->_data['cart'] = $cartData;
        }        
        
        $block = $this->getLayout()->createBlock('tpl', 'cart.view')
            ->setTemplate($cartView)
            ->assign('data', $this->_data)
            ->assign('wishlistActive', Mage::getConfig()->getModuleConfig('Mage_Customer')->is('wishlistActive'))
            ->assign('customerIsLogin', Mage::getSingleton('customer', 'session')->isLoggedIn());
            
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $productId = $intFilter->filter($this->getRequest()->getPost('product_id'));
        $qty = $intFilter->filter($this->getRequest()->getPost('qty', 1));

        $quote = Mage::getSingleton('checkout', 'session')->getQuote();

        $product = Mage::getModel('catalog', 'product')->load($productId);
        $quote->addProduct($product->setQty($qty));
        
        $quoteSession = Mage::getSingleton('checkout', 'session');
        $quote->save();
        
        Mage::getSingleton('checkout', 'session')->setQuoteId($quote->getQuoteId());
        
        $this->_backToCart();
    }
    
    function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');
        
        //foreach ($cart as )
        
        Mage::getSingleton('checkout', 'session')->getQuote()->updateItems($cart)->save();

        $this->_backToCart();
    }
    
    function cleanAction()
    {
        
    }
    
    function estimatePostAction()
    {
        $postcode = $this->getRequest()->getPost('estimate_postcode');
        $quote = Mage::getSingleton('checkout', 'session')->getQuote();
        $quote->setEstimatePostcode($postcode);
        $quote->estimateShippingMethods();
        $quote->save();
        
        $this->_backToCart();
    }
    
    function estimateUpdatePostAction()
    {
        $code = $this->getRequest()->getPost('estimate_method');
        Mage::getSingleton('checkout', 'session')->getQuote()->setShippingMethod($code)->save();
        
        $this->_backToCart();
    }
    
    function couponPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $couponCode = '';
        } else {
            $couponCode = $this->getRequest()->getPost('coupon_code');
        }
        
        Mage::getSingleton('checkout', 'session')->getQuote()->setCouponCode($couponCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
    
    public function giftCertPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $giftCode = '';
        } else {
            $giftCode = $this->getRequest()->getPost('giftcert_code');
        }
        
        Mage::getSingleton('checkout', 'session')->getQuote()->setGiftcertCode($giftCode)->collectTotals()->save();
        
        $this->_backToCart();
    }
}