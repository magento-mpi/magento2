<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        $this->_data['params'] = $this->getRequest()->getParams();
        $this->_data['quote'] = Mage::getSingleton('checkout', 'session')->getQuote();
    }
    
    function indexAction()
    {
        $this->loadLayout();
        
        $quote = $this->_data['quote'];
        
        if (!$quote->hasItems()) {
            $cartView = 'checkout/cart/noItems.phtml';
        } else {
            $cartView = 'checkout/cart/view.phtml';
            
            $itemsFilter = new Varien_Filter_Object_Grid();
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
            $cartData['items'] = $itemsFilter->filter($quote->getItems());

            $totalsFilter = new Varien_Filter_Array_Grid();
            $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
            $cartData['totals'] = $totalsFilter->filter($quote->getTotals());
            
            $alnumFilter = new Zend_Filter_Alnum();
            $cartData['estimate_postcode'] = $alnumFilter->filter($quote->getEstimatePostcode());
            $cartData['coupon_code'] = $alnumFilter->filter($quote->getCouponCode());
            $cartData['giftcert_code'] = $alnumFilter->filter($quote->getGiftcertCode());
            
            $estimateFilter = new Varien_Filter_Object_Grid();
            $estimateFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'amount');
            $cartData['estimate_methods'] = $estimateFilter->filter($quote->getEntitiesByType('shipping'));
            $cartData['estimate_method'] = $quote->getShippingMethod();

            $this->_data['cart'] = $cartData;
        }        
        
        $block = Mage::createBlock('tpl', 'cart.view')
            ->setTemplate($cartView)
            ->assign('data', $this->_data)
            ->assign('wishlistActive', Mage::getConfig()->getModule('Mage_Customer')->is('wishlistActive'));
            
        Mage::getBlock('content')->append($block);
        
        $this->renderLayout();
    }
    
    function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $productId = $intFilter->filter($this->getRequest()->getPost('product_id'));
        $qty = $intFilter->filter($this->getRequest()->getPost('qty', 1));

        $quote = $this->_data['quote'];

        $product = Mage::getModel('catalog', 'product')->load($productId);
        $quote->addProduct($product->setQty($qty));
        
        $quoteSession = Mage::getSingleton('checkout', 'session');
        $quote->save();
        
        Mage::getSingleton('checkout', 'session')->setQuoteId($quote->getQuoteId());
        
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
    
    function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');
        
        //foreach ($cart as )
        
        $this->_data['quote']->updateItems($cart)->save();

        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
    
    function cleanAction()
    {
        
    }
    
    function estimatePostAction()
    {
        $postcode = $this->getRequest()->getPost('estimate_postcode');
        $this->_data['quote']->setEstimatePostcode($postcode);
        $this->_data['quote']->estimateShippingMethods();
        $this->_data['quote']->save();
        
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
    
    function estimateUpdatePostAction()
    {
        $code = $this->getRequest()->getPost('estimate_method');
        $this->_data['quote']->setShippingMethod($code)->save();
        
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
    
    function couponPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $couponCode = '';
        } else {
            $couponCode = $this->getRequest()->getPost('coupon_code');
        }
        
        $this->_data['quote']->setCouponCode($couponCode)->collectTotals()->save();
        
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
    
    public function giftCertPostAction()
    {
        if ($this->getRequest()->getPost('do')==__('Clear')) {
            $giftCode = '';
        } else {
            $giftCode = $this->getRequest()->getPost('giftcert_code');
        }
        
        $this->_data['quote']->setGiftcertCode($giftCode)->collectTotals()->save();
        
        $this->_redirect(Mage::getUrl('checkout', array('controller'=>'cart')));
    }
}