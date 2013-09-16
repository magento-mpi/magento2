<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPalUk Express Module
 */
class Magento_PaypalUk_Model_Express extends Magento_Paypal_Model_Express
{
    protected $_code = Magento_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;
    protected $_formBlockType = 'Magento_PaypalUk_Block_Express_Form';
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento_PaypalUk_Model_Pro';

    /**
     * Express Checkout payment method instance
     *
     * @var Magento_Paypal_Model_Express
     */
    protected $_ecInstance = null;

    /**
     * EC PE won't be available if the EC is available
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = $this->_paymentData
                ->getMethodInstance(Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        }
        if ($quote && $this->_ecInstance) {
            $this->_ecInstance->setStore($quote->getStoreId());
        }
        return $this->_ecInstance ? !$this->_ecInstance->isAvailable() : false;
    }

    /**
     * Import payment info to payment
     *
     * @param Magento_Paypal_Model_Api_Nvp
     * @param Magento_Sales_Model_Order_Payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(Magento_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired() || $api->getRedirectRequested()
            )
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(Magento_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId())
        ;
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getTransactionId()));
        Mage::getModel('Magento_Paypal_Model_Info')->importToPayment($api, $payment);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Magento_Checkout_Controller_Onepage::savePaymentAction()
     * @see Magento_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypaluk/express/start');
    }
}
