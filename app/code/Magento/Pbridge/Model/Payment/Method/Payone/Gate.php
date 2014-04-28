<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Payone dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Payment\Method\Payone;

class Gate extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payone payment method code
     *
     * @var string
     */
    protected $_code = 'payone_gate';

    /**
     * @var string[]
     */
    protected $_allowCurrencyCode = array('EUR');

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
    protected $_canRefundInvoicePartial = false;

    /**
     * @var bool
     */
    protected $_canVoid = false;

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
     * Authorization method being executed via Payment Bridge
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
        return $this;
    }
}
