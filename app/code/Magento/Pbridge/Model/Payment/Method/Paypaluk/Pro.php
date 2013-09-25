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
class Magento_Pbridge_Model_Payment_Method_Paypaluk_Pro extends Magento_PaypalUk_Model_Pro
{
    /**
     * Payment Bridge Payment Method Instance
     *
     * @var Magento_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance;

    /**
     * Paypal pbridge payment method
     *
     * @var Magento_Pbridge_Model_Payment_Method_Paypal
     */
    protected $_pbridgePaymentMethod;

    /**
     * Payment data
     *
     * @var Magento_Payment_Helper_Data
     */
    protected $_paymentData;

    /**
     * Info factory
     *
     * @var Magento_Paypal_Model_InfoFactory
     */
    protected $_infoFactory;

    /**
     * Construct
     *
     * @param Magento_Paypal_Model_Config_Factory $configFactory
     * @param Magento_Paypal_Model_Api_Type_Factory $apiFactory
     * @param Magento_Paypal_Model_InfoFactory $infoFactory
     * @param Magento_Payment_Helper_Data $paymentData
     */
    public function __construct(
        Magento_Paypal_Model_Config_Factory $configFactory,
        Magento_Paypal_Model_Api_Type_Factory $apiFactory,
        Magento_Paypal_Model_InfoFactory $infoFactory,
        Magento_Payment_Helper_Data $paymentData
    ) {
        $this->_paymentData = $paymentData;
        parent::__construct($configFactory, $apiFactory, $infoFactory);
    }

    /**
     * Pbridge payment method setter
     *
     * @param Magento_Pbridge_Model_Payment_Method_Paypal $pbridgePaymentMethod
     */

    public function setPaymentMethod($pbridgePaymentMethod)
    {
        $this->_pbridgePaymentMethod = $pbridgePaymentMethod;
    }

    /**
     * Return Payment Bridge method instance
     *
     * @return Magento_Pbridge_Model_Payment_Method_Pbridge
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
     * @param Magento_Object $payment
     * @param float $amount
     * @return false|null
     */
    public function capture(Magento_Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (false !== $result) {
            $result = new Magento_Object($result);
            $this->_importCaptureResultToPayment($result, $payment);
        }
        return $result;
    }


    /**
     * Refund a capture transaction
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return \Magento_Object|\Magento_Payment_Model_Abstract|null
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $result = $this->getPbridgeMethodInstance()->refund($payment, $amount);

        if ($result) {
            $result = new Magento_Object($result);
            $canRefundMore = $payment->getOrder()->canCreditmemo();
            $this->_importRefundResultToPayment($result, $payment, $canRefundMore);
        }

        return $result;
    }

    /**
     * Refund a capture transaction
     *
     * @param Magento_Object $payment
     * @return \Magento_Payment_Model_Abstract|null
     */
    public function void(Magento_Object $payment)
    {
        $result = $this->getPbridgeMethodInstance()->void($payment);
        $this->_infoFactory->create()->importToPayment(new Magento_Object($result), $payment);
        return $result;
    }

    /**
     * Cancel payment
     *
     * @param Magento_Object $payment
     */
    public function cancel(Magento_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $result = $this->getPbridgeMethodInstance()->void($payment);
            $this->_infoFactory->create()->importToPayment(new Magento_Object($result), $payment);
        }
    }


    /**
     * Parent transaction id getter
     *
     * @param Magento_Object $payment
     * @return string
     */
    protected function _getParentTransactionId(Magento_Object $payment)
    {
        return $payment->getParentTransactionId();
    }


    /**
     * Import capture results to payment
     *
     * @param Magento_Paypal_Model_Api_Nvp
     * @param Magento_Sales_Model_Order_Payment
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
     * @param Magento_Paypal_Model_Api_Nvp $api
     * @param Magento_Sales_Model_Order_Payment $payment
     * @param bool $canRefundMore
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
