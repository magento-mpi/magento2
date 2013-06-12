<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PSi Gate dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Payment_Method_Psigate_Basic extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * PSi Gate method code
     *
     * @var string
     */
    const METHOD_CODE = 'psigate_basic';

    /**
     * Payment code
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
     * List of allowed currency codes
     *
     * @var array
     */
    protected $_allowCurrencyCode = array('USD', 'CAD');
}
