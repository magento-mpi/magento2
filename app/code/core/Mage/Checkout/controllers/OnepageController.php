<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    protected function _construct()
    {
        parent::_construct();
        foreach (array('status','getAddress','saveBilling','savePayment','saveShipping','saveShippingMethod') as $action) {
            $this->setFlag($action, 'no-renderLayout', true);
        }
    }
    
    public function indexAction()
    {
        $statusBlock =  Mage::createBlock('onepage_status', 'checkout.status');
            
        Mage::getBlock('left')->unsetChildren()
            ->insert($statusBlock);
            
        $block = Mage::createBlock('onepage', 'checkout.onepage');
        Mage::getBlock('content')->append($block);
        //$this->_redirect($this->_data['url']['checkout'].'/shipping');
    }
    
    public function statusAction()
    {
        $statusBlock = Mage::createBlock('onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toString());
    }
    
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = new Mage_Customer_Address((int) $addressId);
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->appendBody($address->__toJson());
        }
    }
    
    public function saveBillingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['billing']) ? $_POST['billing'] : array();
            if (!empty($data)) {
                Mage::registry('Mage_Checkout')->setStateData('billing', 'allow', true);
                Mage::registry('Mage_Checkout')->setStateData('payment', 'allow', true);
            }
            Mage::registry('Mage_Checkout')->setStateData('billing', 'data', $data);
        }
    }
    
    public function savePaymentAction()
    {
        
    }
    
    public function saveShippingAction()
    {

        if ($this->getRequest()->isPost()) {
            $data = isset($_POST['shipping']) ? $_POST['shipping'] : array();
            if (!empty($data)) {
                Mage::registry('Mage_Checkout')->setStateData('shipping', 'allow', true);
                //Mage::registry('Mage_Checkout')->setStateData('payment', 'allow', true);
            }
            Mage::registry('Mage_Checkout')->setStateData('shipping', 'data', $data);
        }
        print_r(Mage::registry('Mage_Checkout')->getStateData('shipping', 'data'));
        $shipping = new Mage_Sales_Shipping();

        $request = new Mage_Sales_Shipping_Quote_Request();

        $result = $shipping->fetchQuotes($request);
    }
    
    public function saveShippingMethodAction()
    {

    }
}
