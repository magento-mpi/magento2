<?php
/**
 * Onepage checkout block
 *
 * @package    Ecom
 * @subpackage Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage extends Mage_Core_Block_Template
{
    protected $_steps;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setViewName('Mage_Checkout', 'onepage.phtml');
        
        $this->_initSteps();
    }
    
    protected function _initSteps()
    {
        $this->_steps = array();
        
        if (!Mage_Customer_Front::getCustomerId()) {
            $this->_steps['method'] = array();
            $this->_steps['method']['label'] = 'Checkout';
            $this->_steps['method']['allow'] = true;
            $this->_createMethodBlock();
        }
        else {
            Mage::registry('Mage_Checkout')->setStateData('billing', 'allow', true);
        }
        
        $this->_steps['billing'] = array();
        $this->_steps['billing']['label'] = 'Billing Information';
        $this->_createBillingBlock();

        $this->_steps['payment'] = array();
        $this->_steps['payment']['label'] = 'Payment Information';
        //$this->_steps['payment']['allow'] = true;
        $this->_createPaymentBlock();

        $this->_steps['shipping'] = array();
        $this->_steps['shipping']['label'] = 'Shipping Information';
        $this->_createShippingBlock();

        $this->_steps['shipping_method'] = array();
        $this->_steps['shipping_method']['label'] = 'Shipping Method';
        $this->_createShippingMethodBlock();

        $this->_steps['review'] = array();
        $this->_steps['review']['label'] = 'Order Review';
        $this->_createReviewBlock();
        
        foreach ($this->_steps as $stepId=>$stepInfo) {
            $stepData = Mage::registry('Mage_Checkout')->getStateData($stepId);
            if (!empty($stepData['allow'])) {
                $this->_steps[$stepId]['allow'] = true;
            }
        }
        $this->assign('steps', $this->_steps);
    }

    protected function _createMethodBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('method');
        
        $block = Mage::createBlock('tpl', 'checkout.method')
            ->setViewName('Mage_Checkout', 'onepage/method.phtml')
            ->assign('data', $data);
            
        $this->setChild('method', $block);
    }
    
    protected function _createBillingBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('billing');
        
        $block = Mage::createBlock('tpl', 'checkout.billing')
            ->setViewName('Mage_Checkout', 'onepage/billing.phtml')
            ->assign('data', $data);
        
        $address = array();
        if (Mage::registry('Mage_Checkout')->getStateData('billing', 'data')) {
            $address = Mage::registry('Mage_Checkout')->getStateData('billing', 'data');
            $address = new Varien_DataObject($address);
        }
        
        
        // assign customer addresses
        if (Mage_Customer_Front::getCustomerId()) {

            $addresses = Mage::getResourceModel('customer', 'address_collection')
                ->addFilter('customer_id', (int) Mage_Customer_Front::getCustomerId(), 'and')
                ->load()
                ->getItems();
            $block->assign('addresses', $addresses);
            if (empty($address) && $default_address_id = Mage_Customer_Front::getCustomerInfo('default_address_id')) {
                $address = Mage::getResourceModel('customer', 'address')->getRow($default_address_id);
            }
        }
        
        if (empty($address)) {
            $address = new Varien_DataObject();
        }
        
        $block->assign('address', $address);
        $this->setChild('billing', $block);
    }

    protected function _createPaymentBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('payment');
        
        $block = Mage::createBlock('tpl', 'checkout.payment')
            ->setViewName('Mage_Checkout', 'onepage/payment.phtml')
            ->assign('data', $data);
            
        $this->setChild('payment', $block);
    }

    protected function _createShippingBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('shipping');
        
        $block = Mage::createBlock('tpl', 'checkout.shipping')
            ->setViewName('Mage_Checkout', 'onepage/shipping.phtml')
            ->assign('data', $data);
        
        $address = array();
        if (Mage::registry('Mage_Checkout')->getStateData('shipping', 'data')) {
            $address = Mage::registry('Mage_Checkout')->getStateData('shipping', 'data');
            $address = new Varien_DataObject($address);
        }
        
        
        // assign customer addresses
        if (Mage_Customer_Front::getCustomerId()) {

            $addresses = Mage::getResourceModel('customer', 'address_collection')
                ->addFilter('customer_id', (int) Mage_Customer_Front::getCustomerId(), 'and')
                ->load()
                ->getItems();
            $block->assign('addresses', $addresses);
            if (empty($address) && $default_address_id = Mage_Customer_Front::getCustomerInfo('default_address_id')) {
                $address = Mage::getResourceModel('customer', 'address')->getRow($default_address_id);
            }
        }
        
        if (empty($address)) {
            $address = new Varien_DataObject();
        }
        
        $block->assign('address', $address);
        $this->setChild('shipping', $block);
    }

    protected function _createShippingMethodBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('shipping_method');
        
        $block = Mage::createBlock('tpl', 'checkout.shipping_method')
            ->setViewName('Mage_Checkout', 'onepage/shipping_method.phtml')
            ->assign('data', $data);
            
        $this->setChild('shipping_method', $block);
    }

    protected function _createReviewBlock()
    {
        $data = Mage::registry('Mage_Checkout')->getStateData('review');
        
        $block = Mage::createBlock('tpl', 'checkout.review')
            ->setViewName('Mage_Checkout', 'onepage/review.phtml')
            ->assign('data', $data);
            
        $this->setChild('review', $block);
    }
    
    public function getLastAllowStep()
    {
        $step = false;
        foreach ($this->_steps as $stepId=>$stepInfo) {
            if(!empty($stepInfo['allow'])){
                $step = $stepId;
            }
        }
        return $step;
    }
}