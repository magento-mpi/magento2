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
    public function getPrimaryShippingAddress()
    {
        $address = Mage::getModel('customer/customer')
            ->load(Mage::getSingleton('customer/session')->getCustomerId())
            ->getPrimaryShippingAddress();

        if( $address instanceof Varien_Object ) {
            $this->setData($address->getData());
            return $this->toString(Mage::getModel('customer/address')->getHtmlFormat());
        } else {
            return __('Please, add new address');
        }
    }

    public function getPrimaryBillingAddress()
    {
        $address = Mage::getModel('customer/customer')
            ->load(Mage::getSingleton('customer/session')->getCustomerId())
            ->getPrimaryBillingAddress();

        if( $address instanceof Varien_Object ) {
            $this->setData($address->getData());
            return $this->toString(Mage::getModel('customer/address')->getHtmlFormat());
        } else {
            return __('Please, add new address');
        }
    }
}
