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
 * Cybersource.Com dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Cybersource_Soap extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * Cybersource.Com payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'cybersource_soap';

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = self::PAYMENT_CODE;

    const JAPANESE_METHOD_SINGLE      = '1';
    const JAPANESE_METHOD_BONUS       = '2';
    const JAPANESE_METHOD_INSTALLMENT = '4';
    const JAPANESE_METHOD_REVOLVING   = '5';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;
}
