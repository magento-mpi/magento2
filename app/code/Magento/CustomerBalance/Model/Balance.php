<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Model;

use Magento\Core\Exception;
/**
 * Customer balance model
 *
 * @method \Magento\CustomerBalance\Model\Resource\Balance _getResource()
 * @method \Magento\CustomerBalance\Model\Resource\Balance getResource()
 * @method int getCustomerId()
 * @method \Magento\CustomerBalance\Model\Balance setCustomerId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\CustomerBalance\Model\Balance setWebsiteId(int $value)
 * @method \Magento\CustomerBalance\Model\Balance setAmount(float $value)
 * @method string getBaseCurrencyCode()
 * @method \Magento\CustomerBalance\Model\Balance setBaseCurrencyCode(string $value)
 * @method \Magento\CustomerBalance\Model\Balance setAmountDelta() setAmountDelta(float $value)
 * @method \Magento\CustomerBalance\Model\Balance setComment() setComment(string $value)
 * @method \Magento\CustomerBalance\Model\Balance setCustomer() setCustomer(\Magento\Customer\Model\Customer $customer)
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance extends \Magento\Core\Model\AbstractModel
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var string
     */
    protected $_eventPrefix = 'customer_balance';

    /**
     * @var string
     */
    protected $_eventObject = 'balance';

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\CustomerBalance\Model\Balance\HistoryFactory
     */
    protected $_historyFactory;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\CustomerBalance\Model\Balance\HistoryFactory $historyFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
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
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CustomerBalance\Model\Resource\Balance');
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
     * @return $this
     * @throws Exception
     */
    public function loadByCustomer()
    {
        $this->_ensureCustomer();
        $this->getResource()->loadByCustomerAndWebsiteIds($this, $this->getCustomerId(), $this->getWebsiteId());
        return $this;
    }

    /**
     * Get website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        if ($this->hasWebsiteId()) {
            return $this->_getData('website_id');
        }
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Specify whether email notification should be sent
     *
     * @param bool $shouldNotify
     * @param int|null $storeId
     * @return $this
     * @throws Exception
     */
    public function setNotifyByEmail($shouldNotify, $storeId = null)
    {
        $this->setData('notify_by_email', $shouldNotify);
        if ($shouldNotify) {
            if (null === $storeId) {
                throw new Exception(__('Please also set the Store ID.'));
            }
            $this->setStoreId($storeId);
        }
        return $this;

    }

    /**
     * Validate before saving
     *
     * @return $this
     * @throws Exception
     */
    protected function _beforeSave()
    {
        $this->_ensureCustomer();

        if (0 == $this->getWebsiteId()) {
            throw new Exception(__('A website ID must be set.'));
        }

        // check history action
        if (!$this->getId()) {
            $this->loadByCustomer();
            if (!$this->getId()) {
                $this->setHistoryAction(\Magento\CustomerBalance\Model\Balance\History::ACTION_CREATED);
            }
        }
        if (!$this->hasHistoryAction()) {
            $this->setHistoryAction(\Magento\CustomerBalance\Model\Balance\History::ACTION_UPDATED);
        }

        // check balance delta and email notification settings
        $delta = $this->_prepareAmountDelta();
        if (0 == $delta) {
            $this->setNotifyByEmail(false);
        }
        if ($this->getNotifyByEmail() && !$this->hasStoreId()) {
            throw new Exception(__('The Store ID must be set to send email notifications.'));
        }

        return parent::_beforeSave();
    }

    /**
     * Update history after saving
     *
     * @return $this
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
     * @return void
     * @throws Exception
     */
    protected function _ensureCustomer()
    {
        if ($this->getCustomer() && $this->getCustomer()->getId()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }
        if (!$this->getCustomerId()) {
            throw new Exception(__('A customer ID must be specified.'));
        }
        if (!$this->getCustomer()) {
            $this->setCustomer(
                $this->_customerFactory->create()->load($this->getCustomerId())
            );
        }
        if (!$this->getCustomer()->getId()) {
            throw new Exception(__('This customer is not set or does not exist.'));
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
     * @param \Magento\Sales\Model\Quote $quote
     * @param bool $isEstimation
     * @return bool
     */
    public function isFullAmountCovered(\Magento\Sales\Model\Quote $quote, $isEstimation = false)
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
     * @return $this
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
     * @return $this
     */
    public function deleteBalancesByCustomerId($customerId)
    {
        $this->getResource()->deleteBalancesByCustomerId($customerId);
        return $this;
    }

    /**
     * Get customer orphan balances count
     *
     * @param int $customerId
     * @return $this
     */
    public function getOrphanBalancesCount($customerId)
    {
        return $this->getResource()->getOrphanBalancesCount($customerId);
    }

    /**
     * Public version of afterLoad
     *
     * @return $this
     */
    public function afterLoad()
    {
        return $this->_afterLoad();
    }
}
