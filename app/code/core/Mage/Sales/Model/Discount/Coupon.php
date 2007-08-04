<?php

class Mage_Sales_Model_Discount_Coupon extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('sales/discount_coupon');
    }
    
    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }
    
    public function isValid()
    {
        if (!$this->getIsActive()) {
            return false;
        }

        if ($this->getFromDate() && time() < strtotime($this->getFromDate())) {
            return false;
        }

        if ($this->getToDate() && time() > strtotime($this->getToDate())) {
            return false;
        }
        
        return true;
    }
    
    public function isValidForQuoteAddressItem(Mage_Sales_Model_Quote_Item $item)
    {
        $products = $this->getLimitProducts();
        $categories = $this->getLimitCategories();
        $attributes = $this->getLimitAttributes();
        
        if (empty($products) && empty($categories) && empty($attributes)) {
            return true;
        }
        if (!empty($products)) {
            $product = explode(',', $products);
            if (array_search($item->getProductId(), $products)) {
                return true;
            }
        }
        if (!empty($categories)) {
            $categories = explode(',', $categories);
            if (array_search($item->getCategoryId(), $categories)) {
                return true;
            }
        }
        if (!empty($attributes)) {
            $attributes = explode(',', $attributes);
            if (array_search($item->getProductId(), $attributes)) {
                return true;
            }
        }

        return false;
    }
    
    public function setQuoteAddressDiscount(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->getDiscountPercent()) {
            return $this->_setQuoteAddressDiscountPercent($address);
        } elseif ($this->getDiscountFixed()) {
            return $this->_setQuoteAddressDiscountFixed($address);
        }
        return $this;
    }
        
    protected function _setQuoteAddressDiscountPercent(Mage_Sales_Model_Quote_Address $address)
    {
        $quote->setDiscountPercent($this->getDiscountPercent());
        
        foreach ($address->getAllItems() as $item) {
            if (!$this->isValidForQuoteAddressItem($item)) {
                continue;
            }
            $item->setDiscountPercent($quote->getDiscountPercent());
            $item->setDiscountAmount($item->getRowTotal() * $item->getDiscountPercent()/100);
            $quote->setDiscountAmount($quote->getDiscountAmount() + $item->getDiscountAmount());
        }
        
        return $this;
    }    
    
    protected function _setQuoteAddressDiscountFixed(Mage_Sales_Model_Quote_Address $address)
    {
        // first pass - collect valid items for discount
        $couponSubtotal = 0;
        $validItems = array();
        foreach ($address->getAllItems() as $item) {
            if ($this->isValidForQuoteAddressItem($item)) {
                $validItems[] = $item;
                $couponSubtotal += $item->getRowTotal();
            }
        }
        if ($couponSubtotal < $this->getMinSubtotal()) {
            return $this;
        }

        $quote->setDiscountAmount(min($address->getDiscountFixed(), $couponSubtotal));
        
        $quote->setDiscountPercent($address->getDiscountAmount() / $couponSubtotal * 100);
        
        // second pass - set calculated percentages for items
        foreach ($validItems as $item) {
            $item->setDiscountPercent($address->getDiscountPercent());
            $item->setDiscountAmount($item->getRowTotal() * $item->getDiscountPercent() / 100);
        }
        
        return $this;
    }
}