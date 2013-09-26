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
abstract class Magento_Checkout_Model_Type_Abstract extends Magento_Object
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Customer_Model_Session $customerSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        array $data = array()
    ) {
        parent::__construct();
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_orderFactory = $orderFactory;
    }

    /**
     * Retrieve checkout session model
     *
     * @return Magento_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        $checkout = $this->getData('checkout_session');
        if (is_null($checkout)) {
            $checkout = $this->_checkoutSession;
            $this->setData('checkout_session', $checkout);
        }
        return $checkout;
    }

    /**
     * Retrieve quote model
     *
     * @return Magento_Sales_Model_Quote
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
     * @return Magento_Customer_Model_Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Retrieve customer object
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Retrieve customer default shipping address
     *
     * @return Magento_Customer_Model_Address || false
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
     * @return Magento_Customer_Model_Address || false
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
        $order = $this->_orderFactory->create()
            ->createFromQuoteAddress($address)
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
