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
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Order extends Mage_Core_Model_Abstract
{
    protected $_addresses;

    protected $_items;

    protected $_payments;

    protected $_statusHistory;

    protected $_orderCurrency = null;

    protected function _construct()
    {
        $this->_init('sales/order');
    }

/*********************** ORDER ***************************/

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function initNewOrder()
    {
        $this->setRemoteIp(Mage::registry('controller')->getRequest()->getServer('REMOTE_ADDR'));
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function validate()
    {
        $this->setErrors(array());

        $this->processPayments();

        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendNewOrderEmail()
    {
    	Mage::getModel('core/email_template')
    		->sendTransactional(
    		  Mage::getStoreConfig('sales/new_order/email_template'),
    		  Mage::getStoreConfig('sales/new_order/email_identity'),
    		  $this->getEmail(),
    		  $this->getName(),
    		  array('order'=>$this, 'billing'=>$this->getBillingAddress()));
    	return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function sendOrderUpdateEmail()
    {
    	Mage::getModel('core/email_template')
    		->sendTransactional(
    		  Mage::getStoreConfig('sales/order_update/email_template'),
    		  Mage::getStoreConfig('sales/order_update/email_identity'),
    		  $this->getEmail(),
    		  $this->getName(),
    		  array('order'=>$this, 'billing'=>$this->getBillingAddress()));
    	return $this;
    }



    protected function _beforeSave()
    {
    	Mage::dispatchEvent('beforeSaveOrder', array('order'=>$this));
    	parent::_beforeSave();
    }

/*********************** QUOTES ***************************/

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function createFromQuoteAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $quote = $address->getQuote();

        $this->initNewOrder()
            ->importQuoteAttributes($quote)
            ->importQuoteAddressAttributes($address)
            ->calcTotalDue();

        $billing = Mage::getModel('sales/order_address')
            ->importQuoteAddress($quote->getBillingAddress());
        $this->setBillingAddress($billing);

        $shipping = Mage::getModel('sales/order_address')
            ->importQuoteAddress($address);
        $this->setShippingAddress($shipping);

        #if (!$quote->getIsMultiPayment()) {
        #}
        $payment = Mage::getModel('sales/order_payment')
            ->importQuotePayment($quote->getPayment())
            ->setAmount($this->getTotalDue());
            
        $this->setPayment($payment);

        foreach ($address->getAllItems() as $addressItem) {
        	$item = Mage::getModel('sales/order_item');
        	if ($addressItem instanceof Mage_Sales_Model_Quote_Item) {
        	    /* @var $item Mage_Sales_Model_Order_Item */
                $item->importQuoteItem($addressItem);
	            $this->addItem($item);
        	} elseif ($addressItem instanceof Mage_Sales_Model_Quote_Address_Item) {
        	    /* @var $item Mage_Sales_Model_Order_Item */
        		$item->importQuoteAddressItem($addressItem);
	            $this->addItem($item);
        	}
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function importQuoteAttributes(Mage_Sales_Model_Quote $quote)
    {
        $this
            ->setCustomerId($quote->getCustomerId())
            ->setQuoteId($quote->getId())
            ->setCouponCode($quote->getCouponCode())
            ->setGiftcertCode($quote->getGiftcertCode())
            ->setBaseCurrencyCode($quote->getBaseCurrencyCode())
            ->setStoreCurrencyCode($quote->getStoreCurrencyCode())
            ->setOrderCurrencyCode($quote->getQuoteCurrencyCode())
            ->setStoreToBaseRate($quote->getStoreToBaseRate())
            ->setStoreToOrderRate($quote->getStoreToQuoteRate())
            ->setIsVirtual($quote->getIsVirtual())
            ->setIsMultiPayment($quote->getIsMultiPayment())
            ->setCustomerNotes($quote->getCustomerNotes())
        ;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function importQuoteAddressAttributes(Mage_Sales_Model_Quote_Address $address)
    {
        $this
            ->setWeight($address->getWeight())
            ->setShippingMethod($address->getShippingMethod())
            ->setShippingDescription($address->getShippingDescription())
            ->setSubtotal($address->getSubtotal())
            ->setTaxAmount($address->getTaxAmount())
            ->setShippingAmount($address->getShippingAmount())
            ->setGiftcertAmount($address->getGiftcertAmount())
            ->setCustbalanceAmount($address->getCustbalanceAmount())
            ->setGrandTotal($address->getGrandTotal());
        ;
        return $this;
    }

    public function getSourceQuote()
    {
        $quote = Mage::getModel('sales/quote')->load($this->getQuoteId());
        return $quote;
    }

    public function getSourceQuoteAddress()
    {
        $address = Mage::getModel('sales/quote_address')->load($this->getQuoteAddressId());
        return $address;
    }

/*********************** ADDRESSES ***************************/

    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = Mage::getResourceModel('sales/order_address_collection');

            if ($this->getId()) {
                $this->_addresses
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_addresses as $address) {
                    $address->setOrder($this);
                }
            }
        }

        return $this->_addresses;
    }

    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function addAddress(Mage_Sales_Model_Order_Address $address)
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
        }
        return $this;
    }

    public function setBillingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }

    public function setShippingAddress(Mage_Sales_Model_Order_Address $address)
    {
        $old = $this->getShippingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }

