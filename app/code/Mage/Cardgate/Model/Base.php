<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Model_Base extends Magento_Object
{
    /**
     * Store Config object
     *
     * @var Magento_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Config object
     *
     * @var Magento_Core_Model_Config
     */
    protected $_configObject;

    /**
     * Dir object
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Resource Transaction factory
     *
     * @var Magento_Core_Model_Resource_Transaction_Factory
     */
    protected $_transactionFactory;

    /**
     * Sales Order factory
     *
     * @var Mage_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * Logger object
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Helper object
     *
     * @var Mage_Cardgate_Helper_Data
     */
    protected $_helper;

    /**
     * Filesystem
     *
     * @var Magento_Filesystem
     */
    protected $_filesystem;

    /**
     * Filesystem
     *
     * @var string
     */
    protected $_lockDir;

    /**
     * Callback Data
     *
     * @var array
     */
    protected $_callback;

    /**
     * Basic Card Gate settings
     *
     * @var mixed
     */
    protected $_config = null;

    /**
     * Is payment locked
     *
     * @var bool
     */
    protected $_isLocked = false;

    /**
     * Log file name
     *
     * @var string
     */
    protected $_logFileName = "cardgateplus.log";

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Store_Config $storeConfig
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Dir $dir
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Resource_Transaction_Factory $transactionFactory
     * @param Mage_Sales_Model_OrderFactory $orderFactory
     * @param Mage_Cardgate_Helper_Data $helper
     * @param Magento_Filesystem $filesystem
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Store_Config $storeConfig,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Dir $dir,
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Resource_Transaction_Factory $transactionFactory,
        Mage_Sales_Model_OrderFactory $orderFactory,
        Mage_Cardgate_Helper_Data $helper,
        Magento_Filesystem $filesystem,
        array $data = array()
    ) {
        parent::__construct($data);

        $this->_storeConfig = $storeConfig;
        $this->_configObject = $config;
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->_transactionFactory = $transactionFactory;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;
        $this->_filesystem = $filesystem;

        $this->_config = $this->_storeConfig->getConfig('payment/cardgate');
        if ($this->getConfigData('debug')) {
            $this->_logger->addStreamLog('cardgate', $this->_logFileName);
        }
        $this->_lockDir = $this->_dir->getDir(Magento_Core_Model_Dir::VAR_DIR) . DS . 'locks';
    }

    /**
     * Retrieve config value
     *
     * @param string $field
     * @return mixed
     */
    public function getConfigData($field)
    {
        if (isset($this->_config[$field])) {
            return $this->_config[$field];
        } else {
            return false;
        }
    }

    /**
     * Set callback data
     *
     * @param array $data
     * @return Mage_Cardgate_Model_Base
     */
    public function setCallbackData($data)
    {
        $this->_callback = $data;
        return $this;
    }

    /**
     * Get callback data
     *
     * @param string $field
     * @return string|null
     */
    public function getCallbackData($field = null)
    {
        if ($field === null) {
            return $this->_callback;
        } elseif (isset($this->_callback[$field])) {
            return $this->_callback[$field];
        } else {
            return null;
        }
    }

    /**
     * If the debug mode is enabled
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this->getConfigData('debug');
    }

    /**
     * If the test mode is enabled
     *
     * @return bool
     */
    public function isTest()
    {
        return $this->getConfigData('test_mode');
    }

    /**
     * Log data into the logfile
     *
     * @param string $msg
     * @return void
     */
    public function log($msg)
    {
        if ($this->getConfigData('debug')) {
            $this->_logger->log($msg, Zend_Log::DEBUG, 'cardgate');
        }
    }

    /**
     * Create lock file
     *
     * @return Mage_Cardgate_Model_Base
     */
    public function lock()
    {
        $this->_filesystem->setIsAllowCreateDirectories(true);
        $this->_filesystem->ensureDirectoryExists($this->_lockDir);
        $this->_filesystem->setWorkingDirectory($this->_lockDir);

        $lockFilename = $this->getCallbackData('ref') . '.lock';

        if (!$this->_filesystem->isFile($this->_lockDir . DS . $lockFilename)) {
            $this->_isLocked = true;
            $pid = getmypid();
            $now = date('Y-m-d H:i:s');
            $this->_filesystem->write($this->_lockDir . DS . $lockFilename, "Locked by $pid at $now\n");
            $this->_filesystem->changePermissions($this->_lockDir . DS . $lockFilename, 0644);
        }

        return $this;
    }

    /**
     * Unlock file
     *
     * @return Mage_Cardgate_Model_Base
     */
    public function unlock()
    {
        $this->_isLocked = false;
        $lockFilename = $this->getCallbackData('ref') . '.lock';

        if ($this->_filesystem->isFile($this->_lockDir . DS . $lockFilename)) {
            $this->_filesystem->delete($this->_lockDir . DS . $lockFilename);
        }

        return $this;
    }

    /**
     * Create and mail invoice
     *
     * @param Mage_Sales_Model_Order $order
     * @return boolean
     */
    protected function _createInvoice(Mage_Sales_Model_Order $order)
    {
        if ($order->canInvoice() && !$order->getInvoiceCollection()->getSize()) {
            $invoice = $order->prepareInvoice();
            $invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->save();

            $this->_transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $mailInvoice = $this->getConfigData("mail_invoice");
            if ($mailInvoice) {
                $invoice->setEmailSent(true);
                $invoice->save();
                $invoice->sendEmail();
            }

            $statusMessage = $mailInvoice ? "Invoice #%s created and send to customer." : "Invoice #%s created.";
            $order->addStatusToHistory(
                $order->getStatus(),
                $this->_helper->__($statusMessage, $invoice->getIncrementId()),
                $mailInvoice
            );

            return true;
        }

        return false;
    }

    /**
     * Returns true if the amounts match
     *
     * @throws RuntimeException
     * @param Mage_Sales_Model_Order $order
     * @return boolean
     */
    protected function _validateAmount(Mage_Sales_Model_Order $order)
    {
        $amountInCents = (int)sprintf('%.0f', $order->getBaseTotalDue() * 100);
        $callbackAmount = (int)$this->getCallbackData('amount');

        if (($amountInCents != $callbackAmount) AND (abs($callbackAmount - $amountInCents) > 1)) {
            $this->log('OrderID: ' . $order->getId() . ' do not match amounts. Sent '
                . $amountInCents . ', received: ' . $callbackAmount);
            $statusMessage = $this->_helper
                ->__("Hacker attempt: Order total amount does not match CardGatePlus's gross total amount!");
            $order->addStatusToHistory($order->getStatus(), $statusMessage);
            $order->save();

            throw new RuntimeException('Amount validation failed!');
        }

        return true;
    }

    /**
     * Check whether the order can be updated
     *
     * @param Mage_Sales_Model_Order $order
     * @return bool
     */
    protected function _isOrderUpdatable(Mage_Sales_Model_Order $order)
    {
        // Update only certain states
        $canUpdate = false;
        if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW ||
            $order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT ||
            $order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING ||
            $order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
            $canUpdate = true;
        }

        // Don't update order status if the payment is complete
        foreach ($order->getStatusHistoryCollection(true) as $_item) {
            if ($_item->getComment() == $this->_helper->__("Payment complete.")) {
                $canUpdate = false;
            }
        }

        return $canUpdate;
    }

    /**
     * Sends new order email if it wasn't send earlier
     *
     * @param Mage_Sales_Model_Order $order
     * @return void
     */
    protected function _sendOrderEmail(Mage_Sales_Model_Order $order)
    {
        // Send new order e-mail
        if (!$order->getEmailSent()) {
            $order->setEmailSent(true);
            $order->sendNewOrderEmail();
        }
    }

    /**
     * Update the order status if changed
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $newState
     * @param string $newStatus
     * @param string $statusMessage
     * @return void
     */
    protected function _updateOrderState(Mage_Sales_Model_Order $order, $newState, $newStatus, $statusMessage)
    {
        if ($this->_isOrderUpdatable($order) &&
            (($newState != $order->getState()) || ($newStatus != $order->getStatus()))
        ) {
            // Create an invoice when the payment is completed
            if ($newState == Mage_Sales_Model_Order::STATE_PROCESSING && $this->getConfigData("autocreate_invoice")) {
                $this->_createInvoice($order);
                $this->log("Creating invoice for order ID: " . $order->getId() . ".");
            }

            $order->setState($newState, $newStatus, $statusMessage);
            $this->log("Changing state to ${newState} with message ${statusMessage} for order ID: "
                . $order->getId() . ".");
        }
    }

    /**
     * Process callback for all transactions
     *
     * @return void
     */
    public function processCallback()
    {
        $orderId = $this->getCallbackData('ref');
        if (preg_match('/.*\-([0-9]+)$/', $orderId, $matches)) {
            $orderId = $matches[1];
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($orderId);

        // Log callback
        $this->log('Callback received');
        $this->log($this->getCallbackData());

        // Validate amount
        $this->_validateAmount($order);

        $statusComplete    = $this->getConfigData("complete_status");
        $statusFailed      = $this->getConfigData("failed_status");
        $statusFraud       = $this->getConfigData("fraud_status");

        $newState      = null;
        $newStatus     = true;
        $statusMessage = '';

        switch ($this->getCallbackData('status')) {
            case "200":
                $newState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $newStatus = $statusComplete;
                $statusMessage = $this->_helper->__("Payment complete.");
                // send new email
                $this->_sendOrderEmail($order);
                break;
            case "300":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $newStatus = $statusFailed;
                $statusMessage = $this->_helper->__("Payment failed or canceled by user.");
                break;
            case "301":
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $newStatus = $statusFraud;
                $statusMessage = $this->_helper->__("Transaction failed, payment is fraud.");
                break;
        }

        // Lock
        $this->lock();

        // Update the status if changed
        $this->_updateOrderState($order, $newState, $newStatus, $statusMessage);

        // Save order status changes
        $order->save();

        // Unlock
        $this->unlock();
    }
}
