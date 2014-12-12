<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Model\Payment\Method\Braintree;

use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment;

class Basic extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'braintree_basic';

    /**
     * Array of allowed currency codes
     *
     * @var array
     */
    protected $_allowCurrencyCode = ['USD'];

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

    /**
     * Set capture transaction ID to invoice for informational purposes
     * @param Invoice $invoice
     * @param Payment $payment
     * @return $this
     */
    public function processInvoice($invoice, $payment)
    {
        $invoice->setTransactionId($payment->getLastTransId());
        return $this;
    }
}
