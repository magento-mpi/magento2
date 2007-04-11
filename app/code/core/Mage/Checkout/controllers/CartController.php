<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['cart'] = Mage::getBaseUrl('', 'Mage_Checkout').'/cart/';
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout').'/';
        
        $this->_data['params'] = $this->getRequest()->getParams();
       
        $this->_data['quote'] = Mage::getSingleton('checkout_model', 'session')->getQuote();
        
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
            
            $quoteItems = $quote->getItemsAsArray(array('product_name'=>'text', 'qty'=>'decimal', 'tier_price'=>'decimal', 'row_total'=>'decimal'));
            $itemsFilter = new Varien_Filter_Array_Grid();
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'item_price');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
            $cartData['items'] = $itemsFilter->filter($quoteItems);
            
            $totalsFilter = new Varien_Filter_Array_Grid();
            $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
            $cartData['totals'] = $totalsFilter->filter($quote->collectTotals('_output'));

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

        if (!$quote->getQuoteId()) {
            $quote->save();
            Mage::getSingleton('checkout_model', 'session')->setQuoteId($quote->getQuoteId());
        }
        
        $product = Mage::getModel('catalog', 'product')->load($productId);
        $quote->addProduct($product->setQty($qty))->save();
        
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