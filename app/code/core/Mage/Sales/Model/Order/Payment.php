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

/**
 * Order payment information
 */
class Mage_Sales_Model_Order_Payment extends Mage_Payment_Model_Info
{
    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Declare order model object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Payment
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Check order payment capture action availability
     *
     * @return unknown
     */
    public function canCapture()
    {
        /**
         * @todo checking amounts
         */
        return $this->getMethodInstance()->canCapture();
    }

    public function canRefund()
    {
        return $this->getMethodInstance()->canRefund();
    }

    public function canCapturePartial()
    {
        return $this->getMethodInstance()->canCapturePartial();
    }

    /**
     * Place payment information
     *
     * This method are colling when order will be place
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function place()
    {
        $this->setAmountOrdered($this->getOrder()->getTotalDue());
        $methodInstance = $this->getMethodInstance();

        $orderState = Mage_Sales_Model_Order::STATE_NEW;
        $orderStatus= false;
        /*
        * validating payment method again
        */
        $methodInstance->validate();
        if ($action = $methodInstance->getConfigData('payment_action')) {
            /**
             * Run action declared for payment method in configuration
             */
            switch ($action) {
                case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                    $methodInstance->authorize($this, $this->getOrder()->getTotalDue());
                    $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                    break;
                case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                    $invoice = $this->_invoice();
                    $orderState = Mage_Sales_Model_Order::STATE_PROCESSING;
                    break;
                default:
                    break;
            }
        }

        /**
         * Change order status if it specified
         */
        $orderStatus = $methodInstance->getConfigData('order_status');
        $this->getOrder()->setState($orderState, $orderStatus);
        return $this;
    }

    /**
     * Capture payment
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function capture($invoice)
    {
        if (is_null($invoice)) {
            $invoice = $this->_invoice();
        }

        $this->getMethodInstance()->capture($this, $invoice->getGrandTotal());
        $this->setAmountCaptured($this->getAmountCaptured()+$invoice->getGrandTotal());
        $this->setAmountShipping($this->getAmountShipping()+$invoice->getShippingAmount());

        $invoice->setTransactionId($this->getLastTransId());
        return $this;
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     * register this invoice and capture
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    protected function _invoice()
    {
        $convertor = Mage::getModel('sales/convert_order');
        $invoice = $convertor->toInvoice($this->getOrder());
        foreach ($this->getOrder()->getAllItems() as $orderItem) {
        	$invoiceItem = $convertor->itemToInvoiceItem($orderItem)
        	   ->setQty($orderItem->getQtyToInvoice());
            $invoice->addItem($invoiceItem);
        }
        $invoice->collectTotals()
            ->register()
            ->capture();
        $this->getOrder()->addRelatedObject($invoice);
        return $invoice;
    }

    /**
     * Check order payment void availability
     *
     * @return bool
     */
    public function canVoid(Varien_Object $document)
    {
        return $this->getMethodInstance()->canVoid($document);
    }

    public function void(Varien_Object $document)
    {
        $this->getMethodInstance()->void($document);
        return $this;
    }

    public function refound($creditmemo)
    {
        $this->getMethodInstance()->refund($this, $creditmemo->getGrandTotal());
        return $this;
    }

    public function cancel()
    {
        return $this;
    }
}