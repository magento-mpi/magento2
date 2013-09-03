<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPal Website Payments Pro (Payflow Edition) implementation for payment method instances
 * This model was created because right now PayPal Direct and PayPal Express payment
 * (Payflow Edition) methods cannot have same abstract
 */
class Magento_PaypalUk_Model_Pro extends Magento_Paypal_Model_Pro
{
    /**
     * Api model type
     *
     * @var string
     */
    protected $_apiType = 'Magento_PaypalUk_Model_Api_Nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = 'Magento_Paypal_Model_Config';

    /**
     * Payflow trx_id key in transaction info
     *
     * @var string
     */
    const TRANSPORT_PAYFLOW_TXN_ID = 'payflow_trxid';

    /**
     * Refund a capture transaction
     *
     * @param \Magento\Object $payment
     * @param float $amount
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        if ($captureTxnId = $this->_getParentTransactionId($payment)) {
            $api = $this->getApi();
            $api->setAuthorizationId($captureTxnId);
        }
        parent::refund($payment, $amount);
    }

    /**
     * Is capture request needed on this transaction
     *
     * @return true
     */
    protected function _isCaptureNeeded()
    {
        return true;
    }

    /**
     * Get payflow transaction id from parent transaction
     *
     * @param \Magento\Object $payment
     * @return string
     */
    protected function _getParentTransactionId(\Magento\Object $payment)
    {
        if ($payment->getParentTransactionId()) {
            return $payment->getTransaction($payment->getParentTransactionId())
                ->getAdditionalInformation(Magento_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID);
        }
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
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(false)
            ->setTransactionAdditionalInfo(
                Magento_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId()
        );
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $api->getTransactionId())
        );
        Mage::getModel('Magento_Paypal_Model_Info')->importToPayment($api, $payment);
    }

    /**
     * Fetch transaction details info method does not exists in PaypalUK
     *
     * @param Magento_Payment_Model_Info $payment
     * @param string $transactionId
     * @throws Magento_Core_Exception
     * @return void
     */
    public function fetchTransactionInfo(Magento_Payment_Model_Info $payment, $transactionId)
    {
        Mage::throwException(
            __('Fetch transaction details method does not exists in PaypalUK')
        );
    }

    /**
     * Import refund results to payment
     *
     * @param Magento_Paypal_Model_Api_Nvp
     * @param Magento_Sales_Model_Order_Payment
     * @param bool $canRefundMore
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(1) // refund initiated by merchant
            ->setShouldCloseParentTransaction(!$canRefundMore)
            ->setTransactionAdditionalInfo(
                Magento_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId()
        );
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $api->getTransactionId())
        );
        Mage::getModel('Magento_Paypal_Model_Info')->importToPayment($api, $payment);
    }
}
