<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Eway.Com.Au dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Eway_Direct extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * Eway.Com.Au payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'eway_direct';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_CODE;

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canCaptureOnce          = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = true;

    /**
     * Enable refunds only if Refund Password assigned in configuration
     * @return bool
     */
    public function canRefund()
    {
        return parent::canRefund() && $this->getConfigData('refunds_password');
    }

    /**
     * Enable refunds only if Refund Password assigned in configuration
     * @return bool
     */
    public function canRefundPartialPerInvoice()
    {
        return parent::canRefundPartialPerInvoice() && $this->getConfigData('refunds_password');
    }

    /**
     * Check whether it's possible to void authorization
     *
     * @param Varien_Object $payment
     * @return bool
     */
    public function canVoid(Varien_Object $payment)
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
