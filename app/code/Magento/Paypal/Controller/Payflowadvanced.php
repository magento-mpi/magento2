<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payflow Advanced Checkout Controller
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
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @var Magento_Paypal_Helper_Checkout
     */
    protected $_checkoutHelper;

    /**
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Core_Model_UrlInterface $urlBuilder
     * @param Magento_Sales_Model_QuoteFactory $quoteFactory
     * @param Magento_Checkout_Model_Session $checkoutSession
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory
     * @param Magento_Core_Model_Session_Generic $paypalSession
     * @param Magento_Paypal_Helper_Checkout $checkoutHelper
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Core_Model_UrlInterface $urlBuilder,
        Magento_Sales_Model_QuoteFactory $quoteFactory,
        Magento_Checkout_Model_Session $checkoutSession,
        Magento_Sales_Model_OrderFactory $orderFactory,
        Magento_Paypal_Model_Express_Checkout_Factory $checkoutFactory,
        Magento_Core_Model_Session_Generic $paypalSession,
        Magento_Paypal_Helper_Checkout $checkoutHelper
    ) {
        $this->_logger = $context->getLogger();
        $this->_checkoutHelper = $checkoutHelper;
        parent::__construct(
            $context,
            $customerSession,
            $urlBuilder,
            $quoteFactory,
            $checkoutSession,
            $orderFactory,
            $checkoutFactory,
            $paypalSession
        );
    }

    /**
     * When a customer cancel payment from payflow gateway.
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
     */
    public function returnUrlAction()
    {
        $this->loadLayout(false);
        $redirectBlock = $this->getLayout()->getBlock('payflow.advanced.iframe');;

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
        $html = $this->getLayout()->getBlock('payflow.advanced.iframe')->toHtml();
        $this->getResponse()->setBody($html);
    }

    /**
     * Get response from PayPal by silent post method
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel Magento_Paypal_Model_Payflowadvanced */
            $paymentModel = $this->_checkoutFactory->create($this->_checkoutType);
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
     * @return bool|string
     */
    protected function _cancelPayment($errorMsg = '')
    {
        $gotoSection = false;
        $this->_checkoutHelper->cancelCurrentOrder($errorMsg);
        if ($this->_checkoutHelper->restoreQuote()) {
            $gotoSection = 'payment';
        }
        return $gotoSection;
    }
}
