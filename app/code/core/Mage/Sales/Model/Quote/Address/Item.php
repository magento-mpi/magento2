<?php

class Mage_Sales_Model_Quote_Address_Item extends Mage_Core_Model_Abstract
{
    protected $_address;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
    }
    
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }
    
    public function getAddress()
    {
        return $this->_address;
    }

    public function calcRowTotal()
    {
        $this->setRowTotal($this->getPrice()*$this->getQty());
        return $this;
    }
    
    public function calcRowWeight()
    {
        $this->setRowWeight($this->getWeight()*$this->getQty());
        return $this;
    }

    public function calcTaxAmount()
    {
        $this->setTaxAmount($this->getRowTotal() * $this->getTaxPercent()/100);
        return $this;
    }

    public function importQuoteItem(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        $item = clone $quoteItem;
        $item->setQuoteItemId($item->getId());
        $qty = $item->getQty();
        
        $item->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsQty()
            ->unsParentId();
            
        $this->addData($item->getData());

        if (!$this->hasQty()) {
            $this->setQty($qty);
        }
        
        $this->setQuoteItemImported(true);
        return $this;
    }
}