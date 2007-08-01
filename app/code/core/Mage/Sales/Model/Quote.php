<?php

class Mage_Sales_Model_Quote extends Mage_Core_Model_Abstract
{
    protected $_addresses;
    protected $_items;
    protected $_payments;
    protected $_shipping;
    
    function _construct()
    {
        $this->_init('sales/quote');
    }
    
/*********************** QUOTE ***************************/
    
    public function loadByCustomerId()
    {
        
    }
    
/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (empty($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales_entity/quote_address_collection')
                ->setQuoteFilter($this->getId())
                ->load();
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
    
    public function getAllShippingAddresses()
    {
        $addresses = array();
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                $addresses[] = $address;
            }
        }
        return $addresses;
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
    
    public function getAddressByCustomerAddressId($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getCustomerAddressId()==$addressId) {
                return $address;
            }
        }
        return false;
    }
    
    public function removeAddress($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                $address->isDeleted(true);
                break;
            }
        }
        return $this;
    }
    
    public function setBillingAddress(Varien_Object $newAddress)
    {
        $address = $this->getBillingAddress();
        if (empty($address)) {
            $address = Mage::getModel('sales/quote_address');
            $address->setAddressType('billing');
            $this->getAddressesCollection()->addItem($address);
        }
        $address->addData($newAddress);
        return $this;
    }
    
    public function setShippingAddress(Varien_Object $newAddress)
    {
        if ($newAddress instanceof Mage_Customer_Model_Address) {
            $newAddress->setCustomerAddressId($newAddress->getId());
            $newAddress->setId(null);
        }
        
        $addNewItem = true;
        if (!$this->getIsMultiShipping() && ($address = $this->getShippingAddress())) {
            $addNewItem = false;
            $address->addData($newAddress);
        } else {
            $address = Mage::getModel('sales/quote_address');
            $address->setAddressType('shipping');
        }
        
        if ($addNewItem) {
            $this->getAddressesCollection()->addItem($address);
        }
        
        return $this;
    }

/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getModel('sales_entity/quote_items_collection')
                ->setQuoteFilter($this->getId())
                ->load();
        }
        return $this->_items;
    }

    public function getItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }    
    
    public function hasItems()
    {
        return sizeof($this->getItems())>0;
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
    
    public function removeItem($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                $item->isDeleted(true);
                break;
            }
        }
        return $this;
    }
    
    public function addProduct(Varien_Object $product)
    {
        if (!$product->getQty()) {
            $product->setQty(1);
        }
        
        $itemFound = null;
        if (!$product->getAsNewItem()) {
            foreach ($this->getItems() as $item) {
                if ($item->getProductId()==$product->getProductId()) {
                    $finalQty = $item->getQty() + $product->getQty();
                    $item->setQty($finalQty);
                    $product->setQty($finalQty);
                    $itemFound = $item;
                    break;
                }
            }
        }
        
        if (!$itemFound) {
            $itemFound = Mage::getModel('sales/quote_item')
                ->addData($product->getData());
            $this->getItemsCollection()->addItem($itemFound);
        }
        
        $itemFound->setPrice($product->getFinalPrice());
        
        $this->collectTotals();
        
        return $this;
    }
    
    public function updateItems(array $itemsArr)
    {
        foreach ($itemsArr as $id=>$itemUpd) {
            if (empty($itemUpd['qty']) || !is_numeric($itemUpd['qty']) || intval($itemUpd['qty'])<=0) {
                continue;
            }
            
            $itemUpd['qty'] = (int) $itemUpd['qty'];
            
            if (!empty($itemUpd['remove'])) {
                $this->removeItem($id);
            } else {
                $item = $this->getItemById($id);
                if (!$item) {
                    continue;
                }
                if (!empty($itemUpd['wishlist'])) {
                    Mage::getModel('customer/wishlist')->setProductId($item->getProductId())->save();
                    $this->removeItem($id);
                    continue;
                }
                
                $product = Mage::getModel('catalog/product')->load($item->getProductId());
                $item->setQty($itemUpd['qty']);
                $item->setPrice($product->getFinalPrice($item->getQty()));
            }
        }
        $this->collectTotals();
        return $this;
    }
    
/*********************** PAYMENTS ***************************/

    public function getPaymentsCollection()
    {
        if (empty($this->_payments)) {
            $this->_payments = Mage::getModel('sales_entity/quote_payments_collection')
                ->setQuoteFilter($this->getId())
                ->load();
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
    
    public function setPayment($newPayment)
    {
        if ($newPayment instanceof Mage_Customer_Model_Payment) {
            $newPayment->setCustomerPaymentId($newPayment->getId());
            $newPayment->setId(null);
        }
        
        $addNewPayment = true;
        if (!$this->getIsMultiPayment() && ($payment = $this->getPayment())) {
            $addNewPayment = false;
            $payment->addData($newPayment);
        } else {
            $payment = Mage::getModel('sales/quote_payment');
        }
        
        if ($addNewPayment) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        
        return $this;
    }
    
/*********************** SHIPPING QUOTES ***************************/

    public function getShippingQuotesCollection()
    {
        if (empty($this->_shipping)) {
            $this->_shipping = Mage::getModel('sales_entity/quote_shipping_collection')
                ->setQuoteFilter($this->getId())
                ->load();
        }
        return $this->_shipping;
    }
    
    public function getShippingQuoteById($shippingId)
    {
        foreach ($this->getShippingQuotesCollection() as $shipping) {
            if ($shipping->getId()==$shippingId) {
                return $shipping;
            }
        }
        return false;
    }
}