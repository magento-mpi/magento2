<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wrapper that performs Paypal MEP and Checkout communication
 *
 */
class Mage_XmlConnect_Model_Paypal_Mep_Checkout
{
    /**
     * Keys for passthrough variables in sales/quote_payment and sales/order_payment
     * Uses additional_information as storage
     * @var string
     */
    const PAYMENT_INFO_PAYER_EMAIL = 'paypal_payer_email';
    const PAYMENT_INFO_TRANSACTION_ID = 'paypal_mep_checkout_transaction_id';

    /**
     * Payment method type
     *
     * @var unknown_type
     */
    protected $_methodType = Mage_XmlConnect_Model_Payment_Method_Paypal_Mep::MEP_METHOD_CODE;

    /**
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote = null;

    /**
     * @var Mage_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Mage_XmlConnect_Helper_Data
     */
    protected $_helper;

    /**
     * Set quote instances
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->_helper = Mage::helper('xmlconnect');
        $this->_checkoutSession = Mage::getSingleton('checkout/session');
        if (isset($params['quote']) && $params['quote'] instanceof Mage_Sales_Model_Quote) {
            $this->_quote = $params['quote'];
        } else {
            throw new Exception(Mage::helper('xmlconnect')->__('Quote instance is required.'));
        }
    }

    /**
     * Prepare quote, reserve order ID for specified quote
     * @return string
     */
    public function initCheckout()
    {
        $this->_quote->reserveOrderId()->save();

        /**
         * Reset multishipping flag before any manipulations with quote address
         * addAddress method for quote object related on this flag
         */
        if ($this->_quote->getIsMultiShipping()) {
            $this->_quote->setIsMultiShipping(false);
            $this->_quote->save();
        }

        /*
        * want to laod the correct customer information by assiging to address
        * instead of just loading from sales/quote_address
        */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer) {
            $this->_quote->assignCustomer($customer);
        }
        return $this->_quote->getReservedOrderId();
    }

    /**
     * Save shipping and billing address information to quote
     *
     * @param   array $data
     * @return  array
     */
    public function saveShipping($data)
    {
        if (empty($data)) {
            return array('error' => 1, 'message' => $this->_helper->__('Invalid data.'));
        }

        $address = $this->_quote->getBillingAddress();
        /**
         * Start hard code data
         *
         * @todo remove this hard code
         */
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $data['country_id'] = 'US';
        $data['firstname'] = $customer->getFirstname();
        $data['lastname'] = $customer->getLastname();
        /**
         * End hard code
         */

        $address->addData($data);

        $this->_ignoreAddressValidation();

        $address->implodeStreetAddress();

        if (!$this->_quote->isVirtual()) {
            $billing = clone $address;
            $billing->unsAddressId()->unsAddressType();
            $shipping = $this->_quote->getShippingAddress();
            $shippingMethod = $shipping->getShippingMethod();
            $shipping->addData($billing->getData())
                ->setSameAsBilling(1)
                ->setShippingMethod($shippingMethod)
                ->setCollectShippingRates(true);
        }

        $this->_quote->collectTotals()->save();
        return array();
    }

    /**
     * Specify quote shipping method
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => 1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        $rate = $this->_quote->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => 1, 'message' => $this->_helper->__('Invalid shipping method.'));
        }
        if (!$this->_quote->getIsVirtual() && $shippingAddress = $this->_quote->getShippingAddress()) {
            if ($shippingMethod != $shippingAddress->getShippingMethod()) {
                $this->_ignoreAddressValidation();
                $this->_quote->getShippingAddress()
                    ->setShippingMethod($shippingMethod);
                $this->_quote->collectTotals()
                    ->save();
            }
        }

        return array();
    }

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data)
    {
        if ($this->_quote->isVirtual()) {
            $this->_quote->getBillingAddress()->setPaymentMethod($this->_methodType);
        }
        else {
            $this->_quote->getShippingAddress()->setPaymentMethod($this->_methodType);
        }

        $payment = $this->_quote->getPayment();
        $data['method'] = $this->_methodType;
        $payment->importData($data);

        $payment->setAdditionalInformation(self::PAYMENT_INFO_PAYER_EMAIL, isset($data['payer']) ? $data['payer'] : null);
        $payment->setAdditionalInformation(self::PAYMENT_INFO_TRANSACTION_ID, isset($data['transaction_id']) ? $data['transaction_id'] : null);

        $this->_quote->collectTotals()->save();

        return array();
    }

    /**
     * Place the order when customer returned from paypal
     * Until this moment all quote data must be valid
     *
     * @param string $token
     * @param string $shippingMethodCode
     * @return array
     */
    public function saveOrder()
    {
        $this->_ignoreAddressValidation();

        $order = Mage::getModel('sales/service_quote', $this->_quote)->submit();
        $this->_quote->save();

        if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
            try {
                $order->sendNewOrderEmail();
            }
            catch (Exception $e) {
                Mage::logException($e);
            }
        }

        /**
         * Prepare session to success or cancellation page
         */
        $quoteId = $this->_quote->getId();
        $this->_getCheckoutSession()
            ->setLastQuoteId($quoteId)
            ->setLastSuccessQuoteId($quoteId)
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());
        return array();
    }

    /**
     * Get last order increment id by order id
     *
     * @return string
     */
    public function getLastOrderId()
    {
        $lastId  = $this->_getCheckoutSession()->getLastOrderId();
        $orderId = false;
        if ($lastId) {
            $order = Mage::getModel('sales/order');
            $order->load($lastId);
            $orderId = $order->getIncrementId();
        }
        return $orderId;
    }

    /**
     * Make sure addresses will be saved without validation errors
     */
    protected function _ignoreAddressValidation()
    {
        $this->_quote->getBillingAddress()->setShouldIgnoreValidation(true);
        if (!$this->_quote->getIsVirtual()) {
            $this->_quote->getShippingAddress()->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return $this->_checkoutSession;
    }
}
