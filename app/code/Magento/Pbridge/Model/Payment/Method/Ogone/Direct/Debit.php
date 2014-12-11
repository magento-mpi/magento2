<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Pbridge\Model\Payment\Method\Ogone\Direct;

class Debit extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'pbridge_ogone_direct_debit';

    /**
     * Info block
     *
     * @var string
     */
    protected $_infoBlockType = 'Magento\Pbridge\Block\Payment\Info\Ogone\Debit';

    /**#@+
     * Availability options
     */
    protected $_isGateway = true;
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = true;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid = true;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment = true;
    /**#@-*/
}
