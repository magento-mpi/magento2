<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Website Payments Pro implementation for payment method instaces
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
namespace Magento\PbridgePaypal\Model\Payment\Method\Paypal;

use Magento\Payment\Model\MethodInterface as PaymentMethod;
use Magento\PbridgePaypal\Model\Payment\Method\PaypalDirect;

class Pro extends \Magento\Paypal\Model\Pro
{
    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance;

    /**
     * Paypal pbridge payment method
     *
     * @var PaypalDirect
     */
    protected $_pbridgePaymentMethod;

    /**
     * Payment data
     *
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentData;

    /**
     * Info factory
     *
     * @var \Magento\Paypal\Model\InfoFactory
     */
    protected $_infoFactory;

    /**
     * @param \Magento\Paypal\Model\Config\Factory $configFactory
     * @param \Magento\Paypal\Model\Api\Type\Factory $apiFactory
     * @param \Magento\Paypal\Model\InfoFactory $infoFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     */
    public function __construct(
        \Magento\Paypal\Model\Config\Factory $configFactory,
        \Magento\Paypal\Model\Api\Type\Factory $apiFactory,
        \Magento\Paypal\Model\InfoFactory $infoFactory,
        \Magento\Payment\Helper\Data $paymentData
    ) {
        $this->_infoFactory = $infoFactory;
        $this->_paymentData = $paymentData;
        parent::__construct($configFactory, $apiFactory, $infoFactory);
    }

    /**
     * Pbridge payment method setter
     *
     * @param PaypalDirect $pbridgePaymentMethod
     * @return void
     */
    public function setPaymentMethod($pbridgePaymentMethod)
    {
        $this->_pbridgePaymentMethod = $pbridgePaymentMethod;
        $this->_pbridgeMethodInstance = $this->_pbridgePaymentMethod->getPbridgeMethodInstance();
    }

    /**
     * Attempt to capture payment
     * Will return false if the payment is not supposed to be captured
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return array|bool|PaymentMethod|\Magento\Framework\Object
     */
    public function capture(\Magento\Framework\Object $payment, $amount)
    {
        $result = $this->_pbridgeMethodInstance->capture($payment, $amount);
        if (false !== $result) {
            $result = new \Magento\Framework\Object($result);
            $this->_importCaptureResultToPayment($result, $payment);
        }
        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return \Magento\Framework\Object|array|null
     */
    public function refund(\Magento\Framework\Object $payment, $amount)
    {
        $result = $this->_pbridgeMethodInstance->refund($payment, $amount);

        if ($result) {
            $result = new \Magento\Framework\Object($result);
            $result->setRefundTransactionId($result->getTransactionId());
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Framework\Object $payment
     * @return array|null
     */
    public function void(\Magento\Framework\Object $payment)
    {
        $result = $this->_pbridgeMethodInstance->void($payment);
        $this->_infoFactory->create()->importToPayment(new \Magento\Framework\Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Framework\Object $payment
     * @return void
     */
    public function cancel(\Magento\Framework\Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->_pbridgeMethodInstance->void($payment);
            $this->_infoFactory->create()->importToPayment(new \Magento\Framework\Object($result), $payment);
        }
    }

    /**
     * Perform the payment review
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $action
     * @return bool
     */
    public function reviewPayment(\Magento\Payment\Model\Info $payment, $action)
    {
        $result = [];
        switch ($action) {
            case \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_ACCEPT:
                $result = $this->_pbridgeMethodInstance->acceptPayment($payment);
                break;

            case \Magento\Paypal\Model\Pro::PAYMENT_REVIEW_DENY:
                $result = $this->_pbridgeMethodInstance->denyPayment($payment);
                break;
        }
        if (!empty($result)) {
            $result = new \Magento\Framework\Object($result);
            $this->importPaymentInfo($result, $payment);
            return true;
        }
        return false;
    }

    /**
     * Fetch transaction details info
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\Info $payment, $transactionId)
    {
        $result = $this->_pbridgeMethodInstance->fetchTransactionInfo($payment, $transactionId);
        $result = new \Magento\Framework\Object($result);
        $this->importPaymentInfo($result, $payment);
        $data = $result->getRawSuccessResponseData();
        return ($data) ? $data : [];
    }
}
