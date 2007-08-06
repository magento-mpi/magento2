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
class Mage_Checkout_Block_Onepage_Billing extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        $this->getCheckout()->setStepData('billing', array('label'=>__('Billing Information')));
        if ($this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('billing', 'allow', true);
        }
        parent::_construct();
    }
    
    public function getUseForShipping()
    {
        if ($this->getQuote()->getIsVirtual()) {
            return false;
        }
        return $this->getQuote()->getShippingAddress()->getSameAsBilling();
    }
    
    public function getCountries()
    {
        return Mage::getResourceModel('directory/country_collection')->loadByStore();
    }
    
    public function getMethod()
    {
        return $this->getQuote()->getCheckoutMethod();
    }

    public function getAddresses()
    {
        if ($this->isCustomerLoggedIn()) {
            return $this->getCustomer()->getAddressCollection()->getItems();
        }
        return false;
    }
    
    public function getAddress()
    {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getCountryHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('billing[country_id]')
            ->setId('billing:country_id')
            ->setTitle(__('Country'))
            ->setClass('validate-select')
            ->setValue($this->getAddress()->getCountryId())
            ->setOptions($this->getCountryCollection()->toOptionArray())
            ->getHtml();
    }
    

    public function getRegionHtmlSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('billing[region]')
            ->setId('billing:region')
            ->setTitle(__('State/Province'))
            ->setClass('required-entry validate-state input-text')
            ->setValue($this->getAddress()->getRegionId())
            ->setOptions($this->getRegionCollection()->toOptionArray())
            ->getHtml();
    }
}