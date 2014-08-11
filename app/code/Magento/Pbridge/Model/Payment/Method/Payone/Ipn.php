<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method\Payone;

use Magento\Sales\Model\Order\Email\Sender\OrderSender;

class Ipn
{
    /*
     * @var \Magento\Sales\Model\Order
     */
    protected $_order = null;

    /**
     * IPN request data
     *
     * @var array
     */
    protected $_ipnFormData = array();

    /**
     * Fields that should be replaced in debug with '***'
     *
     * @var array
     */
    protected $_debugReplacePrivateDataKeys = array();

    /**
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * @var \Magento\Framework\HTTP\Adapter\Curl
     */
    protected $_curl;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var OrderSender
     */
    protected $orderSender;

    /**
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\HTTP\Adapter\Curl $curl
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\Logger $logger
     * @param OrderSender $orderSender
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\HTTP\Adapter\Curl $curl,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\Logger $logger,
        OrderSender $orderSender
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_curl = $curl;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
        $this->orderSender = $orderSender;
    }

    /**
     * IPN request data setter
     *
     * @param array $data
     * @return $this
     */
    public function setIpnFormData(array $data)
    {
        $this->_ipnFormData = $data;
        return $this;
    }

    /**
     * IPN request data getter
     *
     * @param string $key
     * @return array|string
     */
    public function getIpnFormData($key = null)
    {
        if (null === $key) {
            return $this->_ipnFormData;
        }
        return isset($this->_ipnFormData[$key]) ? $this->_ipnFormData[$key] : null;
    }

    /**
     * Get ipn data, send verification to PayPal, run corresponding handler
     *
     * @throws \Exception
     * @return void
     */
    public function processIpnRequest()
    {
        if (!$this->_ipnFormData) {
            return;
        }

        $sReq = '';

        foreach ($this->_ipnFormData as $k => $v) {
            $sReq .= '&' . $k . '=' . urlencode(stripslashes($v));
        }
        // append ipn command
        $sReq .= "&cmd=_notify-validate";
        $sReq = substr($sReq, 1);

        $url = rtrim($this->_pbridgeData->getBridgeBaseUrl(), '/') . '/ipn.php?action=PayoneIpn';

        try {
            $config = array('timeout' => 60);
            $this->_curl->setConfig($config);
            $this->_curl->write(\Zend_Http_Client::POST, $url, '1.1', array(), $sReq);
            $response = $this->_curl->read();
        } catch (\Exception $e) {
            throw $e;
        }

        if ($error = $this->_curl->getError()) {
            $this->_notifyAdmin(__('IPN postback HTTP error: %1', $error));
            $this->_curl->close();
            return;
        }

        // cUrl resource must be closed after checking it for errors
        $this->_curl->close();

        if (false !== preg_match('~VERIFIED~si', $response)) {
            $this->processIpnVerified();
        } else {
            $this->_notifyAdmin(__('IPN postback Validation error: %1', $sReq));
        }
    }

