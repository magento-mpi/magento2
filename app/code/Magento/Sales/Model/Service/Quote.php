<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Quote submit service model
 */
class Magento_Sales_Model_Service_Quote
{
    /**
     * Quote object
     *
     * @var Magento_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Quote convert object
     *
     * @var Magento_Sales_Model_Convert_Quote
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
     * Order that may be created during submission
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * If it is true, quote will be inactivate after submitting order or nominal items
     *
     * @var bool
     */
    protected $_shouldInactivateQuote = true;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Class constructor
     *
     *
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Sales_Model_Quote $quote
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Sales_Model_Quote $quote
    ) {
        $this->_eventManager = $eventManager;
        $this->_quote       = $quote;
        $this->_convertor   = Mage::getModel('Magento_Sales_Model_Convert_Quote');
    }

    /**
     * Quote convertor declaration
     *
     * @param   Magento_Sales_Model_Convert_Quote $convertor
     * @return  Magento_Sales_Model_Service_Quote
     */
    public function setConvertor(Magento_Sales_Model_Convert_Quote $convertor)
    {
        $this->_convertor = $convertor;
        return $this;
    }

    /**
     * Get assigned quote object
     *
     * @return Magento_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Specify additional order data
     *
     * @param array $data
     * @return Magento_Sales_Model_Service_Quote
     */
    public function setOrderData(array $data)
    {
        $this->_orderData = $data;
        return $this;
    }

    /**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return Magento_Sales_Model_Order
     */
    public function submitOrder()
    {
        $this->_deleteNominalItems();
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('Magento_Core_Model_Resource_Transaction');
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
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
        if (!$isVirtual) {
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($quote->getShippingAddress()));
            if ($quote->getShippingAddress()->getCustomerAddress()) {
                $order->getShippingAddress()->setCustomerAddress($quote->getShippingAddress()->getCustomerAddress());
            }
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

        $order->setQuote($quote);

        $transaction->addObject($order);
        $transaction->addCommitCallback(array($order, 'place'));
        $transaction->addCommitCallback(array($order, 'save'));

        /**
         * We can use configuration data for declare new order status
         */
        $this->_eventManager->dispatch('checkout_type_onepage_save_order', array('order'=>$order, 'quote'=>$quote));
        $this->_eventManager->dispatch('sales_model_service_quote_submit_before', array('order'=>$order, 'quote'=>$quote));
        try {
            $transaction->save();
            $this->_inactivateQuote();
            $this->_eventManager->dispatch('sales_model_service_quote_submit_success', array('order'=>$order, 'quote'=>$quote));
        } catch (Exception $e) {

            if (!Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn()) {
                // reset customer ID's on exception, because customer not saved
                $quote->getCustomer()->setId(null);
            }

            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item Magento_Sales_Model_Order_Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            $this->_eventManager->dispatch('sales_model_service_quote_submit_failure', array('order'=>$order, 'quote'=>$quote));
            throw $e;
        }
        $this->_eventManager->dispatch('sales_model_service_quote_submit_after', array('order'=>$order, 'quote'=>$quote));
        $this->_order = $order;
        return $order;
    }

    /**
     * Submit nominal items
     *
     * @return array
     */
    public function submitNominalItems()
    {
        $this->_validate();
        $this->_submitRecurringPaymentProfiles();
        $this->_inactivateQuote();
        $this->_deleteNominalItems();
    }

    /**
     * Submit all available items
     * All created items will be set to the object
     */
    public function submitAll()
    {
        // don't allow submitNominalItems() to inactivate quote
        $inactivateQuoteOld = $this->_shouldInactivateQuote;
        $this->_shouldInactivateQuote = false;
        try {
            $this->submitNominalItems();
            $this->_shouldInactivateQuote = $inactivateQuoteOld;
        } catch (Exception $e) {
            $this->_shouldInactivateQuote = $inactivateQuoteOld;
            throw $e;
        }
        // no need to submit the order if there are no normal items remained
        if (!$this->_quote->getAllVisibleItems()) {
            $this->_inactivateQuote();
            return;
        }
        $this->submitOrder();
    }

    /**
     * Return recurring payment profiles
     *
     * @return array
     */
    public function getRecurringPaymentProfiles()
    {
        return $this->_recurringPaymentProfiles;
    }

    /**
     * Get an order that may had been created during submission
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Inactivate quote
     *
     * @return Magento_Sales_Model_Service_Quote
     */
    protected function _inactivateQuote()
    {
        if ($this->_shouldInactivateQuote) {
            $this->_quote->setIsActive(false);
        }
        return $this;
    }

    /**
     * Validate quote data before converting to order
     *
     * @return Magento_Sales_Model_Service_Quote
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(
                    __('Please check the shipping address information. %1', implode(' ', $addressValidation))
                );
            }
            $method= $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                __('Please check the billing address information. %1', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(__('Please select a valid payment method.'));
        }

        return $this;
    }

    /**
     * Submit recurring payment profiles
     */
    protected function _submitRecurringPaymentProfiles()
    {
        $profiles = $this->_quote->prepareRecurringPaymentProfiles();
        foreach ($profiles as $profile) {
            if (!$profile->isValid()) {
                Mage::throwException($profile->getValidationErrors(true, true));
            }
            $profile->submit();
        }
        $this->_recurringPaymentProfiles = $profiles;
    }

    /**
     * Get rid of all nominal items
     */
    protected function _deleteNominalItems()
    {
        foreach ($this->_quote->getAllVisibleItems() as $item) {
            if ($item->isNominal()) {
                $item->isDeleted(true);
            }
        }
    }
}
