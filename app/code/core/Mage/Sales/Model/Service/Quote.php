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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote submit service model
 */
class Mage_Sales_Model_Service_Quote
{
    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Quote convert object
     *
     * @var Mage_Sales_Model_Convert_Quote
     */
    protected $_convertor;

    /**
     * List of additional order attributes which will be added to order before save
     *
     * @var array
     */
    protected $_orderData = array();

    /**
     * List of recurring payment profiles that may have been generated before placing the order
     *
     * @var array
     */
    protected $_recurringPaymentProfiles = array();

    /**
     * Class constructor
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    public function __construct(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote       = $quote;
        $this->_convertor   = Mage::getModel('sales/convert_quote');
    }

    /**
     * Quote convertor declaration
     *
     * @param   Mage_Sales_Model_Convert_Quote $convertor
     * @return  Mage_Sales_Model_Service_Quote
     */
    public function setConvertor(Mage_Sales_Model_Convert_Quote $convertor)
    {
        $this->_convertor = $convertor;
        return $this;
    }

    /**
     * Get assigned quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Specify additional order data
     *
     * @param array $data
     * @return Mage_Sales_Model_Service_Quote
     */
    public function setOrderData(array $data)
    {
        $this->_orderData = $data;
        return $this;
    }

    /**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return Mage_Sales_Model_Order
     */
    public function submit()
    {
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('core/resource_transaction');
        if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        $transaction->addObject($quote);

        $quote->reserveOrderId();
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress());
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if (!$isVirtual) {
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($quote->getShippingAddress()));
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($this->_orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }
        $quote->setIsActive(false);

        $transaction->addObject($order);
        $transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        /**
         * We can use configuration data for declare new order status
         */
        Mage::dispatchEvent('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        Mage::dispatchEvent('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        try {
            $transaction->save();
            Mage::dispatchEvent('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
            $this->_submitRecurringProfiles($order);
        } catch (Exception $e) {
            Mage::dispatchEvent('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
        Mage::dispatchEvent('sales_model_service_quote_submit_after', array('order'=>$order, 'quote'=>$quote));
        return $order;
    }

    /**
     * Validate quote data before converting to order
     *
     * @return Mage_Sales_Model_Service_Quote
     */
    protected function _validate()
    {
        $helper = Mage::helper('sales');
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(
                    $helper->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                );
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException($helper->__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                $helper->__('Please check billing address information. %s', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($helper->__('Please select a valid payment method.'));
        }

        $this->_prepareRecurringPaymentProfiles();
        return $this;
    }

    /**
     * Request recurring profiles from the quote and attempt to validate them
     *
     * @throws Mage_Core_Exception
     */
    protected function _prepareRecurringPaymentProfiles()
    {
        $this->_recurringPaymentProfiles = $this->_quote->prepareRecurringPaymentProfiles();
        if ($this->_recurringPaymentProfiles) {
            foreach ($this->_recurringPaymentProfiles as $profile) {
                if (!$profile->isValid()) {
                    Mage::throwException($profile->getValidationErrors(true, true));
                }
            }
        }
    }

    /**
     * Submit prepared recurring profiles
     * Profiles and recurring order items correspond each other in a severe sequence
     *
     * @param Mage_Sales_Model_Order $order
     * @throws Mage_Core_Exception
     */
    protected function _submitRecurringProfiles(Mage_Sales_Model_Order $order)
    {
        if (!$this->_recurringPaymentProfiles) {
            return;
        }
        try {
            // shift a payment profile instance for each recurring order item
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getIsRecurring() == '1') {
                    $profile = array_shift($this->_recurringPaymentProfiles);
                    if ($profile) {
                        $profile->submit($item);
                    } else {
                        // no translation intentionally
                        throw new Exception(sprintf('No recurring profile matched for item "%s" in order #%s.',
                            $item->getSku(), $order->getIncrementId()
                        ));
                    }
                }
            }
            if ($this->_recurringPaymentProfiles) {
                // no translation intentionally
                throw new Exception(sprintf('Order #%s was placed with redundant recurring payment profiles.',
                    $order->getIncrementId()
                ));
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
