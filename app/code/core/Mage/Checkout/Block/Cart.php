<?php

class Mage_Checkout_Block_Cart extends Mage_Core_Block_Template 
{
    protected $_quote;
    protected $_alnumFilter;
    protected $_priceFilter;
    protected $_qtyFilter;
    protected $_isWishlistActive;
    protected $_totals;
    
    public function _construct()
    {
        if (!$this->getQuote()->hasItems()) {
            $this->setTemplate('checkout/cart/noItems.phtml');
        } else {
            $this->setTemplate('checkout/cart/view.phtml');
        }
        
        $this->_alnumFilter = new Zend_Filter_Alnum();
        $this->_priceFilter = Mage::getSingleton('core/store')->getPriceFilter();
        $this->_qtyFilter = new Varien_Filter_Sprintf('%d');
        $this->_isWishlistActive = Mage::getStoreConfig('wishlist/general/active')
            && Mage::getSingleton('customer/session')->isLoggedIn();
        $this->_totals = $this->getQuote()->getTotals();
    }
    
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
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
    
    public function getEstimateRates()
    {
        $rates = $this->getQuote()->getShippingAddress()->getAllShippingRates();
        $ratesFilter = new Varien_Filter_Object_Grid();
        $ratesFilter->addFilter($this->_priceFilter, 'price');
        return $ratesFilter->filter($rates);
    }
    
    public function getEstimatePostcode()
    {
        return $this->getQuote()->getShippingAddress()->getPostcode();
    }    
    
    public function getEstimateMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }
    
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }
    
    public function getGiftcertCode()
    {
        return $this->getQuote()->getGiftcertCode();
    }
    
    public function isWishlistActive()
    {
        return $this->_isWishlistActive;
    }
}