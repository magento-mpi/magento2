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
class Mage_Cardgate_Model_Base extends Varien_Object
{
    /**
     * Store Config object
     *
     * @var Mage_Core_Model_Store_Config
     */
    protected $_storeConfig;

    /**
     * Config object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configObject;

    /**
     * Dir object
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * Resource Transaction factory
     *
     * @var Mage_Core_Model_Resource_Transaction_Factory
     */
    protected $_resourceTransactionFactory;

    /**
     * Sales Order factory
     *
     * @var Mage_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * Logger object
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_logger;

    /**
     * Helper object
     *
     * @var Mage_Cardgate_Helper_Data
     */
    protected $_helper;

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
     */
    public function __construct(
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Model_Config $config,
        Mage_Core_Model_Dir $dir,
        Mage_Core_Model_Logger $logger,
        Mage_Core_Model_Resource_Transaction_Factory $resourceTransactionFactory,
        Mage_Sales_Model_OrderFactory $orderFactory,
        Mage_Cardgate_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct($data);

        $this->_storeConfig = $storeConfig;
        $this->_configObject = $config;
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->_resourceTransactionFactory = $resourceTransactionFactory;
        $this->_orderFactory = $orderFactory;
        $this->_helper = $helper;

        $this->_config = $this->_storeConfig->getConfig('payment/cardgate');
        if ($this->getConfigData('debug')) {
            $this->_logger->addStreamLog('cardgate', $this->_logFileName);
        }
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
     * @return string
     */
    public function getCallbackData($field = null)
    {
        if ($field === null) {
            return $this->_callback;
        } else {
            return @$this->_callback[$field];
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
        $varDir = $this->_dir->getDir(Mage_Core_Model_Dir::VAR_DIR);
        $lockFilename = $varDir . DS . $this->getCallbackData('ref') . '.lock';
        $fp = @fopen($lockFilename, 'x');

        if ($fp) {
            $this->_isLocked = true;
            $pid = getmypid();
            $now = date('Y-m-d H:i:s');
            fwrite($fp, "Locked by $pid at $now\n");
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
        $varDir = $this->_dir->getDir(Mage_Core_Model_Dir::VAR_DIR);
        $lockFilename = $varDir . DS . $this->getCallbackData('ref') . '.lock';
        @unlink($lockFilename);

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

            $this->_resourceTransactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder())
                ->save();

            $mail_invoice = $this->getConfigData("mail_invoice");
            if ($mail_invoice) {
                $invoice->setEmailSent(true);
                $invoice->save();
                $invoice->sendEmail();
            }

            $statusMessage = $mail_invoice ? "Invoice #%s created and send to customer." : "Invoice #%s created.";
            $order->addStatusToHistory(
                $order->getStatus(),
                $this->_helper->__($statusMessage, $invoice->getIncrementId()),
                $mail_invoice
            );

            return true;
        }

        return false;
    }

    /**
     * Returns true if the amounts match
     *
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
            return false;
        }

        return true;
    }

    /**
     * Process callback for all transactions
     *
     * @throws RuntimeException
     * @return void
     */
    public function processCallback()
    {
        $id = $this->getCallbackData('ref');
        if (preg_match('/.*\-([0-9]+)$/', $id, $matches)) {
            $id = $matches[1];
        }

        /** @var Mage_Sales_Model_Order $order */
        $order = $this->_orderFactory->create();
        $order->loadByIncrementId($id);

        // Log callback
        $this->log('Callback received');
        $this->log($this->getCallbackData());

        // Validate amount
        if (!$this->_validateAmount($order)) {
            throw new RuntimeException('Amount validation failed!');
        }

        $statusComplete    = $this->getConfigData("complete_status");
        $statusFailed      = $this->getConfigData("failed_status");
        $statusFraud       = $this->getConfigData("fraud_status");
        $autocreateInvoice = $this->getConfigData("autocreate_invoice");

        $complete      = false;
        $canceled      = false;
        $newState      = null;
        $newStatus     = true;
        $statusMessage = '';

        switch ($this->getCallbackData('status')) {
            case "200":
                $complete = true;
                $newState = Mage_Sales_Model_Order::STATE_PROCESSING;
                $newStatus = $statusComplete;
                $statusMessage = $this->_helper->__("Payment complete.");
                // Send new order e-mail
                if (!$order->getEmailSent()) {
                    $order->setEmailSent(true);
                    $order->sendNewOrderEmail();
                }
                break;
            case "300":
                $canceled = true;
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $newStatus = $statusFailed;
                $statusMessage = $this->_helper->__("Payment failed or canceled by user.");
                break;
            case "301":
                $canceled = true;
                $newState = Mage_Sales_Model_Order::STATE_CANCELED;
                $newStatus = $statusFraud;
                $statusMessage = $this->_helper->__("Transaction failed, payment is fraud.");
                break;
        }

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

        // Lock
        $this->lock();

        // Update the status if changed
        if ($canUpdate && (($newState != $order->getState()) || ($newStatus != $order->getStatus())) ){
            // Create an invoice when the payment is completed
            if ($complete && !$canceled && $autocreateInvoice) {
                $this->_createInvoice($order);
                $this->log("Creating invoice for order ID: $id.");
            }

            $order->setState($newState, $newStatus, $statusMessage);
            $this->log("Changing state to $newState with message $statusMessage for order ID: $id.");
        }

        // Save order status changes
        $order->save();

        // Unlock
        $this->unlock();
    }
}
