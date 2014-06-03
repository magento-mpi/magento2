<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Worldpay dummy payment method model
 *
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Payment\Method\Worldpay;

class Direct extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'worldpay_direct';

    /**
     * @var array
     */
    protected $_allowCurrencyCode = array('USD');

    /**
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
     * PSi Gate method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Framework\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Framework\Object $payment, $amount)
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
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Framework\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(1);
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @return $this
     */
    public function void(\Magento\Framework\Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(1);
        return $this;
    }

    /**
     * Cancel method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @return $this
     */
    public function cancel(\Magento\Framework\Object $payment)
    {
        return $this->void($payment);
    }
}
