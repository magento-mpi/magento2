<?php

class Mage_Sales_Model_Order extends Mage_Core_Model_Abstract
{
    protected $_addresses;
    
    protected $_items;
    
    protected $_payment;
    
    protected $_statusHistory;
    
    protected function _construct()
    {
        $this->_init('sales/order');
    }
    
/*********************** ORDER ***************************/

    public function initNewOrder()
    {
        $this
            ->setRealOrderId(Mage::getResourceModel('sales/counter')->getCounter('order'))
            ->setRemoteIp(Mage::registry('controller')->getRequest()->getServer('REMOTE_ADDR'))
        ;
        return $this;
    }
    
/*********************** QUOTES ***************************/

    public function createFromQuoteAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();
        
        $this->initNewOrder()
            ->importQuoteAttributes($quote)
            ->importQuoteAddressAttributes($address);
            
        $billing = Mage::getModel('sales/order_address')
            ->importQuoteBillingAddress($quote->getBillingAddress());
        $this->setBillingAddress($billing);
        
        $shipping = Mage::getModel('sales/order_address')
            ->importQuoteShippingAddress($address);
        $this->setShippingAddress($shipping);
        
        if (!$quote->getIsMultiPayment()) {
            $payment = Mage::getModel('sales/order_payment')
                ->importQuotePayment($quote->getPayment());
        }
        $this->setPayment($payment);
                
        foreach ($quote->getAllItems() as $quoteItem) {
            $item = Mage::getModel('sales/order_item')
                ->importQuoteItem($quoteItem);
            $this->addItem($item);
        }
        
        $this->setInitialStatus();
        
        $status = $this->getPayment()->getOrderStatus();
        $order->setStatus($status);
        $statusEntity = Mage::getModel('sales/order_entity_status')
            ->setStatus($status)
            ->setCreatedAt($now);
            
        $order->validate();
        if ($order->getErrors()) {
            //TODO: handle errors (exception?)
        }
        
        return $this;
    }
    
    public function importQuoteAttributes(Mage_Sales_Model_Quote $quote)
    {
        $this
            ->setCustomerId($quote->getCustomerId())       
            ->setQuoteId($quote->getId())
            ->setCouponCode($quote->getCouponCode())
            ->setGiftcertCode($quote->getGiftcertCode())
            ->setBaseCurrencyCode($quote->getBaseCurrencyCode())
            ->setStoreCurrencyCode($quote->getStoreCurrencyCode())
            ->setCurrentCurrencyCode($quote->getCurrentCurrencyCode())
            ->setStoreToBaseRate($quote->getStoreToBaseRate())
            ->setStoreToOrderRate($quote->getStoreToQuoteRate())
            ->setIsVirtual($quote->getIsVirtual())
            ->setIsMultiPayment($quote->getIsMultiPayment())
            ->setCustomerNotes($quote->getCustomerNotes())
        ;
        return $this;
    }
    
    public function importQuoteAddressAttributes(Mage_Sales_Model_Quote_Address $address)
    {
        $this
            ->setWeight($address->getWeight())
            ->setShippingMethod($address->getShippingMethod())
            ->setShippingDescription($address->getShippingDescription())
            ->setSubtotal($address->getSubtotal())
            ->setTaxAmount($address->getTaxAmount())
            ->setShippingAmount($address->getShippingAmount())
            ->setGiftcertAmount($address->getGiftcertAmount())
            ->setCustbalanceAmount($address->getCustbalanceAmount())
            ->setGrandTotal($address->getGrandTotal())
        ;
        return $this;
    }
    
    public function getSourceQuote()
    {
        $quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
        return $quote;
    }
    
    public function getSourceQuoteAddress()
    {
        $address = Mage::getModel('sales/quote_address')->load($this->getQuoteAddressId());
        return $address;
    }
    
/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (empty($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales_entity/order_address_collection');
            
            if ($this->getId()) {
                $this->setOrderFilter($this->getId())->load();
            }
        }
        return $this->_addresses;
    }

    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing') {
                return $address;
            }
        }
        return false;
    }
    
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping') {
                return $address;
            }
        }
        return false;
    }
    
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }
    
    public function setBillingAddress(Mage_Sales_Model_Order_Address $newAddress)
    {
        $address = $this->getBillingAddress();
        if (empty($address)) {
            $address = Mage::getModel('sales/order_address')
                ->setOrder($this)
                ->setParentId($this->getId())
                ->setAddressType('billing');
            $this->getAddressesCollection()->addItem($address);
        }
        $address->addData($newAddress);
        return $address;
    }
    
    public function setShippingAddress(Mage_Sales_Model_Order_Address $newAddress)
    {
        $address = $this->getShippingAddress();
        if (empty($address)) {
            $address = Mage::getModel('sales/order_address')
                ->setOrder($this)
                ->setParentId($this->getId())
                ->setAddressType('shipping');
            $this->getAddressesCollection()->addItem($address);
        }
        $address->addData($newAddress);
        return $address;
    }
    
/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getModel('sales_entity/order_item_collection');
            
            if ($this->getId()) {
                $this->setOrderFilter($this->getId())->load();
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }    
    
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }
    
    public function addItem(Mage_Sales_Model_Order_Item $newItem)
    {
        $item = Mage::getModel('sales/order_item');
        if ($newItem instanceof Mage_Sales_Model_Quote_Item) {
            $item->importQuoteItem($newItem);
        }
        return $item;
    }
    
/*********************** PAYMENTS ***************************/

    public function getPaymentsCollection()
    {
        if (empty($this->_payments)) {
            $this->_payments = Mage::getModel('sales_entity/order_payment_collection');
            
            if ($this->getId()) {
                $this->setOrderFilter($this->getId())->load();
            }
        }
        return $this->_payments;
    }
    
    
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            return $payment;
        }
        return false;
    }
    
    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }
    
    public function setPayment(Mage_Sales_Model_Order_Payment $newPayment)
    {
        $addNewPayment = true;
        if (!$this->getIsMultiPayment() && ($payment = $this->getPayment())) {
            $addNewPayment = false;
            $payment->addData($newPayment);
        } else {
            $payment = Mage::getModel('sales/order_payment')
                ->setQuote($this)
                ->setParentId($this->getId());
        }
        
        if ($addNewPayment) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        
        return $payment;
    }
    
/*********************** STATUSES ***************************/

    public function getStatusHistoryCollection()
    {
        if (empty($this->_statusHistory)) {
            $this->_statusHistory = Mage::getModel('sales_entity/order_status_collection');
            
            if ($this->getId()) {
                $this->setOrderFilter($this->getId())->load();
            }
        }
        return $this->_statusHistory;
    }
    
    public function getAllStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$item->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }
    
    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }
    
    public function addStatus(Mage_Sales_Model_Order_Status $status)
    {
        $this->setOrderStatusId($status->getOrderStatusId());
        return $this;
    }

    public function setInitialStatus()
    {
        $statusId = 1;
        $this->addStatus($statusId);
        return $this;
    }
}