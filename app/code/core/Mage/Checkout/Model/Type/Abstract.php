<?php
/**
 * Cehckout type abstract class
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Checkout_Model_Type_Abstract extends Varien_Object
{
    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        $checkout = $this->getData('checkout_session');
        if (is_null($checkout)) {
            $checkout = Mage::getSingleton('checkout/session');
            $this->setData('checkout_session', $checkout);
        }
        return $checkout;
    }
    
    /**
     * Retrieve quote model
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckoutSession()->getQuote();
    }
    
    /**
     * Retrieve quote items
     *
     * @return array
     */
    public function getQuoteItems()
    {
        return $this->getQuote()->getAllItems();
    }
    
    /**
     * Retrieve customer session vodel
     *
     * @return Mage_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        $customer = $this->getData('customer_session');
        if (is_null($customer)) {
            $customer = Mage::getSingleton('customer/session');
            $this->setData('customer_session', $customer);
        }
        return $customer;
    }
    
    /**
     * Retrieve customer object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }
    
    /**
     * Retrieve customer default shipping address
     *
     * @return Mage_Customer_Model_Address || false
     */
    public function getCustomerDefaultShippingAddress()
    {
        $address = $this->getData('customer_default_shipping_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultShippingAddress();
            $this->setData('customer_default_shipping_address', $address);
        }
        return $address;
    }
    
    /**
     * Retrieve customer default billing address
     *
     * @return Mage_Customer_Model_Address || false
     */
    public function getCustomerDefaultBillingAddress()
    {
        $address = $this->getData('customer_default_billing_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultBillingAddress();
            $this->setData('customer_default_billing_address', $address);
        }
        return $address;
    }
    
    public function hasQuoteItems()
    {
        return $this->getQuote()->hasItems();
    }
}
