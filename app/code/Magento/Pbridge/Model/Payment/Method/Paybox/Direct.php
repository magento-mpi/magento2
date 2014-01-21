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
 * Paybox dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Payment\Method\Paybox;

use Magento\Object;

class Direct extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * @var string
     */
    protected $_code  = 'paybox_direct';

    /**
     * @var bool
     */
    protected $_isGateway               = true;

    /**
     * @var bool
     */
    protected $_canAuthorize            = true;

    /**
     * @var bool
     */
    protected $_canCapture              = true;

    /**
     * @var bool
     */
    protected $_canCapturePartial       = false;

    /**
     * @var bool
     */
    protected $_canRefund               = false;

    /**
     * @var bool
     */
    protected $_canVoid                 = false;

    /**
     * @var bool
     */
    protected $_canUseInternal          = true;

    /**
     * @var bool
     */
    protected $_canUseCheckout          = true;

    /**
     * @var bool
     */
    protected $_canUseForMultishipping  = true;

    /**
     * @var bool
     */
    protected $_canSaveCc               = true;

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(Object $payment, $amount)
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
     * @param Object $payment
     * @param float $amount
     * @return $this
     * @throws \Magento\Core\Exception
     */
    public function refund(Object $payment, $amount)
    {
        throw new \Magento\Core\Exception(__('Refund action is not available.'));
    }
}
