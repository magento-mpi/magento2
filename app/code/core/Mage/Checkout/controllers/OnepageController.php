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
        $this->loadLayout(array('default', 'onepage'), 'onepage');
        
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if ($step!=='checkout_method') {
                    $checkout->setStepData($step, 'allow', 'false');
                }
            }
        }
        
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function statusAction()
    {
        $block = $this->getLayout()->createBlock('checkout/onepage_status');
        $this->getResponse()->setBody($block->toHtml());
    }

    public function shippingMethodAction()
    {
        $block = $this->getLayout()->createBlock('checkout/shipping_method');
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function reviewAction()
    {
        $block = $this->getLayout()->createBlock('checkout/onepage_review');
        $this->getResponse()->setBody($block->toHtml());
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
        $order->load($this->_checkout->getLastOrderId());
        if (!$order->getRealOrderId()) {
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
            $address = Mage::getModel('sales/quote_address')->addData($data);
            if ('register' == $this->getQuote()->getCheckoutMethod()) {
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
            $this->getQuote()->setBillingAddress($address);

            if ($address->getUseForShipping()) {
                $address->setSameAsBilling(1);
                $this->getQuote()->setShippingAddress($address);
                $this->getQuote()->getShippingAddress()->collectShippingRates();
            } else {
                $shipping = $this->getQuote()->getAddressByType('shipping');
                if ($shipping instanceof Varien_Object) {
                    $shipping->setSameAsBilling(0);
                }
            }
            if ($address->getCustomerPassword()) {
                $customerResource = Mage::getResourceModel('customer/customer');
                $this->getQuote()->setPasswordHash($customerResource->hashPassword($address->getCustomerPassword()));
            }
            $this->getQuote()->collectTotals()->save();
            
            $this->getCheckout()
                ->setStepData('billing', 'allow', true)
                ->setStepData('bililng', 'complete', true)
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
            $payment = Mage::getModel('sales/quote_entity_payment')->addData($data);
            $this->getQuote()->setPayment($payment)->save();
            
            $this->getCheckout()
                ->setStepData('payment', 'complete', true)
                ->setStepData('shipping', 'allow', true);
            
            $shipping = $this->getQuote()->getShippingAddress();
            if ($shipping && $shipping->getSameAsBilling()) {
                $this->getCheckout()
                    ->setStepData('shipping', 'complete', true)
                    ->setStepData('shipping_method', 'allow', true);
            }
        }
    }
    
    public function saveShippingAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping', array());
            if (empty($data)) {
                return;
            }
            $address = Mage::getModel('sales/quote_entity_address')->addData($data);
            $address->implodeStreetAddress();
            $this->getQuote()->setShippingAddress($address);
            $this->getQuote()->getShippingAddress()->collectShippingMethods();
            $this->getQuote()->save();

            $this->getCheckout()
                ->setStepData('shipping', 'complete', true)
                ->setStepData('shipping_method', 'allow', true);
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            if (empty($data)) {
                return;
            }
            $this->getQuote()->setShippingMethod($data);
            $this->getQuote()->save();
            
            $this->getCheckout()
                ->setStepData('shipping_method', 'complete', true)
                ->setStepData('review', 'allow', true);
        }

    }
    
    public function saveOrderAction()
    {
        $res = array('error'=>1);
        if ($this->getRequest()->isPost()) {
            try {
                if ('register' == $this->getQuote()->getMethod()) {
                    $customer = $this->_createCustomer();
                    $mailer = Mage::getModel('customer/email')
                        ->setTemplate('email/welcome.phtml')
                        ->setType('html')
                        ->setCustomer($customer)
                        ->send();
                    $email  = $customer->getEmail();
                    $name   = $customer->getName();
                }
                elseif ('register' == $this->getQuote()->getCheckoutMethod()) {
                    $billing = $this->getQuote()->getBillingAddress();
                    $email  = $billing->getEmail();
                    $name   = $billing->getFirstname().' '.$billing->getLastname();
                }
                else {
                    $customer = Mage::getSingleton('customer/session')->getCustomer();
                    $email  = $customer->getEmail();
                    $name   = $customer->getName();
                }
                
                $this->getQuote()->createOrder();
                $orderId = $this->getQuote()->getCreatedOrderId();
                $this->getCheckout()->clear();
                $this->getCheckout()->setLastOrderId($orderId);
                
                $mailer = Mage::getModel('core/email')
                        ->setTemplate('email/order.phtml')
                        ->setType('html')
                        ->setTemplateVar('order', $this->getQuote()->getLastCreatedOrder())
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
                // TODO: create responce for open checkout card with error
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