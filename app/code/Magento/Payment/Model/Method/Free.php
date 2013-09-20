<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Free payment method
 */
class Magento_Payment_Model_Method_Free extends Magento_Payment_Model_Method_Abstract
{
    /**
     * XML Paths for configuration constants
     */
    const XML_PATH_PAYMENT_FREE_ACTIVE = 'payment/free/active';
    const XML_PATH_PAYMENT_FREE_ORDER_STATUS = 'payment/free/order_status';
    const XML_PATH_PAYMENT_FREE_PAYMENT_ACTION = 'payment/free/payment_action';

    /**
     * Payment Method features
     * @var bool
     */
    protected $_canAuthorize = true;

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'free';

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Store_Config $coreStoreConfig
     * @param Magento_Core_Model_Log_AdapterFactory $logAdapterFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_Log_AdapterFactory $logAdapterFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $logAdapterFactory, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Check whether method is available
     *
     * @param Magento_Sales_Model_Quote|null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return parent::isAvailable($quote) && !empty($quote)
            && $this->_storeManager->getStore()->roundPrice($quote->getGrandTotal()) == 0;
    }

    /**
     * Get config payment action, do nothing if status is pending
     *
     * @return string|null
     */
    public function getConfigPaymentAction()
    {
        return $this->getConfigData('order_status') == 'pending' ? null : parent::getConfigPaymentAction();
    }
}
