<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\Payment\Method;

class Ogone extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'pbridge_ogone_direct';

    /**#@+
     * Availability options
     */
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment = true;
    /**#@-*/

    /**
     * Return true if 3D Secure checks performed on the last checkout step (Order review page)
     *
     * @return bool
     */
    public function getIsDeferred3dCheck()
    {
        return $this->is3dSecureEnabled();
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
        parent::refund($payment, $amount);
        $payment->setShouldCloseParentTransaction(false);
        return $this;
    }
}
