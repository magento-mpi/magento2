<?php
/**
 * One page abstract child block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Cart
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Checkout_Block_Cart_Abstract extends Mage_Core_Block_Template
{
    protected $_customer;
    protected $_checkout;
    protected $_quote;
    
    protected $_alnumFilter;
    protected $_priceFilter;
    protected $_qtyFilter;
    protected $_isWishlistActive;
    
    protected function _construct()
    {
        $this->_alnumFilter = new Zend_Filter_Alnum();
        $this->_priceFilter = Mage::getSingleton('core/store')->getPriceFilter();
        $this->_qtyFilter = new Varien_Filter_Sprintf('%d');
        $this->_isWishlistActive = Mage::getStoreConfig('wishlist/general/active')
            && Mage::getSingleton('customer/session')->isLoggedIn();
            
        
        parent::_construct();
    }
    
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }
    
    public function getCheckout()
    {
        if (empty($this->_checkout)) {
            $this->_checkout = Mage::getSingleton('checkout/session');
        }
        return $this->_checkout;
    }
    
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = $this->getCheckout()->getQuote();
        }
        return $this->_quote;
    }
}