<?php
/**
 * Customer address book block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Address_Book extends Mage_Core_Block_Template
{
    protected function _initChildren()
    {
        $this->getLayout()->getBlock('head')
            ->setTitle(__('Address Book'));
            
        return parent::_initChildren();
    }
    
    public function getAddAddressUrl()
    {
        return $this->getUrl('customer/address/new', array('_secure'=>true));
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/index', array('_secure'=>true));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('customer/address/delete');
    }

    public function getAddressEditUrl($address)
    {
        return $this->getUrl('customer/address/edit', array('_secure'=>true, 'id'=>$address->getId()));
    }

    public function getPrimaryBillingAddress()
    {
        return $this->getCustomer()->getPrimaryBillingAddress();
    }
    
    public function getPrimaryShippingAddress()
    {
        return $this->getCustomer()->getPrimaryShippingAddress();
    }
    
    public function hasPrimaryAddress()
    {
        return $this->getPrimaryBillingAddress() || $this->getPrimaryShippingAddress();
    }
    
    public function getAdditionalAddresses()
    {
        $addresses = $this->getCustomer()->getAdditionalAddresses();
        return empty($addresses) ? false : $addresses;
    }

    public function getAddressHtml($address)
    {
    	return $address->getFormated(true);
        //return $address->toString($address->getHtmlFormat());
    }
    
    public function getCustomer()
    {
        $customer = $this->getData('customer');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }
}
