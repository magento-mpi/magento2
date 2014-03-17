<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PayPalRecurringPayment\Model;

use Exception;

/**
 * PayPal Recurring Instant Payment Notification processor model
 */
class Ipn extends \Magento\Paypal\Model\AbstractIpn implements \Magento\Paypal\Model\IpnInterface
{
    /**
     * Recurring payment instance
     *
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_recurringPayment;

    /**
     * @var \Magento\RecurringPayment\Model\PaymentFactory
     */
    protected $_recurringPaymentFactory;

    /**
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\HTTP\Adapter\CurlFactory $curlFactory
     * @param \Magento\RecurringPayment\Model\PaymentFactory $recurringPaymentFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Paypal\Model\ConfigFactory $configFactory,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\HTTP\Adapter\CurlFactory $curlFactory,
        \Magento\RecurringPayment\Model\PaymentFactory $recurringPaymentFactory,
        array $data = array()
    ) {
        parent::__construct($configFactory, $logAdapterFactory, $curlFactory, $data);
        $this->_recurringPaymentFactory = $recurringPaymentFactory;
    }

    /**
     * Get ipn data, send verification to PayPal, run corresponding handler
     *
     * @return void
     * @throws Exception
     */
    public function processIpnRequest()
    {
        $this->_addDebugData('ipn', $this->getRequestData());

        try {
            $this->_getConfig();
            $this->_postBack();
            $this->_processRecurringPayment();
        } catch (Exception $e) {
            $this->_addDebugData('exception', $e->getMessage());
            $this->_debug();
            throw $e;
        }
        $this->_debug();
    }

    /**
     * Get config with the method code and store id
     *
     * @return \Magento\Paypal\Model\Config
     * @throws Exception
     */
    protected function _getConfig()
    {
        $recurringPayment = $this->_getRecurringPayment();
        $methodCode = $recurringPayment->getMethodCode();
        $parameters = array('params' => array($methodCode, $recurringPayment->getStoreId()));
        $this->_config = $this->_configFactory->create($parameters);
        if (!$this->_config->isMethodActive($methodCode) || !$this->_config->isMethodAvailable()) {
            throw new Exception(sprintf('Method "%s" is not available.', $methodCode));
        }
        return $this->_config;
    }

    /**
     * Load recurring payment
     *
     * @return \Magento\RecurringPayment\Model\Payment
     * @throws Exception
     */
    protected function _getRecurringPayment()
    {
        $referenceId = $this->getRequestData('rp_invoice_id');
        $this->_recurringPayment = $this->_recurringPaymentFactory->create()->loadByInternalReferenceId($referenceId);
        if (!$this->_recurringPayment->getId()) {
            throw new Exception(sprintf('Wrong recurring payment INTERNAL_REFERENCE_ID: "%s".', $referenceId));
        }
        return $this->_recurringPayment;
    }

    /**
     * Process notification from recurring payments
     *
     * @return void
     * @throws \Magento\Core\Exception
     * @throws Exception
     */
    protected function _processRecurringPayment()
    {
        $this->_getConfig();
        try {
            // handle payment_status
            $paymentStatus = $this->_filterPaymentStatus($this->getRequestData('payment_status'));
            if ($paymentStatus != \Magento\Paypal\Model\Info::PAYMENTSTATUS_COMPLETED) {
                throw new Exception("Cannot handle payment status '{$paymentStatus}'.");
            }
            // Register recurring payment notification, create and process order
            $price = $this->getRequestData(
                'mc_gross'
            ) - $this->getRequestData(
                'tax'
            ) - $this->getRequestData(
                'shipping'
            );
            $productItemInfo = new \Magento\Object();
            $type = trim($this->getRequestData('period_type'));
            if ($type == 'Trial') {
                $productItemInfo->setPaymentType(\Magento\RecurringPayment\Model\PaymentTypeInterface::TRIAL);
            } elseif ($type == 'Regular') {
                $productItemInfo->setPaymentType(\Magento\RecurringPayment\Model\PaymentTypeInterface::REGULAR);
            }
            $productItemInfo->setTaxAmount($this->getRequestData('tax'));
            $productItemInfo->setShippingAmount($this->getRequestData('shipping'));
            $productItemInfo->setPrice($price);

            $order = $this->_recurringPayment->createOrder($productItemInfo);

            $payment = $order->getPayment()->setTransactionId(
                $this->getRequestData('txn_id')
            )->setCurrencyCode(
                $this->getRequestData('mc_currency')
            )->setPreparedMessage(
                $this->_createIpnComment('')
            )->setIsTransactionClosed(
                0
            );
            $order->save();
            $this->_recurringPayment->addOrderRelation($order->getId());
            $payment->registerCaptureNotification($this->getRequestData('mc_gross'));
            $order->save();

            // notify customer
            $invoice = $payment->getCreatedInvoice();
            if ($invoice) {
                $message = __('You notified customer about invoice #%1.', $invoice->getIncrementId());
                $order->sendNewOrderEmail()->addStatusHistoryComment($message)->setIsCustomerNotified(true)->save();
            }
        } catch (\Magento\Core\Exception $e) {
            $comment = $this->_createIpnComment(__('Note: %1', $e->getMessage()), true);
            //TODO: add to payment comments
            //$comment->save();
            throw $e;
        }
    }

    /**
     * Generate an "IPN" comment with additional explanation.
     * Returns the generated comment or order status history object
     *
     * @param string $comment
     * @param bool $addToHistory
     * @return string|\Magento\Sales\Model\Order\Status\History
     */
    protected function _createIpnComment($comment = '', $addToHistory = false)
    {
        $message = __('IPN "%1"', $this->getRequestData('payment_status'));
        if ($comment) {
            $message .= ' ' . $comment;
        }
        if ($addToHistory) {
        }
        return $message;
    }
}
