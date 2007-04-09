<?php

class Mage_Cart_IndexController extends Mage_Core_Controller_Front_Action 
{
    protected $_data = array();
    
    protected function _construct()
    {
        $this->_data['url']['base'] = Mage::getBaseUrl();
        $this->_data['url']['cart'] = Mage::getBaseUrl('', 'Mage_Cart');
        $this->_data['url']['checkout'] = Mage::getBaseUrl('', 'Mage_Checkout');
        
        $this->_data['params'] = $this->getRequest()->getParams();
        
        foreach (array('add','clean','updatePost','estimatePost','couponPost') as $action) {
            $this->setFlag($action, 'no-defaultLayout', true);
        }
    }
    
    function indexAction()
    {
        $cart = new Mage_Cart_Cart();
        $cartData['items'] = $cart->getItems();
        
        if (empty($cartData['items'])) {
            $cartView = 'noItems.phtml';
        } else {
            $cartView = 'view.phtml';

            $itemsFilter = new Varien_Filter_Array_Grid();
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'item_price');
            $itemsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
            $cartData['items'] = $itemsFilter->filter($cartData['items']);
            
            $cartData['totals'] = $cart->getTotals()->asArray('_output');

            $totalsFilter = new Varien_Filter_Array_Grid();
            $totalsFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'value');
            $cartData['totals'] = $totalsFilter->filter($cartData['totals']);
            
            $this->_data['cart'] = $cartData;
        }        
        
        $block = Mage::createBlock('tpl', 'cart.view')
            ->setViewName('Mage_Cart', $cartView)
            ->assign('data', $this->_data);
            
        Mage::getBlock('content')->append($block);
    }
    
    function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $productId = $this->getRequest()->getPost('product_id');
        $productId = $intFilter->filter($productId);

        //$productId = $this->getRequest()->getParam('id');
        
        $result = Mage::getModel('cart', 'cart')->addProduct($productId);
        $this->_redirect($this->_data['url']['cart']);
    }
    
    function updatePostAction()
    {
        $cart = $this->getRequest()->getPost('cart');

        $result = Mage::getModel('cart', 'cart')->update($cart);

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