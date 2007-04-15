<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['catalog'] = Mage::getBaseUrl('', 'Mage_Catalog').'/';
        $this->_data['url']['cart'] = Mage::getBaseUrl('', 'Mage_Checkout').'/cart/';
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout').'/';
        
        $this->_data['params'] = $this->getRequest()->getParams();
       
        $this->_data['quote'] = Mage::getSingleton('checkout', 'session')->getQuote();
        
        foreach (array('add','clean','updatePost','estimatePost','couponPost') as $action) {
            $this->setFlag($action, 'no-defaultLayout', true);
        }
    }
    
    function indexAction()
    {
        $quote = $this->_data['quote'];
        
        if (!$quote->hasItems()) {
            $cartView = 'cart/noItems.phtml';
        } else {
            $cartView = 'cart/view.phtml';
            
            $itemsFilter = new Varien_Filter_Object_Grid();
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'price');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
            $cartData['items'] = $itemsFilter->filter($quote->getItems());

            $totalsFilter = new Varien_Filter_Array_Grid();
            $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
            $cartData['totals'] = $totalsFilter->filter($quote->getTotals());

            $this->_data['cart'] = $cartData;
        }        
        
        $block = Mage::createBlock('tpl', 'cart.view')
            ->setViewName('Mage_Checkout', $cartView)
            ->assign('data', $this->_data);
            
        Mage::getBlock('content')->append($block);
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
        
        $this->_redirect($this->_data['url']['cart']);
    }
    
    function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');
        
        //foreach ($cart as )
        
        $this->_data['quote']->updateItems($cart)->save();

        $this->_redirect($this->_data['url']['cart']);
    }
    
    function cleanAction()
    {
        
    }
    
    function estimatePostAction()
    {
        
        $this->_redirect($this->_data['url']['cart']);
    }
    
    function couponPostAction()
    {
        
        $this->_redirect($this->_data['url']['cart']);
    }
}