<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method\Eway;

class Direct extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'eway_direct';

    /**
     * List of default accepted currency codes supported by payment gateway
     *
     * @var array
     */
    protected $_allowCurrencyCode = ['AUD','USD', 'GBP', 'NZD', 'CAD', 'HKD', 'SGD', 'EUR', 'JPY'];

    /**#@+
     * Availability options
     */
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canCaptureOnce = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canSaveCc = true;
    /**#@-*/

    /**
     * Check whether it's possible to void authorization
     *
     * @param \Magento\Framework\Object $payment
     * @return bool
     */
    public function canVoid(\Magento\Framework\Object $payment)
    {
        $canVoid = parent::canVoid($payment);

        if ($canVoid) {
            $order = $this->getInfoInstance()->getOrder();
            if ($order && count($order->getInvoiceCollection()) > 0) {
                $canVoid = false;
            }
        }

        return $canVoid;
    }
}