/*********************** ITEMS ***************************/

    public function getItemsCollection()
    {
        if (is_null($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_item_collection');

            if ($this->getId()) {
                $this->_items
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        return $this->_items;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function addItem(Mage_Sales_Model_Order_Item $item)
    {
        $item->setOrder($this)->setParentId($this->getId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

/*********************** PAYMENTS ***************************/

    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = Mage::getResourceModel('sales/order_payment_collection');

            if ($this->getId()) {
                $this->_payments
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_payments as $payment) {
                    $payment->setOrder($this);
                }
            }
        }
        return $this->_payments;
    }

    public function getAllPayments()
    {
        $payments = array();
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                $payments[] =  $payment;
            }
        }
        return $payments;
    }

    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        return false;
    }

    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setOrder($this)->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
        }
        return $this;
    }

    public function setPayment(Mage_Sales_Model_Order_Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);

        return $payment;
    }

    public function processPayments()
    {
        $method = $this->getPayment()->getMethod();

        if (!($modelName = Mage::getStoreConfig('payment/'.$method.'/model'))
            ||!($model = Mage::getModel($modelName))) {
            return $this;
        }

        $this->setDocument($this->getOrder());

        $model->onOrderValidate($this->getPayment());

        if ($this->getPayment()->getStatus()!=='APPROVED') {
            $errors = $this->getErrors();
            $errors[] = $this->getPayment()->getStatusDescription();
            $this->setErrors($errors);
        }

        return $this;
    }

/*********************** STATUSES ***************************/

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Entity_Order_Status_History_Collection
     */
    public function getStatusHistoryCollection()
    {
        if (is_null($this->_statusHistory)) {
            $this->_statusHistory = Mage::getResourceModel('sales/order_status_history_collection');

            if ($this->getId()) {
                $this->_statusHistory
                    ->addAttributeToSelect('*')
                    ->setOrderFilter($this->getId())
                    ->load();
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getAllStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComments() && $status->getIsCustomerNotified()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }

    public function addStatusHistory(Mage_Sales_Model_Order_Status_History $status)
    {
        $status->setOrder($this)->setParentId($this->getId())->setStoreId($this->getStoreId());
        $this->setOrderStatusId($status->getOrderStatusId());
        if (!$status->getId()) {
            $this->getStatusHistoryCollection()->addItem($status);
        }
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int $statusId
     * @param string $comments
     * @param boolean $is_customer_notified
     * @return Mage_Sales_Model_Order
     */
    public function addStatus($statusId, $comments='', $isCustomerNotified=false)
    {
        $status = Mage::getModel('sales/order_status_history')
            ->setOrderStatusId($statusId)
            ->setComments($comments)
            ->setIsCustomerNotified($isCustomerNotified);
        $this->addStatusHistory($status);

        if (4 == $statusId) {
            // Canceled
            $this->cancel();
        }
        return $this;
    }

    public function setInitialStatus()
    {
        $statusId = 1;
        $this->addStatus($statusId);
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getRealOrderId()
    {
        return $this->getIncrementId();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Directory_Model_Currency
     */
    public function getOrderCurrency()
    {
        if (is_null($this->_orderCurrency)) {
            $this->_orderCurrency = Mage::getModel('directory/currency')->load($this->getOrderCurrencyCode());
        }
        return $this->_orderCurrency;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order_Status
     */
    public function getStatus()
    {
        return Mage::getModel('sales/order_status')->load($this->getOrderStatusId());
    }

    public function _afterSave()
    {
    	Mage::dispatchEvent('sales_order_afterSave', array('order'=>$this));
    	parent::_afterSave();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function calcTotalDue()
    {
        $this->setTotalDue(max($this->getGrandTotal() - $this->getTotalPaid(), 0));
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return float
     */
    public function getTotalDue()
    {
        $this->calcTotalDue();
        return $this->getData('total_due');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function cancel()
    {
        foreach ($this->getItemsCollection() as $item) {
            $item->cancel();
        }
        return $this;
    }

}
