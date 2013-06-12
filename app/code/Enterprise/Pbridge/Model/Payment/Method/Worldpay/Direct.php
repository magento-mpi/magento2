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
 * Worldpay dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento
 */
class Enterprise_Pbridge_Model_Payment_Method_Worldpay_Direct extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * Worldpay payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'worldpay_direct';

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
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)){
            return false;
        }

        if('business' == $this->getConfigData('account_type') && !$this->getConfigData('installation_id')) {
            return false;
        }

        if ($this->is3dSecureEnabled() && Mage::app()->getStore()->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * Return true if 3D Secure checks performed on the last checkout step (Order review page)
     *
     * @return bool
     */
    public function getIsDeferred3dCheck()
    {
        return $this->is3dSecureEnabled();
    }
}
