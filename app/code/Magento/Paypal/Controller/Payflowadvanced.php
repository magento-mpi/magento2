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
namespace Magento\Paypal\Controller;

class Payflowadvanced extends \Magento\Paypal\Controller\Express\AbstractExpress
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento\Paypal\Model\Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = \Magento\Paypal\Model\Config::METHOD_PAYFLOWADVANCED;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento\Paypal\Model\Payflowadvanced';

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\Paypal\Helper\Checkout
     */
    protected $_checkoutHelper;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory
     * @param \Magento\Session\Generic $paypalSession
     * @param \Magento\Paypal\Helper\Checkout $checkoutHelper
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Paypal\Model\Express\Checkout\Factory $checkoutFactory,
        \Magento\Session\Generic $paypalSession,
        \Magento\Paypal\Helper\Checkout $checkoutHelper,
        \Magento\Logger $logger
    ) {
        $this->_logger = $logger;
        $this->_checkoutHelper = $checkoutHelper;
        parent::__construct(
            $context,
            $customerSession,
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
        $this->_view->loadLayout(false);
        $gotoSection = $this->_cancelPayment();
        $redirectBlock = $this->_view->getLayout()->getBlock('payflow.advanced.iframe');
        $redirectBlock->setGotoSection($gotoSection);
        $this->_view->renderLayout();
    }

    /**
     * When a customer return to website from payflow gateway.
     */
    public function returnUrlAction()
    {
        $this->_view->loadLayout(false);
        $redirectBlock = $this->_view->getLayout()->getBlock('payflow.advanced.iframe');;

        if ($this->_checkoutSession->getLastRealOrderId()) {
            $order = $this->_orderFactory->create()->loadByIncrementId($this->_checkoutSession->getLastRealOrderId());

            if ($order && $order->getIncrementId() == $this->_checkoutSession->getLastRealOrderId()) {
                $allowedOrderStates = array(
                    \Magento\Sales\Model\Order::STATE_PROCESSING,
                    \Magento\Sales\Model\Order::STATE_COMPLETE
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

        $this->_view->renderLayout();
    }

    /**
     * Submit transaction to Payflow getaway into iframe
     */
    public function formAction()
    {
        $this->_view->loadLayout(false)->renderLayout();
        $html = $this->_view->getLayout()->getBlock('payflow.advanced.iframe')->toHtml();
        $this->getResponse()->setBody($html);
    }

    /**
     * Get response from PayPal by silent post method
     */
    public function silentPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (isset($data['INVNUM'])) {
            /** @var $paymentModel \Magento\Paypal\Model\Payflowadvanced */
            $paymentModel = $this->_checkoutFactory->create($this->_checkoutType);
            try {
                $paymentModel->process($data);
            } catch (\Exception $e) {
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
