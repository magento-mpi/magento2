<?php

class Mage_Sales_Model_Discount_Coupon extends Varien_Object
{
    public function loadByCode($code)
    {
        $data = Mage::getResourceModel('sales/discount_coupon')->loadByCode($code);
        $this->setData($data);
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
    
    public function isValidForQuoteItem(Mage_Sales_Model_Quote_Entity_Item $item)
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
    
    public function setQuoteDiscount(Mage_Sales_Model_Quote $quote)
    {
        if ($this->getDiscountPercent()) {
            return $this->_setQuoteDiscountPercent($quote);
        } elseif ($this->getDiscountFixed()) {
            return $this->_setQuoteDiscountFixed($quote);
        }
        return $this;
    }
        
    protected function _setQuoteDiscountPercent(Mage_Sales_Model_Quote $quote)
    {
        $quote->setDiscountPercent($this->getDiscountPercent());
        
        foreach ($quote->getEntitiesByType('item') as $item) {
            if (!$this->isValidForQuoteItem($item)) {
                continue;
            }
            $item->setDiscountPercent($quote->getDiscountPercent());
            $item->setDiscountAmount($item->getRowTotal() * $item->getDiscountPercent()/100);
            $quote->setDiscountAmount($quote->getDiscountAmount() + $item->getDiscountAmount());
        }
        
        return $this;
    }    
    
    protected function _setQuoteDiscountFixed(Mage_Sales_Model_Quote $quote)
    {
        // first pass - collect valid items for discount
        $couponSubtotal = 0;
        $validItems = array();
        foreach ($quote->getEntitiesByType('item') as $item) {
            if ($this->isValidForQuoteItem($item)) {
                $validItems[] = $item;
                $couponSubtotal += $item->getRowTotal();
            }
        }
        if ($couponSubtotal < $this->getMinSubtotal()) {
            return $this;
        }

        $quote->setDiscountAmount(min($this->getDiscountFixed(), $couponSubtotal));
        
        $quote->setDiscountPercent($quote->getDiscountAmount() / $couponSubtotal * 100);
        
        // second pass - set calculated percentages for items
        foreach ($validItems as $item) {
            $item->setDiscountPercent($quote->getDiscountPercent());
            $item->setDiscountAmount($item->getRowTotal() * $item->getDiscountPercent() / 100);
        }
        
        return $this;
    }
}