<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Hosted Pro Checkout Controller
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Controller_Hostedpro extends Magento_Core_Controller_Front_Action
{
    /**
     * When a customer return to website from gateway.
     */
    public function returnAction()
    {
        $session = $this->_objectManager->get('Magento_Checkout_Model_Session');;
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
        $helper = $this->_objectManager->get('Magento_Paypal_Helper_Checkout');
        $helper->cancelCurrentOrder($errorMsg);
        if ($helper->restoreQuote()) {
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }
}
