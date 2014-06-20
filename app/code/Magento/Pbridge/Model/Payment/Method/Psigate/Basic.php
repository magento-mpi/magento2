<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method\Psigate;

class Basic extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'psigate_basic';

    /**
     * List of allowed currency codes
     * @var array
     */
    protected $_allowCurrencyCode = array('USD', 'CAD');

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
    /**#@-*/
}
