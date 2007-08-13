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
            ->getPrimaryShippingAddress()
            ->getData();

        $this->setData($address);
        return $this->toString(Mage::getModel('customer/address')->getHtmlFormat());
    }

    public function getPrimaryBillingAddress()
    {
        $address = Mage::getModel('customer/customer')
            ->load(Mage::getSingleton('customer/session')->getCustomerId())
            ->getPrimaryBillingAddress()
            ->getData();

        $this->setData( $address );
        return $this->toString(Mage::getModel('customer/address')->getHtmlFormat());
    }
}
