<?php

class Mage_Checkout_Model_Type_Onepage
{
    /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Type_Onepage
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login'
                    || Mage::getSingleton('customer/session')->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param string $method
     * @return array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        $this->getCheckout()->setStepData('billing', 'allow', true);
        return array();
    }

    /**
     * Enter description here...
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     */
    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }
        if (empty($data['use_for_shipping'])) {
            $data['use_for_shipping'] = 0;
        }
        else {
            $data['use_for_shipping'] = 1;
        }

        $address = $this->getQuote()->getBillingAddress();

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
                return $res;
            }
        }

        $address->implodeStreetAddress();

        if (!empty($data['use_for_shipping'])) {
            $billing = clone $address;
            $billing->unsEntityId()->unsAddressType();
            $shipping = $this->getQuote()->getShippingAddress();
            $shipping->addData($billing->getData())
            	->setSameAsBilling(1)
            	->setCollectShippingRates(true);
            $this->getCheckout()->setStepData('shipping', 'complete', true);
        } else {
            $shipping = $this->getQuote()->getShippingAddress();
            $shipping->setSameAsBilling(0);
        }
        if ($address->getCustomerPassword()) {
            $customer = Mage::getModel('customer/customer');
            $this->getQuote()->setPasswordHash($customer->hashPassword($address->getCustomerPassword()));
        }
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('billing', 'allow', true)
            ->setStepData('billing', 'complete', true)
            ->setStepData('shipping', 'allow', true);

        return array();
    }

    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }
        $address = $this->getQuote()->getShippingAddress();

        if (!empty($customerAddressId)) {
            $customerAddress = Mage::getModel('customer/address')->load($customerAddressId);
            if ($customerAddress->getId()) {
                $address->importCustomerAddress($customerAddress);
            }
        } else {
            $address->addData($data);
        }
        $address->implodeStreetAddress();
        $address->setCollectShippingRates(true);
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        return array();
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }
        $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod)->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }

    public function savePayment($data)
    {
        if (empty($data)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }
        $payment = $this->getQuote()->getPayment();
        $payment->importPostData($data);
        $this->getQuote()->save();

        $this->getCheckout()
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function saveOrder()
    {
        $res = array('error'=>1);

        try {
            $billing = $this->getQuote()->getBillingAddress();
            $shipping = $this->getQuote()->getShippingAddress();

            switch ($this->getQuote()->getCheckoutMethod()) {
            case 'guest':
                $email  = $billing->getEmail();
                $name   = $billing->getFirstname().' '.$billing->getLastname();
                break;

            case 'register':
                $customer = $this->_createCustomer();
                $customer->sendNewAccountEmail();
                $email  = $customer->getEmail();
                $name   = $customer->getName();
                break;

            default:
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $email  = $customer->getEmail();
                $name   = $customer->getName();
                
                $billing = $this->getQuote()->getBillingAddress();
                $shipping = $this->getQuote()->getShippingAddress();
                if (!$billing->getCustomerAddressId()) {
                	$customer->addAddress($billing->exportCustomerAddress());
                }
                if (!$shipping->getCustomerAddressId() && !$shipping->getSameAsBilling()) {
                	$customer->addAddress($shipping->exportCustomerAddress());
                }
                $customer->save();
            }

            $order = Mage::getModel('sales/order');
            /* @var $order Mage_Sales_Model_Order */

            $order->createFromQuoteAddress($shipping);

            $order->validate();

            if ($order->getErrors()) {
                //TODO: handle errors (exception?)
            }

            $order->setInitialStatus();

            $order->save();

            $this->getQuote()->setIsActive(false);
            $this->getQuote()->save();

            $orderId = $order->getIncrementId();
            $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
            $this->getCheckout()->setLastOrderId($order->getId());

            $order->sendNewOrderEmail();

            $res['success'] = true;
            $res['error']   = false;
            //$res['error']   = true;
        }
        catch (Exception $e){
            // TODO: create response for open checkout card with error
            echo $e;
        }

        return $res;
    }

    protected function _createCustomer()
    {
    	$quote = $this->getQuote();
    	
        $customer = Mage::getModel('customer/customer');

        $billingEntity = $quote->getBillingAddress();
        $billing = $billingEntity->exportCustomerAddress();
        $customer->addAddress($billing);

        $shippingEntity = $quote->getShippingAddress();
        if (!$shippingEntity->getSameAsBilling()) {
            $shipping = $shippingEntity->exportCustomerAddress();
            $customer->addAddress($shipping);
        } else {
            $shipping = $billing;
        }
        //TODO: check that right primary types are assigned

        $customer->setFirstname($billing->getFirstname());
        $customer->setLastname($billing->getLastname());
        $customer->setEmail($billing->getEmail());
        $customer->setPasswordHash($quote->getPasswordHash());

        $customer->save();

        $quote->setCustomerId($customer->getId());
        $billingEntity->setCustomerId($customer->getId())->setCustomerAddressId($billing->getId());
        $shippingEntity->setCustomerId($customer->getId())->setCustomerAddressId($shipping->getId());

        Mage::getSingleton('customer/session')->loginById($customer->getId());

        return $customer;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getLastOrderId()
    {
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
        $orderId = $order->getIncrementId();
        return $orderId;
    }
}