<?php
/**
 * Customer dashboard addresses section
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Customer_Block_Account_Dashboard_Address extends Mage_Core_Block_Template
{
	public function getCustomer()
	{
		return Mage::getSingleton('customer/session')->getCustomer();
	}
	
    public function getPrimaryShippingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryShippingAddress();

        if( $address instanceof Varien_Object ) {
            return $address->getFormated(true);
        } else {
            return __('You have not set a primary shipping address.');
        }
    }

    public function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if( $address instanceof Varien_Object ) {
        	return $address->getFormated(true);
        } else {
            return __('You have not set a primary billing address.');
        }
    }
    
    public function getPrimaryShippingAddressEditUrl()
    {
    	return Mage::getUrl('customer/address/edit', array('id'=>$this->getCustomer()->getDefaultShipping()));
    }
    
    public function getPrimaryBillingAddressEditUrl()
    {
    	return Mage::getUrl('customer/address/edit', array('id'=>$this->getCustomer()->getDefaultBilling()));
    }
    
    public function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }
}
