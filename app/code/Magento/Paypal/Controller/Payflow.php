<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Checkout Controller
 */
class Magento_Paypal_Controller_Payflow extends Magento_Core_Controller_Front_Action
{
    /**
     * @var Magento_Checkout_Model_Session
     */
    protected $_checkoutSession;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Paypal_Model_PayflowlinkFactory
     */
    protected $_payflowlinkFactory;

    /**
     * @var Magento_Paypal_Helper_Checkout
     */
    protected $_checkoutHelper;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Paypal_Model_PayflowlinkFactory $payflowlinkFactory
     * @param Magento_Paypal_Helper_Checkout $checkoutHelper
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Paypal_Model_PayflowlinkFactory $payflowlinkFactory,
        Magento_Paypal_Helper_Checkout $checkoutHelper
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $context->getLogger();
        $this->_payflowlinkFactory = $payflowlinkFactory;
        $this->_checkoutHelper = $checkoutHelper;
        parent::__construct($context);
    }

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

        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $this->_checkoutSession->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    Magento_Sales_Model_Order::STATE_PROCESSING,
                    Magento_Sales_Model_Order::STATE_COMPLETE
                );
                if (in_array($order->getState(), $allowedOrderStates)) {
                    $this->_checkoutSession->unsLastRealOrderId();
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
            $paymentModel = $this->_payflowlinkFactory->create();
            try {
                $paymentModel->process($data);
            } catch (Exception $e) {
                $this->_logger->logException($e);
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
        $this->_checkoutHelper->cancelCurrentOrder($errorMsg);
        if ($this->_checkoutHelper->restoreQuote()) {
            //Redirect to payment step
            $gotoSection = 'payment';
        }

        return $gotoSection;
    }
}
