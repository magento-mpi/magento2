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
 * Payflow Advanced Checkout Controller
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Paypal_PayflowadvancedController extends Mage_Paypal_Controller_Express_Abstract
{
    /**
     * When a customer cancel payment from payflow gateway.
     *
     * @return void
     */
    public function cancelPaymentAction()
    {
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_getIframeBlock()
            ->setGotoSection($gotoSection)
            ->setTemplate('payflowadvanced/redirect.phtml');
        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * When a customer return to website from payflow gateway.
     *
     * @return void
     */
    public function returnUrlAction()
    {
        $redirectBlock = $this->_getIframeBlock()
            ->setTemplate('paypal/payflowadvanced/redirect.phtml');

        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('sales/order')->loadByIncrementId($session->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $session->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    Mage_Sales_Model_Order::STATE_PROCESSING,
                    Mage_Sales_Model_Order::STATE_COMPLETE
                );
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $session->unsLastRealOrderId();
                    $redirectBlock->setGotoSuccessPage(true);
                } else {
                    $gotoSection = $this->_cancelPayment(strval($this->getRequest()->getParam('RESPMSG')));
                    $redirectBlock->setGotoSection($gotoSection);
                    $redirectBlock->setErrorMsg($this->__('Payment has been declined. Please try again.'));
                }
            }
        }

        $this->getResponse()->setBody($redirectBlock->toHtml());
    }

    /**
     * Submit transaction to Payflow getaway into iframe
     *
     * @return void
     */
    public function formAction()
    {
        $this->getResponse()
            ->setBody($this->_getIframeBlock()->toHtml());
    }

    /**
     * Get response from PayPal by silent post method
     *
     * @return void
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel Mage_Paypal_Model_Payflowadvanced */
            $paymentModel = Mage::getModel('paypal/payflowadvanced');
            try {
                $paymentModel->process($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    /**
     * Cancel order, return quote to customer
     *
     * @param string $errorMsg
     * @return bool|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($session->getLastRealOrderId());
            if ($order->getId()) {
                //Cancel order
                if ($order->getState() != Mage_Sales_Model_Order::STATE_CANCELED) {
                    $order->registerCancellation($errorMsg)->save();
                }
                $quote = Mage::getModel('Mage_Sales_Model_Quote')
                    ->load($order->getQuoteId());
                //Return quote
                if ($quote->getId()) {
                    $quote->setIsActive(1)
                        ->setReservedOrderId(NULL)
                        ->save();
                    $session->replaceQuote($quote);
                }
                //Unset data
                $session->unsLastRealOrderId();
                //Redirect to payment step
                $gotoSection = 'payment';
            }
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

    /**
     * Get iframe block
     *
     * @return Mage_Paypal_Block_Payflow_Advanced_Iframe
     */
    protected function _getIframeBlock()
    {
        $this->loadLayout('paypal_payflow_advanced_iframe');
        return $this->getLayout()
            ->getBlock('payflow.advanced.iframe');
    }
}
