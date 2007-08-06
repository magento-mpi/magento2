<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Shipping extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('shipping', array('label'=>__('Order Review')));
        parent::_construct();
    }
    
    public function getCountries()
    {
        return Mage::getResourceModel('directory/country_collection')->loadByStore();
    }
    
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }
    
    public function getRegions()
    {
        return Mage::getResourceModel('directory/country_collection')->getDefault(
            $this->getQuote()->getBillingAddress()->getCountryId()
        )->getRegions();
    }
    
    public function getAddresses()
    {
        $customerSession = Mage::getSingleton('customer/session');
        if ($customerSession->isLoggedIn()) {
            $customer = $customerSession->getCustomer();
            $addresses = $customer->getAddressCollection();
            return $addresses->getItems();
        }
        return false;
    }
    
    public function getAddress()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('shipping[country_id]')
            ->setId('shipping:country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select')
            ->setValue($this->getAddress()->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->getHtml();
    }
    
    public function getRegionHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('shipping[region]')
            ->setId('shipping:region')
            ->setTitle(__('State/Province'))
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray())
            ->getHtml();
    }
}