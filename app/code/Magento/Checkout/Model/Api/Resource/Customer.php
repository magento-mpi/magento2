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
 * Checkout api resource for Customer
 *
 * @category   Magento
 * @package    Magento_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model\Api\Resource;

class Customer extends \Magento\Checkout\Model\Api\Resource
{
    /**
     * Customer address types
     */
    const ADDRESS_BILLING    = \Magento\Sales\Model\Quote\Address::TYPE_BILLING;
    const ADDRESS_SHIPPING   = \Magento\Sales\Model\Quote\Address::TYPE_SHIPPING;

    /**
     * Customer checkout types
     */
     const MODE_CUSTOMER = \Magento\Checkout\Model\Type\Onepage::METHOD_CUSTOMER;
     const MODE_REGISTER = \Magento\Checkout\Model\Type\Onepage::METHOD_REGISTER;
     const MODE_GUEST    = \Magento\Checkout\Model\Type\Onepage::METHOD_GUEST;


    /**
     *
     */
    protected function _getCustomer($customerId)
    {
        /** @var $customer \Magento\Customer\Model\Customer */
        $customer = \Mage::getModel('\Magento\Customer\Model\Customer')
            ->load($customerId);
        if (!$customer->getId()) {
            $this->_fault('customer_not_exists');
        }

        return $customer;
    }

    /**
     * Get customer address by identifier
     *
     * @param   int $addressId
     * @return  \Magento\Customer\Model\Address
     */
    protected function _getCustomerAddress($addressId)
    {
        $address = \Mage::getModel('\Magento\Customer\Model\Address')->load((int)$addressId);
        if (is_null($address->getId())) {
            $this->_fault('invalid_address_id');
        }

        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    /**
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function prepareCustomerForQuote(\Magento\Sales\Model\Quote $quote)
    {
        $isNewCustomer = false;
        switch ($quote->getCheckoutMethod()) {
        case self::MODE_GUEST:
            $this->_prepareGuestQuote($quote);
            break;
        case self::MODE_REGISTER:
            $this->_prepareNewCustomerQuote($quote);
            $isNewCustomer = true;
            break;
        default:
            $this->_prepareCustomerQuote($quote);
            break;
        }

        return $isNewCustomer;
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Checkout\Model\Api\Resource\Customer
     */
    protected function _prepareGuestQuote(\Magento\Sales\Model\Quote $quote)
    {
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Checkout\Model\Api\Resource\Customer
     */
    protected function _prepareNewCustomerQuote(\Magento\Sales\Model\Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        //$customer = \Mage::getModel('\Magento\Customer\Model\Customer');
        $customer = $quote->getCustomer();
        /* @var $customer \Magento\Customer\Model\Customer */
        $customerBilling = $billing->exportCustomerAddress();
        $customer->addAddress($customerBilling);
        $billing->setCustomerAddress($customerBilling);
        $customerBilling->setIsDefaultBilling(true);
        if ($shipping && !$shipping->getSameAsBilling()) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
            $customerShipping->setIsDefaultShipping(true);
        } else {
            $customerBilling->setIsDefaultShipping(true);
        }

        \Mage::helper('Magento\Core\Helper\Data')->copyFieldset('checkout_onepage_quote', 'to_customer', $quote, $customer);
        $customer->setPassword($customer->decryptPassword($quote->getPasswordHash()));
        $customer->setPasswordHash($customer->hashPassword($customer->getPassword()));
        $quote->setCustomer($customer)
            ->setCustomerId(true);

        return $this;
    }

    /**
     * Prepare quote for customer order submit
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Checkout\Model\Api\Resource\Customer
     */
    protected function _prepareCustomerQuote(\Magento\Sales\Model\Quote $quote)
    {
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $quote->getCustomer();
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $customerBilling = $billing->exportCustomerAddress();
            $customer->addAddress($customerBilling);
            $billing->setCustomerAddress($customerBilling);
        }
        if ($shipping && ((!$shipping->getCustomerId() && !$shipping->getSameAsBilling())
            || (!$shipping->getSameAsBilling() && $shipping->getSaveInAddressBook()))) {
            $customerShipping = $shipping->exportCustomerAddress();
            $customer->addAddress($customerShipping);
            $shipping->setCustomerAddress($customerShipping);
        }

        if (isset($customerBilling) && !$customer->getDefaultBilling()) {
            $customerBilling->setIsDefaultBilling(true);
        }
        if ($shipping && isset($customerShipping) && !$customer->getDefaultShipping()) {
            $customerShipping->setIsDefaultShipping(true);
        } else if (isset($customerBilling) && !$customer->getDefaultShipping()) {
            $customerBilling->setIsDefaultShipping(true);
        }
        $quote->setCustomer($customer);

        return $this;
    }

    /**
     * Involve new customer to system
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Checkout\Model\Api\Resource\Customer
     */
    public function involveNewCustomer(\Magento\Sales\Model\Quote $quote)
    {
        $customer = $quote->getCustomer();
        if ($customer->isConfirmationRequired()) {
            $customer->sendNewAccountEmail('confirmation');
        } else {
            $customer->sendNewAccountEmail();
        }

        return $this;
    }
}
