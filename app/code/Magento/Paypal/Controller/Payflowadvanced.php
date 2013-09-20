<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Advanced Checkout Controller
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Controller_Payflowadvanced extends Magento_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Magento_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento_Paypal_Model_Payflowadvanced';

    /**
     * When a customer cancel payment from payflow gateway.
     *
     * @return void
     */
    public function cancelPaymentAction()
    {
        $this->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->getLayout()->getBlock('payflow.advanced.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        $this->renderLayout();
    }

    /**
     * When a customer return to website from payflow gateway.
     *
     * @return void
     */
    public function returnUrlAction()
    {
        $this->loadLayout(false);
        $redirectBlock = $this->getLayout()->getBlock('payflow.advanced.iframe');;

        $session = $this->_objectManager->get('Magento_Checkout_Model_Session');
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('Magento_Sales_Model_Order')->loadByIncrementId($session->getLastRealOrderId());

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
     *
     * @return void
     */
    public function formAction()
    {
        $this->loadLayout(false)->renderLayout();
        $html = $this->getLayout()->getBlock('payflow.advanced.iframe')->toHtml();
        $this->getResponse()->setBody($html);
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
            /** @var $paymentModel Magento_Paypal_Model_Payflowadvanced */
            $paymentModel = Mage::getModel('Magento_Paypal_Model_Payflowadvanced');
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
     * @return bool|string
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
