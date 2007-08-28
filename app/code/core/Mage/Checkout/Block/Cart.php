<?php

class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract 
{
    protected $_totals;
    
    public function chooseTemplate()
    {
        if ($this->getQuote()->hasItems()) {
            $this->setTemplate($this->getCartTemplate());
        } else {
            $this->setTemplate($this->getEmptyTemplate());
        }
    }
    
    public function getItems()
    {
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter($this->_priceFilter, 'price');
        $itemsFilter->addFilter($this->_priceFilter, 'row_total');
        $items = $this->getQuote()->getAllItems();
        return $itemsFilter->filter($items);
    }
    
    public function getItemsSummaryQty()
    {
        return $this->getQuote()->getItemsSummaryQty();
    }
    
    public function getCanDoMultishipping()
    {
        return !$this->getQuote()->hasItemsWithDecimalQty();
    }
    
    public function getTotals()
    {
        $totalsFilter = new Varien_Filter_Object_Grid();
        $totalsFilter->addFilter($this->_priceFilter, 'value');
        return $totalsFilter->filter($this->getTotalsCache());
    }
    
    public function getTotalsCache()
    {
        if (empty($this->_totals)) {
            $this->_totals = $this->getQuote()->getTotals();
        }
        return $this->_totals;
    }
    
    public function getGiftcertCode()
    {
        return $this->getQuote()->getGiftcertCode();
    }
    
    public function isWishlistActive()
    {
        return $this->_isWishlistActive;
    }
    
    public function getCheckoutUrl()
    {
        return $this->getUrl('checkout/onepage', array('_secure'=>true));
    }
    
    public function getMultiShippingUrl()
    {
        return $this->getUrl('checkout/multishipping', array('_secure'=>true));
    }
    
    public function getPaypalUrl()
    {
        return $this->getUrl('checkout/paypal');
    }
    
    public function getGoogleUrl()
    {
        return $this->getUrl('checkout/google');
    }
    
    public function getItemDeleteUrl(Mage_Sales_Model_Quote_Item $item)
    {
    	return $this->getUrl('checkout/cart/delete', array('id'=>$item->getId()));
    }
    
    public function getItemUrl($item)
    {
        return $this->getHelper('checkout/item')->getItemUrl($item);
    }
    
    public function getItemImageUrl($item)
    {
        return $this->getHelper('checkout/item')->getItemImageUrl($item);
    }
    
    public function getItemName($item)
    {
        return $this->getHelper('checkout/item')->getItemName($item);
    }
    
    public function getItemDescription($item)
    {
        return $this->getHelper('checkout/item')->getItemDescription($item);
    }
    
    public function getItemQty($item)
    {
        return $this->getHelper('checkout/item')->getItemQty($item);
    }
    
    public function getItemIsInStock($item)
    {
        return $this->getHelper('checkout/item')->getItemIsInStock($item);
    }
}