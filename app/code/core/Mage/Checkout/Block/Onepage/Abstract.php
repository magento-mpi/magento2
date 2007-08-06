<?php
/**
 * One page abstract child block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Checkout_Block_Onepage_Abstract extends Mage_Core_Block_Template
{
    protected $_customer;
    protected $_checkout;
    protected $_quote;
    
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
    
    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }

    public function getCountryCollection()
    {
        if (!$this->_countryCollection) {
            $this->_countryCollection = Mage::getModel('directory/country')->getResourceCollection()
                ->load();
        }
        return $this->_countryCollection;
    }
    
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('country_id')
            ->setId('country')
            ->setTitle('Country')
            ->setClass('validate-select')
            ->setValue($this->getAddress()->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->getHtml();
    }
    
    public function getRegionCollection()
    {
        if (!$this->_regionCollection) {
            $this->_regionCollection = Mage::getModel('directory/region')->getResourceCollection()
                ->addCountryFilter($this->getAddress()->getCountryId())
                ->load();
        }
        return $this->_regionCollection;
    }
    
    public function getRegionHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('region')
            ->setTitle('State/Province')
            ->setId('state')
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray())
            ->getHtml();
    }
}