<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
class Saas_Paypal_Model_Boarding_Express_Checkout extends Mage_Paypal_Model_Express_Checkout
{
    /**
     * Api Model Type
     *
     * @var string
     */
    protected $_apiType = 'Saas_Paypal_Model_Api_Nvp';

    /**
     * Payment method type
     *
     * @var string
     */
    protected $_methodType = Saas_Paypal_Model_Boarding_Config::METHOD_EXPRESS_BOARDING;

    /**
     * Setter for customer with billing and shipping address changing ability.
     *
     * @param  Mage_Customer_Model_Customer   $customer
     * @param  Mage_Sales_Model_Quote_Address $billingAddress
     * @param  Mage_Sales_Model_Quote_Address $shippingAddress
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function setCustomerWithAddressChange($customer, $billingAddress = null, $shippingAddress = null)
    {
        if ($customer->getId()) {
            $this->_quote->setCustomer($customer);

            if (!is_null($billingAddress)) {
                $this->_quote->setBillingAddress($billingAddress);
            } else {
                $defaultBillingAddress = $customer->getDefaultBillingAddress();
                if ($defaultBillingAddress && $defaultBillingAddress->getId()) {
                    $billingAddress = Mage::getModel('Mage_Sales_Model_Quote_Address')
                        ->importCustomerAddress($defaultBillingAddress);
                    $this->_quote->setBillingAddress($billingAddress);
                }
            }

            if (is_null($shippingAddress)) {
                $defaultShippingAddress = $customer->getDefaultShippingAddress();
                if ($defaultShippingAddress && $defaultShippingAddress->getId()) {
                    $shippingAddress = Mage::getModel('Mage_Sales_Model_Quote_Address')
                    ->importCustomerAddress($defaultShippingAddress);
                } else {
                    $shippingAddress = Mage::getModel('Mage_Sales_Model_Quote_Address');
                }
            }
            $this->_quote->setShippingAddress($shippingAddress);
        }

        $this->_customerId = $customer->getId();
        return $this;
    }
}
