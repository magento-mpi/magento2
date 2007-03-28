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
        
        $this->setFlag('add', 'no-defaultLayout', true);
        $this->setFlag('updatePost', 'no-defaultLayout', true);
        $this->setFlag('clean', 'no-defaultLayout', true);
    }
    
    function indexAction()
    {
        $cart['products'] = Mage::getModel('cart', 'cart')->getProducts();
        
        if (empty($cart['products'])) {
            $cartView = 'noItems';
        } else {
            $cartView = 'view';

            $filter = new Varien_Filter_Grid();
            $filter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
            $filter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'item_price');
            $filter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'row_total');
            $cart['products'] = $filter->filter($cart['products']);
            
            $this->_data['cart'] = $cart;
        }        
        
        $block = Mage::createBlock('tpl', 'cart.view')
            ->setViewName('Mage_Cart', $cartView)
            ->assign('data', $this->_data);
            
        Mage::getBlock('content')->append($block);
    }
    
    function addAction()
    {
        $intFilter = new Zend_Filter_Int();
        $product_Id = $this->getRequest()->getPost('product_Id');
        $product_Id = $intFilter->filter($product_Id);

        //$productId = $this->getRequest()->getParam('id');
        
        $result = Mage::getModel('cart', 'cart')->addProduct($product_Id);
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
    
}