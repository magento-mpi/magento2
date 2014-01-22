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
 * PayPal UK Website Payments Pro implementation for payment method instaces
 * This model was created because right now PayPal Direct and PayPal Express payment methods cannot have same abstract
 */
namespace Magento\Pbridge\Model\Payment\Method\Paypaluk;

use Magento\Object;
use Magento\Sales\Model\Order\Payment;
use Magento\Paypal\Model\Api\Nvp;

class Pro extends \Magento\PaypalUk\Model\Pro
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
     * @var \Magento\Pbridge\Model\Payment\Method\Paypal
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
     * @param \Magento\Pbridge\Model\Payment\Method\Paypal $pbridgePaymentMethod
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
     * @param Object $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (false !== $result) {
            $result = new Object($result);
            $this->_importCaptureResultToPayment($result, $payment);
        }
        return $result;
    }


    /**
     * Refund a capture transaction
     *
     * @param Object $payment
     * @param float $amount
     * @return Object|array|null
     */
    public function refund(Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->refund($payment, $amount);

        if ($result) {
            $result = new Object($result);
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param Object $payment
     * @return array|null
     */
    public function void(Object $payment)
    {
        $result = $this->getPbridgeMethodInstance()->void($payment);
        $this->_infoFactory->create()->importToPayment(new Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param Object $payment
     * @return void
     */
    public function cancel(Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->getPbridgeMethodInstance()->void($payment);
            $this->_infoFactory->create()->importToPayment(new Object($result), $payment);
        }
    }


    /**
     * Parent transaction id getter
     *
     * @param Object $payment
     * @return string
     */
    protected function _getParentTransactionId(Object $payment)
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
