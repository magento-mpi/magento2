<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Website Payments Pro implementation for payment method instaces
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
class Enterprise_Pbridge_Model_Payment_Method_Paypal_Pro extends Mage_Paypal_Model_Pro
{

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Paypal pbridge payment method
     * @var Enterprise_Pbridge_Model_Payment_Method_Paypal
     */
    protected $_pbridgePaymentMethod = null;

    /**
     * Pbridge payment method setter
     *
     * @param Enterprise_Pbridge_Model_Payment_Method_Paypal $pbridgePaymentMethod
     */

    public function setPaymentMethod($pbridgePaymentMethod)
    {
        $this->_pbridgePaymentMethod = $pbridgePaymentMethod;
    }

    /**
     * Return Payment Bridge method instance
     *
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = Mage::helper('payment')->getMethodInstance('pbridge');
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this->_pbridgePaymentMethod);
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (false !== $result) {
            $result = new Varien_Object($result);
            $this->_importCaptureResultToPayment($result, $payment);
        }
        return $result;
    }


    /**
     * Refund a capture transaction
     *
     * @param Varien_Object $payment
     * @param float $amount
     * @return \Mage_Payment_Model_Abstract|\Varien_Object|void
     */
    public function refund(Varien_Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->refund($payment, $amount);

        if ($result) {
            $result = new Varien_Object($result);
            $result->setRefundTransactionId($result->getTransactionId());
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param Varien_Object $payment
     */
    public function void(Varien_Object $payment)
    {
        $result = $this->getPbridgeMethodInstance()->void($payment);
        Mage::getModel('paypal/info')->importToPayment(new Varien_Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param Varien_Object $payment
     */
    public function cancel(Varien_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->getPbridgeMethodInstance()->void($payment);
            Mage::getModel('paypal/info')->importToPayment(new Varien_Object($result), $payment);
        }
    }

    /**
     * Perform the payment review
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $action
     * @return bool
     */
    public function reviewPayment(Mage_Payment_Model_Info $payment, $action)
    {
        $result = array();
        switch ($action) {
            case Mage_Paypal_Model_Pro::PAYMENT_REVIEW_ACCEPT:
                $result = $this->getPbridgeMethodInstance()->acceptPayment($payment);
                break;

            case Mage_Paypal_Model_Pro::PAYMENT_REVIEW_DENY:
                $result = $this->getPbridgeMethodInstance()->denyPayment($payment);
                break;
        }
        if (!empty($result)) {
            $result = new Varien_Object($result);
            $this->importPaymentInfo($result, $payment);
            return true;
        }
        return false;
    }

    /**
     * Fetch transaction details info
     *
     * @param Mage_Payment_Model_Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(Mage_Payment_Model_Info $payment, $transactionId)
    {
        $result = $this->getPbridgeMethodInstance()->fetchTransactionInfo($payment, $transactionId);
        $result = new Varien_Object($result);
        $this->importPaymentInfo($result, $payment);
        $data = $result->getRawSuccessResponseData();
        return ($data) ? $data : array();
    }

}
