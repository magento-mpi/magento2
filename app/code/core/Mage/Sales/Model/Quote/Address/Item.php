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
        return $this;
    }

    public function calcTaxAmount()
    {
        return $this;
    }

    public function calcRowWeight()
    {
        return $this;
    }
    
    public function importQuoteItem(Mage_Sales_Model_Quote_Item $item)
    {
        $this->setQuoteItemId($item->getId())
            ->_importQuoteItemProperty($item, 'name')
            ->_importQuoteItemProperty($item, 'weight')
            ->_importQuoteItemProperty($item, 'sku')
            ->_importQuoteItemProperty($item, 'qty');
            
        $this->setQuoteItemImported(true);
        return $this;
    }
    
    protected function _importQuoteItemProperty($item, $property)
    {
        if (!$this->hasData($property)) {
            $this->setData($property, $item->getData($property));
        }
        return $this;
    }
}