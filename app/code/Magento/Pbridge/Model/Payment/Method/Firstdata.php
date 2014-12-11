<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\Payment\Method;

class Firstdata extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'firstdata';

    /**#@+
     * Availability options
     */
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid = false;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canSaveCc = false;
    /**#@-*/
}
