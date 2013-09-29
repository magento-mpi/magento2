<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cehckout type abstract class
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Type;

abstract class AbstractType extends \Magento\Object
{
    /**
     * Retrieve checkout session model
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckoutSession()
    {
        $checkout = $this->getData('checkout_session');
        if (is_null($checkout)) {
            $checkout = \Mage::getSingleton('Magento\Checkout\Model\Session');
            $this->setData('checkout_session', $checkout);
        }
        return $checkout;
    }

    /**
     * Retrieve quote model
     *
     * @return \Magento\Sales\Model\Quote
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
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        $customer = $this->getData('customer_session');
        if (is_null($customer)) {
            $customer = \Mage::getSingleton('Magento\Customer\Model\Session');
            $this->setData('customer_session', $customer);
        }
        return $customer;
    }

    /**
     * Retrieve customer object
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }

    /**
     * Retrieve customer default shipping address
     *
     * @return \Magento\Customer\Model\Address || false
     */
    public function getCustomerDefaultShippingAddress()
    {
        $address = $this->getData('customer_default_shipping_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultShippingAddress();
            if (!$address) {
                foreach ($this->getCustomer()->getAddresses() as $address) {
                    if($address){
                        break;
                    }
                }
            }
            $this->setData('customer_default_shipping_address', $address);
        }
        return $address;
    }

    /**
     * Retrieve customer default billing address
     *
     * @return \Magento\Customer\Model\Address || false
     */
    public function getCustomerDefaultBillingAddress()
    {
        $address = $this->getData('customer_default_billing_address');
        if (is_null($address)) {
            $address = $this->getCustomer()->getDefaultBillingAddress();
            if (!$address) {
                foreach ($this->getCustomer()->getAddresses() as $address) {
                    if($address){
                        break;
                    }
                }
            }
            $this->setData('customer_default_billing_address', $address);
        }
        return $address;
    }

    protected function _createOrderFromAddress($address)
    {
        $order = \Mage::getModel('Magento\Sales\Model\Order')->createFromQuoteAddress($address)
            ->setCustomerId($this->getCustomer()->getId())
            ->setGlobalCurrencyCode('USD')
            ->setBaseCurrencyCode('USD')
            ->setStoreCurrencyCode('USD')
            ->setOrderCurrencyCode('USD')
            ->setStoreToBaseRate(1)
            ->setStoreToOrderRate(1);
        return $order;
    }
}
