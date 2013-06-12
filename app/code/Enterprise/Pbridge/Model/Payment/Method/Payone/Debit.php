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
 * Payone dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Payment_Method_Payone_Debit extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * PayOne Debit payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'pbridge_payone_debit';

    /**
     * Info block
     * @var string
     */
    protected $_infoBlockType = 'Enterprise_Pbridge_Block_Payment_Info_Payone_Debit';

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
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;

    /**
     * Retrieve dummy payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return Mage_Payment_Model_Method_Abstract::getCode();
    }
}
