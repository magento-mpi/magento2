<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Website Payments Pro implementation for payment method instaces
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
namespace Magento\Pbridge\Model\Payment\Method\Paypal;

class Pro extends \Magento\Paypal\Model\Pro
{

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Paypal pbridge payment method
     * @var \Magento\Pbridge\Model\Payment\Method\Paypal
     */
    protected $_pbridgePaymentMethod = null;

    /**
     * Pbridge payment method setter
     *
     * @param \Magento\Pbridge\Model\Payment\Method\Paypal $pbridgePaymentMethod
     */

    public function setPaymentMethod($pbridgePaymentMethod)
    {
        $this->_pbridgePaymentMethod = $pbridgePaymentMethod;
    }

    /**
     * Return Payment Bridge method instance
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = \Mage::helper('Magento\Payment\Helper\Data')->getMethodInstance('pbridge');
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this->_pbridgePaymentMethod);
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (false !== $result) {
            $result = new \Magento\Object($result);
            $this->_importCaptureResultToPayment($result, $payment);
        }
        return $result;
    }


    /**
     * Refund a capture transaction
     *
     * @param \Magento\Object $payment
     * @param float $amount
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->refund($payment, $amount);

        if ($result) {
            $result = new \Magento\Object($result);
            $result->setRefundTransactionId($result->getTransactionId());
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Object $payment
     */
    public function void(\Magento\Object $payment)
    {
        $result = $this->getPbridgeMethodInstance()->void($payment);
        \Mage::getModel('Magento\Paypal\Model\Info')->importToPayment(new \Magento\Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Object $payment
     */
    public function cancel(\Magento\Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->getPbridgeMethodInstance()->void($payment);
            \Mage::getModel('Magento\Paypal\Model\Info')->importToPayment(new \Magento\Object($result), $payment);
        }
    }
}
