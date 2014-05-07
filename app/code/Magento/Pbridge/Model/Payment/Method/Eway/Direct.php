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
 * Eway.Com.Au dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model\Payment\Method\Eway;

class Direct extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Eway Direct payment method code
     *
     * @var string
     */
    protected $_code = 'eway_direct';

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
    protected $_canCapturePartial = true;

    /**
     * @var bool
     */
    protected $_canRefund = true;

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
    protected $_canSaveCc = true;

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
}
