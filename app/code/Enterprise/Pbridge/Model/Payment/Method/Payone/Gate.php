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
class Enterprise_Pbridge_Model_Payment_Method_Payone_Gate extends Enterprise_Pbridge_Model_Payment_Method_Abstract
{
    /**
     * PayOne Direct payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'payone_gate';

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
     * Do not validate payment form using server methods
     *
     * @return  bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Disable payment method for admin orders if 3D Secure is ON
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        if ($this->is3dSecureEnabled() && Mage::app()->getStore()->isAdmin()) {
            return false;
        }
        return parent::isAvailable($quote);
    }

    /**
     * Set order status to Pending until IPN
     * @return bool
     */
    public function isInitializeNeeded()
    {
        if ($this->is3dSecureEnabled()) {
            return true;
        }
        return parent::isInitializeNeeded();
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param Varien_Object $stateObject
     * @return \Mage_Payment_Model_Abstract|void
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
            case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

                $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }

    /**
     * Whether order created required on Order Review page or not
     * @return bool
     */
    public function getIsPendingOrderRequired()
    {
        if ($this->is3dSecureEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * Return URL after order placed successfully. Redirect parent to checkout/success
     *
     * @return string
     */
    public function getRedirectUrlSuccess()
    {
        return Mage::getUrl('enterprise_pbridge/pbridge/onepagesuccess', array('_secure' => true));
    }

    /**
     * Return URL after order placed with errors. Redirect parent to checkout/failure
     *
     * @return string
     */
    public function getRedirectUrlError()
    {
        return Mage::getUrl('enterprise_pbridge/pbridge/cancel', array('_secure' => true));
    }
}
