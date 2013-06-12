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
 * Braintree dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Payment_Method_Braintree_Basic extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * Payment method code
     *
     * @var string
     */
    const METHOD_CODE = 'braintree_basic';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = self::METHOD_CODE;

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    /**
     * List of default accepted currency codes supported by payment gateway
     *
     * @var array
     */
    protected $_allowCurrencyCode = array('USD');
}
