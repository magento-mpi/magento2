<?php

class Mage_Sales_Model_Quote extends Mage_Sales_Model_Document 
{
    protected function _setDocumentProperties()
    {
        $this->_docType = 'quote';
    }
    
    public function getItems()
    {
        return $this->getEntitiesByType('item');
    }

    public function getAddressByType($type)
    {
        foreach ($this->getEntitiesByType('address') as $addr) {
            if ($addr->getAddressType()==$type) {
                return $addr;
            }
        }
        return false;
    }
    
    public function setBillingAddress(Varien_Data_Object $address)
    {
        $old = $this->getAddressByType('billing');
        if (!empty($old)) {
            $this->removeEntity($old);
        }
        $address->setAddressType('billing');
        $this->addEntity($address);
        
        return $this;
    }
    
    public function setShippingAddress(Varien_Data_Object $address)
    {
        $old = $this->getAddressByType('shipping');
        if (!empty($old)) {
            $this->removeEntity($old);
        }
        $address->setAddressType('shipping');
        $this->addEntity($address);
        return $this;
    }
    
    public function setAddress($addressType, Mage_Customer_Model_Address $address)
    {
        $existingAddress = $this->getAddressByType($addressType);
        if (empty($existingAddress)) {
            $address->setAddressType($addressType);
            $this->addEntity('address', $address);
        } else {
            $existingAddress->addData($address->getData());
        }
        return $this;
    }
    
    public function setPayment($payment)
    {
        foreach ($this->getEntitiesByType('payment') as $oldPayment) {
            $this->removeEntity($oldPayment);
        }
        $this->addEntity($payment);
        return $this;
    }
    
    public function getPayment()
    {
        $payments = $this->getEntitiesByType('payment');
        if (empty($payments)) {
            return false;
        }
        foreach ($payments as $payment) {
            return $payment;
        }
    }
    
    public function loadByCustomerId($customerId)
    {
        $quotes = Mage::getModel('sales_resource', 'quote')->getQuoteIdsByCustomerId($customerId);
        if (empty($quotes)) {
            return false;
        }
        $this->load($quotes[0]);
        return true;
    }
}