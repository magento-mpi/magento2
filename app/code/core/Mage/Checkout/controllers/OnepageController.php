<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    
    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;
    
    protected function _construct()
    {
        parent::_construct();
        
        if (!$this->getQuote()->hasItems() && $this->getRequest()->getActionName()!='success') {
            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('checkout/cart');
        }
    }
    
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
    
    /**
     * Checkout page
     */
    public function indexAction()
    {
        Mage::getSingleton('customer/session')->setUrlBeforeAuthentication(
            $this->getRequest()->getRequestUri()
        );
            
        $this->loadLayout(array('default', 'onepage'), 'onepage');
        
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login' 
                    || Mage::getSingleton('customer/session')->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }
        
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function progressAction()
    {
        $this->loadLayout('onepage_progress');
        $this->renderLayout();
    }

    public function shippingMethodAction()
    {
        $this->loadLayout('onepage_shipping');
        $this->renderLayout();
    }
    
    public function reviewAction()
    {
        $this->loadLayout('onepage_review');
        $this->renderLayout();
    }
    
    public function successAction()
    {
        $this->loadLayout();
        
        /*
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeSelect('self/real_order_id')
            ->addAttributeFilter('self/customer_id', $customerSession->getCustomerId())
            ->setOrder('self/created_at', 'DESC')
            ->setPageSize(1)
            ->loadData();
        foreach ($collection as $order) {
            $orderId = $order->getRealOrderId();
        }
        */
        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if (!$order->getIncrementId()) {
            $this->_redirect('checkout/cart');
            return;
        }
        $orderId = $order->getIncrementId();
        
        $block = $this->getLayout()->createBlock('core/template', 'checkout.success')
            ->setTemplate('checkout/success.phtml')
            ->assign('orderId', $orderId);
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }

    /**
     * Address JSON
     */
    public function getAddressAction()
    {
        $addressId = $this->getRequest()->getParam('address', false);
        if ($addressId) {
            $address = Mage::getModel('customer/address')->load((int)$addressId);
            $address->explodeStreetAddress();
            if ($address->getRegionId()) {
                $address->setRegion($address->getRegionId());
            }
            $this->getResponse()->setHeader('Content-type', 'application/x-json');
            $this->getResponse()->setBody($address->toJson());
        }
    }
    
    public function saveMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            if (empty($method)) {
                return;
            }

            $this->getQuote()->setCheckoutMethod($method)->save();
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
    }

    /**
     * save checkout billing address
     */
    public function saveBillingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('billing', array());
            if (empty($data)) {
                return;
            }
            if (empty($data['use_for_shipping'])) {
                $data['use_for_shipping'] = 0;
            }
            else {
                $data['use_for_shipping'] = 1;
            }
            
            $address = $this->getQuote()->getBillingAddress();
            
            $customerAddressId = $this->getRequest()->getPost('billing_address_id', false);
            if (!empty($customerAddressId)) {
                $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
                if ($customerAddress->getId()) {
                    $address->importCustomerAddress($customerAddress);
                }
            } else {
                $address->addData($data);
            }
            if (!$this->getQuote()->getCustomerId() && 'register' == $this->getQuote()->getCheckoutMethod()) {
                $email = $address->getEmail();
                $customer = Mage::getModel('customer/customer')->loadByEmail($email);
                if ($customer->getId()) {
                    $res = array(
                        'error' => 1,
                        'message' => __('There is already a customer registered using this email')
                    );
                    $this->getResponse()->setBody(Zend_Json::encode($res));
                    return;
                }
            }
            
            $address->implodeStreetAddress();
            
            if (!empty($data['use_for_shipping'])) {
                $billing = clone $address;
                $billing->unsEntityId()->unsAddressType();
                $shipping = $this->getQuote()->getShippingAddress();
                $shipping->addData($billing->getData())->setSameAsBilling(1);
                $this->getQuote()->save();
                $shipping->collectShippingRates();
                $this->getCheckout()->setStepData('shipping', 'complete', true);
            } else {
                $shipping = $this->getQuote()->getShippingAddress();
                $shipping->setSameAsBilling(0);
            }
            if ($address->getCustomerPassword()) {
                $customer = Mage::getModel('customer/customer');
                $this->getQuote()->setPasswordHash($customer->hashPassword($address->getCustomerPassword()));
            }
            $this->getQuote()->collectTotals()->save();
            
            $this->getCheckout()
                ->setStepData('billing', 'allow', true)
                ->setStepData('billing', 'complete', true)
                ->setStepData('shipping', 'allow', true);
            $this->getResponse()->setBody('[]');
        }
    }
    
    public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            if (empty($data)) {
                return;
            }
            $address = $this->getQuote()->getShippingAddress();
            
            $customerAddressId = $this->getRequest()->getPost('shipping_address_id', false);
            if (!empty($customerAddressId)) {
                $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
                if ($customerAddress->getId()) {
                    $address->importCustomerAddress($customerAddress);
                }
            } else {
                $address->addData($data);
            }
            $address->implodeStreetAddress();
            $address->collectShippingRates();
            $this->getQuote()->save();

            $this->getCheckout()
                ->setStepData('shipping', 'complete', true)
                ->setStepData('shipping_method', 'allow', true);
                
            $this->getResponse()->setBody('[]');
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            if (empty($data)) {
                return;
            }
            $this->getQuote()->getShippingAddress()->setShippingMethod($data)->collectTotals()->save();
            
            $this->getCheckout()
                ->setStepData('shipping_method', 'complete', true)
                ->setStepData('payment', 'allow', true);
                
            $this->getResponse()->setBody('[]');
        }

    }
    
    public function savePaymentAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('payment', array());
            if (empty($data)) {
                return;
            }
            $payment = $this->getQuote()->getPayment();
            $payment->importPostData($data)->save();
            
            $this->getCheckout()
                ->setStepData('payment', 'complete', true)
                ->setStepData('review', 'allow', true);
                
            $this->getResponse()->setBody('[]');
        }
    }
    
    public function saveOrderAction()
    {
        $res = array('error'=>1);
        if ($this->getRequest()->isPost()) {
            try {
                switch ($this->getQuote()->getCheckoutMethod()) {
                case 'register':
                    $customer = $this->_createCustomer();
                    $mailer = Mage::getModel('customer/email')
                        ->setTemplate('email/welcome.phtml')
                        ->setType('html')
                        ->setCustomer($customer)
                        ->send();
                    $email  = $customer->getEmail();
                    $name   = $customer->getName();
                    break;
                    
                case 'guest':
                    $billing = $this->getQuote()->getBillingAddress();
                    $email  = $billing->getEmail();
                    $name   = $billing->getFirstname().' '.$billing->getLastname();
                    break;
                    
                default:
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $email  = $customer->getEmail();
                    $name   = $customer->getName();
                }
                
                $shipping = $this->getQuote()->getShippingAddress();
                $order = $shipping->createOrder();
                
                $order->validate();
                if ($order->getErrors()) {
                    //TODO: handle errors (exception?)
                }

                $orderId = $order->getIncrementId();
                $this->getCheckout()->clear();
                $this->getCheckout()->setLastOrderId($order->getId());
                
                $mailer = Mage::getModel('core/email')
                        ->setTemplate('email/order.phtml')
                        ->setType('html')
                        ->setTemplateVar('order', $order)
                        ->setTemplateVar('quote', $this->getQuote())
                        ->setTemplateVar('name', $name)
                        ->setToName($name)
                        ->setToEmail($email)
                        ->send();

                $res['success'] = true;
                $res['error']   = false;
                //$res['error']   = true;
            }
            catch (Exception $e){
                // TODO: create response for open checkout card with error
                echo $e;
            }
        }
        
        $this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->appendBody(Zend_Json::encode($res));
    }
    
    protected function _createCustomer()
    {
        $customer = Mage::getModel('customer/customer');
        
        $billingEntity = $this->getQuote()->getBillingAddress();
        $billing = Mage::getModel('customer/address');
        $billing->addData($billingEntity->getData());
        $customer->addAddress($billing);
        
        $shippingEntity = $this->getQuote()-getShippingAddress();
        if (!$shippingEntity->getSameAsBilling()) {
            $shipping = Mage::getModel('customer/address');
            $shipping->addData($shippingEntity->getData());
            $customer->addAddress($shipping);
        } else {
            $shipping = $billing;
        }
        //TODO: check that right primary types are assigned
        
        $customer->setFirstname($billing->getFirstname());
        $customer->setLastname($billing->getLastname());
        $customer->setEmail($billing->getEmail());
        $customer->setPasswordHash($this->getQuote()->getPasswordHash());

        $customer->save();
        
        $this->getQuote()->setCustomerId($customer->getId());
        $billingEntity->setCustomerId($customer->getId())->setCustomerAddressId($billing->getId());
        $shippingEntity->setCustomerId($customer->getId())->setCustomerAddressId($shipping->getId());
        
        Mage::getSingleton('customer/session')->loginById($customer->getId());
        
        return $customer;
    }
}