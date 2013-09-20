<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Checkout Controller
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Controller_Payflow extends Magento_Core_Controller_Front_Action
{
    /**
     * When a customer cancel payment from payflow gateway.
     */
    public function cancelPaymentAction()
    {
        $this->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->getLayout()->getBlock('payflow.link.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        $this->renderLayout();
    }

    /**
     * When a customer return to website from payflow gateway.
     */
    public function returnUrlAction()
    {
        $this->loadLayout(false);
        $redirectBlock = $this->getLayout()->getBlock('payflow.link.iframe');

        $session = $this->_objectManager->get('Magento_Checkout_Model_Session');
        if ($session->getLastRealOrderId()) {
            $order = $this->_objectManager->create('Magento_Sales_Model_Order')->loadByIncrementId($session->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $session->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    Magento_Sales_Model_Order::STATE_PROCESSING,
                    Magento_Sales_Model_Order::STATE_COMPLETE
                );
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $session->unsLastRealOrderId();
                    $redirectBlock->setGotoSuccessPage(true);
                } else {
                    $gotoSection = $this->_cancelPayment(strval($this->getRequest()->getParam('RESPMSG')));
                    $redirectBlock->setGotoSection($gotoSection);
                    $redirectBlock->setErrorMsg(__('Your payment has been declined. Please try again.'));
                }
            }
        }

        $this->renderLayout();
    }

    /**
     * Submit transaction to Payflow getaway into iframe
     */
    public function formAction()
    {
        $this->loadLayout(false)->renderLayout();
    }

    /**
     * Get response from PayPal by silent post method
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel Magento_Paypal_Model_Payflowlink */
            $paymentModel = Mage::getModel('Magento_Paypal_Model_Payflowlink');
            try {
                $paymentModel->process($data);
            } catch (Exception $e) {
                $this->_objectManager->get('Magento_Core_Model_Logger')->logException($e);
            }
        }
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
            //Redirect to payment step
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }
}
