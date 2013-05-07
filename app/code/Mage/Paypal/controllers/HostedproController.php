<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Pro Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_HostedproController extends Mage_Core_Controller_Front_Action
{
    /**
     * When a customer return to website from gateway.
     */
    public function returnAction()
    {
        $session = $this->_getCheckout();
        //TODO: some actions with order
        if ($session->getLastRealOrderId()) {
            $this->_redirect('checkout/onepage/success');
        }
    }

    /**
     * When a customer cancel payment from gateway.
     */
    public function cancelAction()
    {
        $this->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->getLayout()->getBlock('hosted.pro.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        //TODO: clarify return logic whether customer will be returned in iframe or in parent window
        $this->renderLayout();
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return mixed
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        /* @var $helper Mage_Paypal_Helper_Checkout */
        $helper = Mage::helper('paypal/checkout');
        $helper->cancelCurrentOrder($errorMsg);
        if ($helper->restoreQuote()) {
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }

    /**
     * Get frontend checkout session object
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('Mage_Checkout_Model_Session');
    }
}
