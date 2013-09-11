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
namespace Magento\PaypalUk\Model;

class Pro extends \Magento\Paypal\Model\Pro
{
    /**
     * Api model type
     *
     * @var string
     */
    protected $_apiType = '\Magento\PaypalUk\Model\Api\Nvp';

    /**
     * Config model type
     *
     * @var string
     */
    protected $_configType = '\Magento\Paypal\Model\Config';

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
                ->getAdditionalInformation(\Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID);
        }
        return $payment->getParentTransactionId();
    }

    /**
     * Import capture results to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     */
    protected function _importCaptureResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(false)
            ->setTransactionAdditionalInfo(
                \Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId()
        );
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $api->getTransactionId())
        );
        \Mage::getModel('Magento\Paypal\Model\Info')->importToPayment($api, $payment);
    }

    /**
     * Fetch transaction details info method does not exists in PaypalUK
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $transactionId
     * @throws \Magento\Core\Exception
     * @return void
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\Info $payment, $transactionId)
    {
        \Mage::throwException(
            __('Fetch transaction details method does not exists in PaypalUK')
        );
    }

    /**
     * Import refund results to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     * @param bool $canRefundMore
     */
    protected function _importRefundResultToPayment($api, $payment, $canRefundMore)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())
            ->setIsTransactionClosed(1) // refund initiated by merchant
            ->setShouldCloseParentTransaction(!$canRefundMore)
            ->setTransactionAdditionalInfo(
                \Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId()
        );
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $api->getTransactionId())
        );
        \Mage::getModel('Magento\Paypal\Model\Info')->importToPayment($api, $payment);
    }
}
