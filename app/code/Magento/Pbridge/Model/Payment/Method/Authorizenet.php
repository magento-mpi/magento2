<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Model\Payment\Method;

/**
 * Authoreze.Net dummy payment method model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
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
    protected $_allowCurrencyCode = array('USD');

    /**
     * Availability options
     * @var bool
     */
    protected $_isGateway = true;

    /**
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * @var bool
     */
    protected $_canCapture = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial = false;

    /**
     * @var bool
     */
    protected $_canRefund = true;

    /**
     * @var bool
     */
    protected $_canRefundInvoicePartial = true;

    /**
     * @var bool
     */
    protected $_canVoid = true;

    /**
     * @var bool
     */
    protected $_canUseInternal = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout = true;

    /**
     * @var bool
     */
    protected $_canSaveCc = false;

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
     * Authorization method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(1);
        $payment->setShouldCloseParentTransaction($response['is_transaction_closed']);
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @return $this
     */
    public function void(\Magento\Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param \Magento\Object $payment
     * @return $this
     */
    public function cancel(\Magento\Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $response = $this->getPbridgeMethodInstance()->void($payment);
            $payment->addData((array)$response);
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        return true;
    }
}
