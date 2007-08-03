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
    
    public function loadByCustomerId($customerId)
    {
        $quotes = $this->getResourceCollection()
            ->addAttributeToFilter('customer_id', $customerId)
            ->setPage(1,1)
            ->load();
        if (!$quotes->count()) {
            return false;
        }
        foreach ($quotes as $quote) {
            return $quote;
        }
    }
    
    /**
     * Entity resource
     * 
     * dummy function for correct zend studio autocompletion
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getResource()
    {
        return parent::getResource();
    }
    
/*********************** CART ***************************/

    public function collectTotals()
    {
        foreach ($this->getAllShippingAddresses() as $address) {
            $address->collectTotals();
        }
        return $this;
    }
    
    public function getTotals()
    {
        $totals = array();
        foreach ($this->getAllShippingAddresses() as $address) {
            $totals[$address->getId()] = $address->getTotals();
        }
        return $totals;
    }
    
    public function createOrder()
    {
        if ($this->getIsVirtual()) {
            $this->getBillingAddress()->createOrder();
        } elseif (!$this->getIsMultiShipping()) {
            $this->getShippingAddress()->createOrder();
        } else {
            foreach ($this->getAllShippingAddresses() as $address) {
                $address->createOrder();
            }
        }
        return $this;
    }
    
/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (empty($this->_addresses)) {
            $this->_addresses = Mage::getModel('sales_entity/quote_address_collection');
            
            if ($this->getId()) {
                $this->setQuoteFilter($this->getId())->load();
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
    
    public function addAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setQuote($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }
    
    public function setBillingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }
    
    public function setShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        if (!$this->getIsMultiShipping() && ($old = $this->getShippingAddress())) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }
    
    public function addShippingAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->setShippingAddress($address);
        return $this;
    }
    
/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getModel('sales_entity/quote_item_collection');
            
            if ($this->getId()) {
                $this->setQuoteFilter($this->getId())->load();
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
    
    public function hasItems()
    {
        return sizeof($this->getAllItems())>0;
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
    
    public function addItem(Mage_Sales_Model_Quote_Item $item)
    {
        $item->setQuote($this)->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    public function addProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$product->getQty()) {
            $product->setQty(1);
        }
        
        $item = null;
        if (!$product->getAsNewItem()) {
            foreach ($this->getAllItems() as $quoteItem) {
                if ($quoteItem->getProductId()==$product->getProductId()) {
                    $finalQty = $quoteItem->getQty() + $product->getQty();
                    $quoteItem->setQty($finalQty);
                    $product->setQty($finalQty);
                    $item = $quoteItem;
                    break;
                }
            }
        }
        
        if (!$item) {
            $item = Mage::getModel('sales/quote_item')
                ->importCatalogProduct($product);
        }
        
        $this->addItem($item);
        
        $this->collectTotals();
        
        return $item;
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
            $this->_payments = Mage::getModel('sales_entity/quote_payment_collection');
            
            if ($this->getId()) {
                $this->setQuoteFilter($this->getId())->load();
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
    
    public function addPayment($payment)
    {
        $payment->setQuote($this)->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }
    
    public function setPayment($newPayment)
    {
        if (!$this->getIsMultiPayment() && ($payment = $this->getPayment())) {
            $payment->addData($newPayment);
        } else {
            $payment = Mage::getModel('sales/quote_payment')
                ->setQuote($this)
                ->setParentId($this->getId());
        }
        
        $this->addPayment($payment);
        
        return $payment;
    }
}