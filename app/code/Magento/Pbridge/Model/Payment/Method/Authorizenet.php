<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Payment\Method;

class Authorizenet extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'authorizenet';

    /**
     * Array of allowed currency codes
     *
     * @var array
     */
    protected $_allowCurrencyCode = ['USD'];

    /**#@+
     * Availability options
     */
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    /**#@-*/

    /**#@+
     * Authorize.net transaction status
     */
    const TRANSACTION_STATUS_AUTHORIZED_PENDING_PAYMENT = 'authorizedPendingCapture';
    const TRANSACTION_STATUS_CAPTURED_PENDING_SETTLEMENT = 'capturedPendingSettlement';
    const TRANSACTION_STATUS_VOIDED = 'voided';
    const TRANSACTION_STATUS_DECLINED = 'declined';
    /**#@-*/

    /**
     * Retrieve dummy payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return 'pbridge_' . parent::getCode();
    }

    /**
     * Return 3D validation flag
     *
     * @return bool
     */
    public function is3dSecureEnabled()
    {
        return (bool)$this->getConfigData('centinel');
    }

    /**
     * Fetch transaction info
     *
     * @param \Magento\Payment\Model\Info $payment
     * @param string $transactionId
     * @return array
     */
    public function fetchTransactionInfo(\Magento\Payment\Model\Info $payment, $transactionId)
    {
        $result = $this->getPbridgeMethodInstance()->fetchTransactionInfo($payment, $transactionId);
        $result = new \Magento\Framework\Object($result);
        $this->importPaymentInfo($result, $payment);
        $data = $result->getRawSuccessResponseData();
        return ($data) ? $data : [];
    }

    /**
     * Get transaction status from gateway response array and change payment status to appropriate
     *
     * @param \Magento\Framework\Object $from
     * @param \Magento\Payment\Model\Info $to
     * @return $this
     */
    public function importPaymentInfo(\Magento\Framework\Object $from, \Magento\Payment\Model\Info $to)
    {
        $approvedTransactionStatuses = [
            self::TRANSACTION_STATUS_AUTHORIZED_PENDING_PAYMENT,
            self::TRANSACTION_STATUS_CAPTURED_PENDING_SETTLEMENT,
        ];

        $canceledTransactionStatuses = [
            self::TRANSACTION_STATUS_VOIDED,
            self::TRANSACTION_STATUS_DECLINED,
        ];

        $transactionStatus = $from->getTransactionStatus();

        if (in_array($transactionStatus, $approvedTransactionStatuses)) {
            $to->setIsTransactionApproved(true);
        } elseif (in_array($transactionStatus, $canceledTransactionStatuses)) {
            $to->setIsTransactionDenied(true);
        }

        return $this;
    }
}
