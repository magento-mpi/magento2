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
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Mage_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_PAYFLOWADVANCED;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_Paypal_Model_Payflowadvanced';

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

        $session = $this->_getCheckout();
        if ($session->getLastRealOrderId()) {
            $order = Mage::getModel('Mage_Sales_Model_Order')->loadByIncrementId($session->getLastRealOrderId());

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
            /** @var $paymentModel Mage_Paypal_Model_Payflowadvanced */
            $paymentModel = Mage::getModel('Mage_Paypal_Model_Payflowadvanced');
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
        /* @var $helper Mage_Paypal_Helper_Checkout */
        $helper = Mage::helper('Mage_Paypal_Helper_Checkout');
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
