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

use Magento\Pbridge\Model\Payment\Method\Paypal;
use Magento\Payment\Model\Method\AbstractMethod;

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
     * @var Paypal
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
     * @param Paypal $pbridgePaymentMethod
     * @return void
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
            $this->_pbridgeMethodInstance = $this->_paymentData->getMethodInstance('pbridge');
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
     * @return array|bool|AbstractMethod|\Magento\Object
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
     * @return \Magento\Object|array|void
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
     * @return array|void
     */
    public function void(\Magento\Object $payment)
    {
        $result = $this->getPbridgeMethodInstance()->void($payment);
        $this->_infoFactory->create()->importToPayment(new \Magento\Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Object $payment
     * @return void
     */
    public function cancel(\Magento\Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->getPbridgeMethodInstance()->void($payment);
            $this->_infoFactory->create()->importToPayment(new \Magento\Object($result), $payment);
        }
    }
}
