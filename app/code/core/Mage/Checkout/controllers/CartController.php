<?php

class Mage_Checkout_CartController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['cart'] = Mage::getBaseUrl('', 'Mage_Checkout').'/cart/';
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout');
        
        $this->_data['params'] = $this->getRequest()->getParams();
        
        foreach (array('add','clean','updatePost','estimatePost','couponPost') as $action) {
            $this->setFlag($action, 'no-defaultLayout', true);
        }
    }
    
    function indexAction()
    {
        $quoteId = Mage::registry('Mage_Checkout')->getStateData('cart', 'quote_id');
        
        if (!$quoteId) {
            $cartView = 'cart/noItems.phtml';
        } else {
            $quote = Mage::getResourceModel('sales', 'quote');
            $quote->load($quoteId);
        
            $cartView = 'cart/view.phtml';
            $quoteItems = $quote->getItemsAsArray(array('product_name'=>'text', 'qty'=>'decimal', 'item_price'=>'decimal', 'row_total'=>'decimal'));

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
        
        $productId = $this->getRequest()->getPost('product_id');
        $productId = $intFilter->filter($productId);
        
        $qty = $this->getRequest()->getPost('qty');
        $qty = $intFilter->filter($qty);
        
        if (!$qty) {
            $qty = 1;
        }

        $product = Mage::getResourceModel('catalog', 'product')->load($productId);
        $result = Mage::getResourceModel('sales', 'quote')->addProductItem($product, $qty);
        $this->_redirect($this->_data['url']['cart']);
    }
    
    function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');
        
        foreach ($cart as )
        
        $result = Mage::getResourceModel('sales', 'quote')->updateCartItems($cart);

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