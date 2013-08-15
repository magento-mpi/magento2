<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * PayPalUk Express Module
 */
class Mage_PaypalUk_Model_Express extends Mage_Paypal_Model_Express
{
    protected $_code = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;
    protected $_formBlockType = 'Mage_PaypalUk_Block_Express_Form';
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Mage_PaypalUk_Model_Pro';

    /**
     * Express Checkout payment method instance
     *
     * @var Mage_Paypal_Model_Express
     */
    protected $_ecInstance = null;

    /**
     * EC PE won't be available if the EC is available
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = Mage::helper('Mage_Payment_Helper_Data')
                ->getMethodInstance(Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS);
        }
        if ($quote && $this->_ecInstance) {
            $this->_ecInstance->setStore($quote->getStoreId());
        }
        return $this->_ecInstance ? !$this->_ecInstance->isAvailable() : false;
    }

    /**
     * Import payment info to payment
     *
     * @param Mage_Paypal_Model_Api_Nvp
     * @param Mage_Sales_Model_Order_Payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(Mage_Paypal_Model_Express_Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired() || $api->getRedirectRequested()
            )
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(Mage_PaypalUk_Model_Pro::TRANSPORT_PAYFLOW_TXN_ID, $api->getTransactionId())
        ;
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getTransactionId()));
        Mage::getModel('Mage_Paypal_Model_Info')->importToPayment($api, $payment);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see Mage_Checkout_Controller_Onepage::savePaymentAction()
     * @see Mage_Sales_Model_Quote_Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypaluk/express/start');
    }
}
