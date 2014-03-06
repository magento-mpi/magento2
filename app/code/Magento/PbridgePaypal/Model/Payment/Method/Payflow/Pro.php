<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method\Payflow;

use Magento\Paypal\Model\Api\Nvp;
use Magento\PbridgePaypal\Model\Payment\Method\PayflowDirect;
use Magento\Sales\Model\Order\Payment;

/**
 * Payflow Pro implementation for payment method instances
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
class Pro extends \Magento\Paypal\Model\Payflow\Pro
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
     * @var PayflowDirect
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
     * Construct
     *
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
        $this->_paymentData = $paymentData;
        parent::__construct($configFactory, $apiFactory, $infoFactory);
    }

    /**
     * Pbridge payment method setter
     *
     * @param PayflowDirect $pbridgePaymentMethod
     * @return void
     */
    public function setPaymentMethod($pbridgePaymentMethod)
    {
        $this->_pbridgePaymentMethod = $pbridgePaymentMethod;
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
        $result = $this->_pbridgePaymentMethod->getPbridgeMethodInstance()->capture($payment, $amount);
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
     * @return \Magento\Object|array|null
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $result = $this->_pbridgePaymentMethod->getPbridgeMethodInstance()->refund($payment, $amount);

        if ($result) {
            $result = new \Magento\Object($result);
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Object $payment
     * @return array|null
     */
    public function void(\Magento\Object $payment)
    {
        $result = $this->_pbridgePaymentMethod->getPbridgeMethodInstance()->void($payment);
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
            $result = $this->_pbridgePaymentMethod->getPbridgeMethodInstance()->void($payment);
            $this->_infoFactory->create()->importToPayment(new \Magento\Object($result), $payment);
        }
    }

    /**
     * Parent transaction id getter
     *
     * @param \Magento\Object $payment
     * @return string
     */
    protected function _getParentTransactionId(\Magento\Object $payment)
    {
        return $payment->getParentTransactionId();
    }


    /**
     * Import capture results to payment
     *
     * @param Nvp $api
     * @param Payment $payment
     * @return void
     */
    protected function _importCaptureResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(false);
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $api->getData(self::TRANSPORT_PAYFLOW_TXN_ID))
        );
        $this->_infoFactory->create()->importToPayment($api, $payment);
    }

    /**
     * Import refund results to payment
     *
     * @param Nvp $api
     * @param Payment $payment
     * @param bool $canRefundMore
     * @return void
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getTransactionId())
                ->setIsTransactionClosed(1) // refund initiated by merchant
                ->setShouldCloseParentTransaction(!$canRefundMore)
                ->setTransactionAdditionalInfo(self::TRANSPORT_PAYFLOW_TXN_ID, $api->getPayflowTrxid());

        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getData(self::TRANSPORT_PAYFLOW_TXN_ID)));
        $this->_infoFactory->create()->importToPayment($api, $payment);
    }
}
