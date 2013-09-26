<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance block for order creation page
 *
 */
class Magento_CustomerBalance_Block_Adminhtml_Sales_Order_Create_Payment
extends Magento_Core_Block_Template
{
    /**
     * @var Magento_CustomerBalance_Model_Balance
     */
    protected $_balanceInstance;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected $_orderCreate;

    /**
     * @var Magento_Adminhtml_Model_Session_Quote
     */
    protected $_sessionQuote;

    /**
     * @var Magento_CustomerBalance_Model_BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param Magento_CustomerBalance_Model_BalanceFactory $balanceFactory
     * @param Magento_Adminhtml_Model_Session_Quote $sessionQuote
     * @param Magento_Adminhtml_Model_Sales_Order_Create $orderCreate
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Model_BalanceFactory $balanceFactory,
        Magento_Adminhtml_Model_Session_Quote $sessionQuote,
        Magento_Adminhtml_Model_Sales_Order_Create $orderCreate,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_balanceFactory = $balanceFactory;
        $this->_sessionQuote = $sessionQuote;
        $this->_orderCreate = $orderCreate;
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Retrieve order create model
     *
     * @return Magento_Adminhtml_Model_Sales_Order_Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_orderCreate;
    }

    /**
     * Return store manager instance
     *
     * @return Magento_Core_Model_StoreManager
     */
    protected function _getStoreManagerModel()
    {
        return $this->_storeManager;
    }

    /**
     * Format value as price
     *
     * @param double $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->_sessionQuote->getStore()->formatPrice($value);
    }

    /**
     * Balance getter
     *
     * @param bool $convertPrice
     * @return float
     */
    public function getBalance($convertPrice = false)
    {
        if (
            !$this->_helperFactory->get('Magento_CustomerBalance_Helper_Data')->isEnabled()
            || !$this->_getBalanceInstance()
        ) {
            return 0.0;
        }
        if ($convertPrice) {
            return $this->_getStoreManagerModel()->getStore($this->_getOrderCreateModel()->getQuote()->getStoreId())
                ->convertPrice($this->_getBalanceInstance()->getAmount());
        }
        return $this->_getBalanceInstance()->getAmount();
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function getUseCustomerBalance()
    {
        return $this->_orderCreate->getQuote()->getUseCustomerBalance();
    }

    /**
     * Check whether customer balance fully covers quote
     *
     * @return bool
     */
    public function isFullyPaid()
    {
        if (!$this->_getBalanceInstance()) {
            return false;
        }
        return $this->_getBalanceInstance()->isFullAmountCovered($this->_orderCreate->getQuote());
    }

    /**
     * Check whether quote uses customer balance
     *
     * @return bool
     */
    public function isUsed()
    {
        return $this->getUseCustomerBalance();
    }

    /**
     * Instantiate/load balance and return it
     *
     * @return Magento_CustomerBalance_Model_Balance|false
     */
    protected function _getBalanceInstance()
    {
        if (!$this->_balanceInstance) {
            $quote = $this->_orderCreate->getQuote();
            if (!$quote || !$quote->getCustomerId() || !$quote->getStoreId()) {
                return false;
            }

            $store = $this->_storeManager->getStore($quote->getStoreId());
            $this->_balanceInstance = $this->_balanceFactory->create()
                ->setCustomerId($quote->getCustomerId())
                ->setWebsiteId($store->getWebsiteId())
                ->loadByCustomer();
        }
        return $this->_balanceInstance;
    }

    /**
     * Whether customer store credit balance could be used
     *
     * @return bool
     */
    public function canUseCustomerBalance()
    {
        $quote = $this->_orderCreate->getQuote();
        return $this->getBalance() && ($quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() > 0);
    }
}
