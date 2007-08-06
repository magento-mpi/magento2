<?php

class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract 
{
    protected $_totals;
    
    protected function _construct()
    {
        $this->_totals = $this->getQuote()->getTotals();
        
        parent::_construct();
    }
    
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
        $itemsFilter->addFilter($this->_qtyFilter, 'qty');
        $itemsFilter->addFilter($this->_priceFilter, 'price');
        $itemsFilter->addFilter($this->_priceFilter, 'row_total');
        return $itemsFilter->filter($this->getQuote()->getAllItems());
    }
    
    public function getTotals()
    {
        $totalsFilter = new Varien_Filter_Object_Grid();
        $totalsFilter->addFilter($this->_priceFilter, 'value');
        return $totalsFilter->filter($this->_totals);
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
}