    /**
     * Load and validate order
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    protected function _getOrder()
    {
        if (empty($this->_order)) {
            // get proper order
            $id = $this->getIpnFormData('order_id');
            /** @var \Magento\Sales\Model\Order $order */
            $order = $this->_orderFactory->create();
            $order->loadByIncrementId($id);
            if (!$order->getId()) {
                // throws Exception intentionally, because cannot be logged to order comments
                throw new \Exception(__('Wrong Order ID (%1) specified.', $id));
            }
            $this->_order = $order;
        }
        return $this->_order;
    }

    /**
     * IPN workflow implementation
     * Everything should be added to order comments. In positive processing cases customer will get email notifications.
     * Admin will be notified on errors.
     *
     * @return void
     */
    public function processIpnVerified()
    {
        try {
            $paymentStatus = $this->getIpnFormData('txaction');
            switch ($paymentStatus) {
                case 'appointed':
                    $this->_registerPaymentAuthorization();
                    break;
                case 'cancellation':
                    $this->_registerPaymentFailure();
                    break;
                case 'paid':
                case 'capture':
                    $this->_registerPaymentCapture();
                    break;
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $history = $this->_createIpnComment(__('Note: %1', $e->getMessage()))
                ->save();
            $this->_notifyAdmin($history->getComment(), $e);
        }
    }

    /**
     * Register authorization of a payment: create a non-paid invoice
     *
     * @return void
     */
    protected function _registerPaymentAuthorization()
    {
        // authorize payment
        $order = $this->_getOrder();
        if ($order->getStatus() != \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT) {
            return false;
        }
        $comment = __('3D secure authentication passed.');
        $order->getPayment()
            ->setPreparedMessage($this->_createIpnComment($comment, false))
            ->setTransactionId($this->getIpnFormData('transaction_id'))
            ->setParentTransactionId($this->getIpnFormData('txid'))
            ->setIsTransactionClosed(0)
            ->registerAuthorizationNotification($this->getIpnFormData('price'));

        $order->save();
    }

    /**
     * Process completed payment
     * If an existing authorized invoice with specified txn_id exists - mark it as paid and save,
     * otherwise create a completely authorized/captured invoice
     *
     * Everything after saving order is not critical, thus done outside the transaction.
     *
     * @throws \Magento\Framework\Model\Exception
     * @return bool|void
     */
    protected function _registerPaymentCapture()
    {
        $order = $this->_getOrder();
        if ($order->getStatus() != \Magento\Sales\Model\Order::STATE_PROCESSING) {
            return false;
        }
        $payment = $order->getPayment();
        $payment->setTransactionId($this->getIpnFormData('transaction_id'))
            ->setPreparedMessage($this->_createIpnComment('', false))
            ->setParentTransactionId($this->getIpnFormData('txid'))
            ->setShouldCloseParentTransaction(1)
            ->setIsTransactionClosed(0)
            ->registerCaptureNotification($this->getIpnFormData('price'));
        $order->save();

        // notify customer
        if ($invoice = $payment->getCreatedInvoice()) {
            $this->orderSender->send($order);
            $order->addStatusHistoryComment(
                __('Notified customer about invoice #%1.', $invoice->getIncrementId())
            )
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    /**
     * Treat failed payment as order cancellation
     *
     * @param string $explanationMessage
     * @return bool|void
     */
    protected function _registerPaymentFailure($explanationMessage = '')
    {
        $order = $this->_getOrder();
        if ($order->getStatus() != \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT) {
            return false;
        }
        $cancellationComment = $this->_createIpnComment($explanationMessage, false);
        $order->registerCancellation($cancellationComment, false)
            ->save();
    }

    /**
     * Generate a "PayPal Verified" comment with additional explanation.
     * Returns the generated comment or order status history object
     *
     * @param string $comment
     * @param bool $addToHistory
     * @return string|\Magento\Sales\Model\Order\Status\History
     */
    protected function _createIpnComment($comment = '', $addToHistory = true)
    {
        $paymentStatus = $this->getIpnFormData('txaction');
        $message = __('IPN verification "%1".', $paymentStatus);
        if ($comment) {
            $message .= ' ' . $comment;
        }
        if ($addToHistory) {
            $message = $this->_getOrder()->addStatusHistoryComment($message);
            $message->setIsCustomerNotified(null);
        }
        return $message;
    }

    /**
     * Notify Administrator about exceptional situation
     *
     * @param string $message
     * @param \Exception $exception
     * @return void
     */
    protected function _notifyAdmin($message, \Exception $exception = null)
    {
        // prevent notification failure cause order procesing failure
        try {
            $this->_logger->log($message);
            if ($exception) {
                $this->_logger->logException($exception);
            }
        } catch (\Exception $e) {
            $this->_logger->logException($e);
        }
    }
}
