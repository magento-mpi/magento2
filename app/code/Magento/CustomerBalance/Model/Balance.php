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
 * Customer balance model
 *
 * @method Magento_CustomerBalance_Model_Resource_Balance _getResource()
 * @method Magento_CustomerBalance_Model_Resource_Balance getResource()
 * @method int getCustomerId()
 * @method Magento_CustomerBalance_Model_Balance setCustomerId(int $value)
 * @method int getWebsiteId()
 * @method Magento_CustomerBalance_Model_Balance setWebsiteId(int $value)
 * @method Magento_CustomerBalance_Model_Balance setAmount(float $value)
 * @method string getBaseCurrencyCode()
 * @method Magento_CustomerBalance_Model_Balance setBaseCurrencyCode(string $value)
 * @method Magento_CustomerBalance_Model_Balance setAmountDelta() setAmountDelta(float $value)
 * @method Magento_CustomerBalance_Model_Balance setComment() setComment(string $value)
 * @method Magento_CustomerBalance_Model_Balance setCustomer() setCustomer(Magento_Customer_Model_Customer $customer)
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerBalance_Model_Balance extends Magento_Core_Model_Abstract
{
    /**
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    protected $_eventPrefix = 'customer_balance';
    protected $_eventObject = 'balance';

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_CustomerBalance_Model_Balance_HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @param Magento_CustomerBalance_Model_Balance_HistoryFactory $historyFactory
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_CustomerBalance_Model_Balance_HistoryFactory $historyFactory,
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_historyFactory = $historyFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_CustomerBalance_Model_Resource_Balance');
    }

    /**
     * Get balance amount
     *
     * @return float
     */
    public function getAmount()
    {
        return (float)$this->getData('amount');
    }

    /**
     * Load balance by customer
     * Website id should either be set or not admin
     *
     * @return Magento_CustomerBalance_Model_Balance
     * @throws Magento_Core_Exception
     */
    public function loadByCustomer()
    {
        $this->_ensureCustomer();
        if ($this->hasWebsiteId()) {
            $websiteId = $this->getWebsiteId();
        } else {
            if ($this->_storeManager->isAdmin()) {
                throw new Magento_Core_Exception(__('A website ID must be set.'));
            }
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }
        $this->getResource()->loadByCustomerAndWebsiteIds($this, $this->getCustomerId(), $websiteId);
        return $this;
    }

    /**
     * Specify whether email notification should be sent
     *
     * @param bool $shouldNotify
     * @param int $storeId
     * @return Magento_CustomerBalance_Model_Balance
     * @throws Magento_Core_Exception
     */
    public function setNotifyByEmail($shouldNotify, $storeId = null)
    {
        $this->setData('notify_by_email', $shouldNotify);
        if ($shouldNotify) {
            if (null === $storeId) {
                throw new Magento_Core_Exception(__('Please also set the Store ID.'));
            }
            $this->setStoreId($storeId);
        }
        return $this;

    }

    /**
     * Validate before saving
     *
     * @return Magento_CustomerBalance_Model_Balance
     */
    protected function _beforeSave()
    {
        $this->_ensureCustomer();

        if (0 == $this->getWebsiteId()) {
            throw new Magento_Core_Exception(__('A website ID must be set.'));
        }

        // check history action
        if (!$this->getId()) {
            $this->loadByCustomer();
            if (!$this->getId()) {
                $this->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_CREATED);
            }
        }
        if (!$this->hasHistoryAction()) {
            $this->setHistoryAction(Magento_CustomerBalance_Model_Balance_History::ACTION_UPDATED);
        }

        // check balance delta and email notification settings
        $delta = $this->_prepareAmountDelta();
        if (0 == $delta) {
            $this->setNotifyByEmail(false);
        }
        if ($this->getNotifyByEmail() && !$this->hasStoreId()) {
            throw new Magento_Core_Exception(__('The Store ID must be set to send email notifications.'));
        }

        return parent::_beforeSave();
    }

    /**
     * Update history after saving
     *
     * @return Magento_CustomerBalance_Model_Balance
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        // save history action
        if (abs($this->getAmountDelta())) {
            $this->_historyFactory->create()
                ->setBalanceModel($this)
                ->save();
        }

        return $this;
    }

    /**
     * Make sure proper customer information is set. Load customer if required
     *
     * @throws Magento_Core_Exception
     */
    protected function _ensureCustomer()
    {
        if ($this->getCustomer() && $this->getCustomer()->getId()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }
        if (!$this->getCustomerId()) {
            throw new Magento_Core_Exception(__('A customer ID must be specified.'));
        }
        if (!$this->getCustomer()) {
            $this->setCustomer(
                $this->_customerFactory->create()->load($this->getCustomerId())
            );
        }
        if (!$this->getCustomer()->getId()) {
            throw new Magento_Core_Exception(__('This customer is not set or does not exist.'));
        }
    }

    /**
     * Validate & adjust amount change
     *
     * @return float
     */
    protected function _prepareAmountDelta()
    {
        $result = 0;
        if ($this->hasAmountDelta()) {
            $result = (float)$this->getAmountDelta();
            if ($this->getId()) {
                if (($result < 0) && (($this->getAmount() + $result) < 0)) {
                    $result = -1 * $this->getAmount();
                }
            } elseif ($result <= 0) {
                $result = 0;
            }
        }
        $this->setAmountDelta($result);
        if (!$this->getId()) {
            $this->setAmount($result);
        } else {
            $this->setAmount($this->getAmount() + $result);
        }
        return $result;
    }

    /**
     * Check whether balance completely covers specified quote
     *
     * @param Magento_Sales_Model_Quote $quote
     * @return bool
     */
    public function isFullAmountCovered(Magento_Sales_Model_Quote $quote, $isEstimation = false)
    {
        if (!$isEstimation && !$quote->getUseCustomerBalance()) {
            return false;
        }
        return $this->getAmount() >=
            ((float)$quote->getBaseGrandTotal() + (float)$quote->getBaseCustomerBalAmountUsed());
    }

    /**
     * Update customers balance currency code per website id
     *
     * @param int $websiteId
     * @param string $currencyCode
     * @return Magento_CustomerBalance_Model_Balance
     */
    public function setCustomersBalanceCurrencyTo($websiteId, $currencyCode)
    {
        $this->getResource()->setCustomersBalanceCurrencyTo($websiteId, $currencyCode);
        return $this;
    }

    /**
     * Delete customer orphan balances
     *
     * @param int $customerId
     * @return Magento_CustomerBalance_Model_Balance
     */
    public function deleteBalancesByCustomerId($customerId)
    {
        $this->getResource()->deleteBalancesByCustomerId($customerId);
        return $this;
    }

    /**
     * Get customer orphan balances count
     *
     * @return Magento_CustomerBalance_Model_Balance
     */
    public function getOrphanBalancesCount($customerId)
    {
        return $this->getResource()->getOrphanBalancesCount($customerId);
    }

    /**
     * Public version of afterLoad
     *
     * @return Magento_Core_Model_Abstract
     */
    public function afterLoad()
    {
        return $this->_afterLoad();
    }
}
