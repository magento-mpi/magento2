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
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Create;

class Payment
extends \Magento\Core\Block\Template
{
    /**
     * @var \Magento\CustomerBalance\Model\Balance
     */
    protected $_balanceInstance;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected $_orderCreate;

    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @var \Magento\CustomerBalance\Model\BalanceFactory
     */
    protected $_balanceFactory;

    /**
     * @param \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\CustomerBalance\Model\BalanceFactory $balanceFactory,
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Adminhtml\Model\Sales\Order\Create $orderCreate,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
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
     * @return \Magento\Adminhtml\Model\Sales\Order\Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->_orderCreate;
    }

    /**
     * Return store manager instance
     *
     * @return \Magento\Core\Model\StoreManager
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
            !$this->_helperFactory->get('Magento\CustomerBalance\Helper\Data')->isEnabled()
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
     * @return \Magento\CustomerBalance\Model\Balance|false
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
