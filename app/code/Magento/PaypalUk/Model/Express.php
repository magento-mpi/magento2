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
namespace Magento\PaypalUk\Model;

class Express extends \Magento\Paypal\Model\Express
{
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;
    protected $_formBlockType = 'Magento\PaypalUk\Block\Express\Form';
    protected $_canCreateBillingAgreement = false;
    protected $_canManageRecurringProfiles = false;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\PaypalUk\Model\Pro';

    /**
     * Express Checkout payment method instance
     *
     * @var \Magento\Paypal\Model\Express
     */
    protected $_ecInstance = null;

    /**
     * EC PE won't be available if the EC is available
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        if (!parent::isAvailable($quote)) {
            return false;
        }
        if (!$this->_ecInstance) {
            $this->_ecInstance = $this->_paymentData
                ->getMethodInstance(\Magento\Paypal\Model\Config::METHOD_WPP_EXPRESS);
        }
        if ($quote && $this->_ecInstance) {
            $this->_ecInstance->setStore($quote->getStoreId());
        }
        return $this->_ecInstance ? !$this->_ecInstance->isAvailable() : false;
    }

    /**
     * Import payment info to payment
     *
     * @param \Magento\Paypal\Model\Api\Nvp
     * @param \Magento\Sales\Model\Order\Payment
     */
    protected function _importToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getPaypalTransactionId())->setIsTransactionClosed(0)
            ->setAdditionalInformation(\Magento\Paypal\Model\Express\Checkout::PAYMENT_INFO_TRANSPORT_REDIRECT,
                $api->getRedirectRequired() || $api->getRedirectRequested()
            )
            ->setIsTransactionPending($api->getIsPaymentPending())
            ->setTransactionAdditionalInfo(\Magento\PaypalUk\Model\Pro::TRANSPORT_PAYFLOW_TXN_ID,
                $api->getTransactionId())
        ;
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $api->getTransactionId()));
        \Mage::getModel('Magento\Paypal\Model\Info')->importToPayment($api, $payment);
    }

    /**
     * Checkout redirect URL getter for onepage checkout (hardcode)
     *
     * @see \Magento\Checkout\Controller\Onepage::savePaymentAction()
     * @see \Magento\Sales\Model\Quote\Payment::getCheckoutRedirectUrl()
     * @return string
     */
    public function getCheckoutRedirectUrl()
    {
        return \Mage::getUrl('paypaluk/express/start');
    }
}
