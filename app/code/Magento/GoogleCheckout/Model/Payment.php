<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Model_Payment extends Magento_Payment_Model_Method_Abstract
{
    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;

    protected $_code  = 'googlecheckout';
    protected $_formBlockType = 'Magento_GoogleCheckout_Block_Form';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = false;
    protected $_canUseForMultishipping  = false;

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('googlecheckout/redirect/redirect');
    }

    /**
     * Authorize
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return  Magento_GoogleCheckout_Model_Payment
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        $api = Mage::getModel('Magento_GoogleCheckout_Model_Api')->setStoreId($payment->getOrder()->getStoreId());
        $api->authorize($payment->getOrder()->getExtOrderId());

        return $this;
    }

    /**
     * Capture payment
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return  Magento_GoogleCheckout_Model_Payment
     */
    public function capture(Magento_Object $payment, $amount)
    {
        if ($payment->getOrder()->getPaymentAuthExpiration() < Mage::getModel('Magento_Core_Model_Date')->gmtTimestamp()) {
            try {
                $this->authorize($payment, $amount);
            } catch (Exception $e) {
                // authorization is not expired yet
            }
        }

        $api = Mage::getModel('Magento_GoogleCheckout_Model_Api')->setStoreId($payment->getOrder()->getStoreId());
        $api->charge($payment->getOrder()->getExtOrderId(), $amount);
        $payment->setForcedState(Magento_Sales_Model_Order_Invoice::STATE_OPEN);

        return $this;
    }

    /**
     * Refund money
     *
     * @param Magento_Object $payment
     * @param float $amount
     *
     * @return  Magento_GoogleCheckout_Model_Payment
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $reason = $this->getReason() ? $this->getReason() : __('No Reason');
        $comment = $this->getComment() ? $this->getComment() : __('No Comment');

        $api = Mage::getModel('Magento_GoogleCheckout_Model_Api')->setStoreId($payment->getOrder()->getStoreId());
        $api->refund($payment->getOrder()->getExtOrderId(), $amount, $reason, $comment);

        return $this;
    }

    public function void(Magento_Object $payment)
    {
        $this->cancel($payment);

        return $this;
    }

    /**
     * Void payment
     *
     * @param Magento_Object $payment
     *
     * @return Magento_GoogleCheckout_Model_Payment
     */
    public function cancel(Magento_Object $payment)
    {
        if (!$payment->getOrder()->getBeingCanceledFromGoogleApi()) {
            $reason = $this->getReason() ? $this->getReason() : __('Unknown Reason');
            $comment = $this->getComment() ? $this->getComment() : __('No Comment');

            $api = Mage::getModel('Magento_GoogleCheckout_Model_Api')->setStoreId($payment->getOrder()->getStoreId());
            $api->cancel($payment->getOrder()->getExtOrderId(), $reason, $comment);
        }

        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|Magento_Core_Model_Store $storeId
     *
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'google/checkout/' . $field;

        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Check void availability
     *
     * @param   Magento_Object $payment
     * @return  bool
     */
    public function canVoid(Magento_Object $payment)
    {
        if ($payment instanceof Magento_Sales_Model_Order_Invoice
            || $payment instanceof Magento_Sales_Model_Order_Creditmemo
        ) {
            return false;
        }

        return $this->_canVoid;
    }
}
