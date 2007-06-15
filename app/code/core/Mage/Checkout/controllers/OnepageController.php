<?php

class Mage_Checkout_OnepageController extends Mage_Core_Controller_Front_Action 
{
    
    protected $_data = array();
    protected $_checkout = null;
    protected $_quote = null;
    
    protected function _construct()
    {
        parent::_construct();

        $this->_checkout = Mage::getSingleton('checkout/session');
        $this->_quote = $this->_checkout->getQuote();
        
        if (!$this->_quote->hasItems() && $this->getRequest()->getParam('action')!='success') {
            $this->setFlag('', 'no-dispatch', true);
            $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'cart')));
        }
    }
    
    /**
     * Checkout page
     */
    public function indexAction()
    {
        $this->_checkout->setCompletedBilling(false);
        $this->_checkout->setCompletedPayment(false);
        $this->_checkout->setCompletedShipping(false);
        $this->_checkout->setCompletedShippingMethod(false);
        $this->_checkout->setAllowBilling(false);
        $this->_checkout->setAllowPayment(false);
        $this->_checkout->setAllowShipping(false);
        $this->_checkout->setAllowShippingMethod(false);
        $this->_checkout->setAllowReview(false);
        
        $this->loadLayout(array('default', 'checkout'), 'checkout');
        
        $this->renderLayout();
    }

    /**
     * Checkout status block
     */
    public function statusAction()
    {
        $statusBlock = $this->getLayout()->createBlock('checkout/onepage_status', 'root');
        $this->getResponse()->appendBody($statusBlock->toHtml());
    }

    /**
     * Shipping methos tab
     */
    public function shippingMethodAction()
    {
        $block = $this->getLayout()->createBlock('checkout/shipping_method', 'root');
        
        $this->getResponse()->appendBody($block->toHtml());
    }
    
    public function reviewAction()
    {
        $block = $this->getLayout()->createBlock('checkout/onepage_review', 'root');
        
        $this->getResponse()->appendBody($block->toHtml());
    }
    
    public function successAction()
    {
        $this->loadLayout();
        
        /*
        $customerSession = Mage::getSingleton('customer/session');
        if (!$customerSession->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'cart')));
            return;
        }
        $collection = Mage::getModel('sales_resource/order_collection')
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
            $this->getResponse()->setRedirect(Mage::getUrl('checkout', array('controller'=>'cart')));
            return;
        }
        $orderId = $order->getRealOrderId();
        
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
            $this->getResponse()->appendBody($address->toJson());
        }
    }
    
    public function saveMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $method = $this->getRequest()->getPost('method');
            if (empty($method)) {
                return;
            }

            $this->_quote->setMethod($method);
            $this->_quote->save();
            $this->_checkout->setAllowBilling(true);
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
            $address = Mage::getModel('sales/quote_entity_address')->addData($data);
            if ('register' == $this->_quote->getMethod()) {
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
            $this->_quote->setBillingAddress($address);

            if ($address->getUseForShipping()) {
                $address->setSameAsBilling(1);
                $this->_quote->setShippingAddress($address);
                $this->_quote->collectAllShippingMethods();
            } else {
                $shipping = $this->_quote->getAddressByType('shipping');
                if ($shipping instanceof Varien_Object) {
                    $shipping->setSameAsBilling(0);
                }
            }
            if ($address->getCustomerPassword()) {
                $customerResource = Mage::getModel('customer_resource/customer');
                $this->_quote->setPasswordHash($customerResource->hashPassword($address->getCustomerPassword()));
            }
            $this->_quote->save();
            
            $this->_checkout->setAllowBilling(true);
            $this->_checkout->setCompletedBilling(true);
            $this->_checkout->setAllowPayment(true);
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
            $this->_quote->setPayment($payment);
            $this->_quote->save();
            
            $this->_checkout->setCompletedPayment(true);
            $this->_checkout->setAllowShipping(true);
            
            $shipping = $this->_quote->getAddressByType('shipping');
            if ($shipping && $shipping->getSameAsBilling()) {
                $this->_checkout->setCompletedShipping(true);
                $this->_checkout->setAllowShippingMethod(true);
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
            $this->_quote->setShippingAddress($address);
            $this->_quote->collectAllShippingMethods();
            $this->_quote->save();

            $this->_checkout->setCompletedShipping(true);
            $this->_checkout->setAllowShippingMethod(true);
        }
    }
    
    public function saveShippingMethodAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost('shipping_method', '');
            if (empty($data)) {
                return;
            }
            $this->_quote->setShippingMethod($data);
            $this->_quote->save();
            
            $this->_checkout->setCompletedShippingMethod(true);
            $this->_checkout->setAllowReview(true);
        }

    }
    
    public function saveOrderAction()
    {
        $res = array('error'=>1);
        if ($this->getRequest()->isPost()) {
            try {
                if ('register' == $this->_quote->getMethod()) {
                    $customer = $this->_createCustomer();
                    $mailer = Mage::getModel('customer/email')
                        ->setTemplate('email/welcome.phtml')
                        ->setType('html')
                        ->setCustomer($customer)
                        ->send();
                    $email  = $customer->getEmail();
                    $name   = $customer->getName();
                }
                elseif ('register' == $this->_quote->getMethod()) {
                    $email  = $this->_quote->getAddressByType('billing')->getEmail();
                    $name   = $this->_quote->getAddressByType('billing')->getFirstname() . ' ' .
                        $this->_quote->getAddressByType('billing')->getLastname();
                }
                else {
                    $email  = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
                    $name   = Mage::getSingleton('customer/session')->getCustomer()->getName();
                }
                
                $this->_quote->createOrders();
                $orderId = $this->_quote->getCreatedOrderId();
                $this->_checkout->clear();
                $this->_checkout->setLastOrderId($orderId);
                
                $mailer = Mage::getModel('core/email')
                        ->setTemplate('email/order.phtml')
                        ->setType('html')
                        ->setTemplateVar('order', $this->_quote->getLastCreatedOrder())
                        ->setTemplateVar('quote', $this->_quote)
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
        
        $billingEntity = $this->_quote->getAddressByType('billing');
        $billing = Mage::getModel('customer/address');
        $billing->addData($billingEntity->getData());
        $customer->addAddress($billing);
        
        $shippingEntity = $this->_quote->getAddressByType('shipping');
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
        $customer->setPasswordHash($this->_quote->getPasswordHash());

        $customer->save();
        
        $this->_quote->setCustomerId($customer->getId());
        $billingEntity->setCustomerId($customer->getId())->setAddressId($billing->getId());
        $shippingEntity->setCustomerId($customer->getId())->setAddressId($shipping->getId());
        
        Mage::getSingleton('customer/session')->loginById($customer->getId());
        
        return $customer;
    }
}