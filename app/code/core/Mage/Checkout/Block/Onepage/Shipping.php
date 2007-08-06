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
}