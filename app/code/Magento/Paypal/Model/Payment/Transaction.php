<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\Payment;

/**
 * Payment transaction model
 * Tracks transaction history
 *
 * @method string getTxnId()
 * @method string getCreatedAt()
 * @method \Magento\Paypal\Model\Payment\Transaction setCreatedAt(string $value)
 */
class Transaction extends \Magento\Model\AbstractModel
{
    /**
     * Whether to throw exceptions on different operations
     *
     * @var bool
     */
    protected $_isFailsafe = false;

    /**
     * Event object prefix
     *
     * @var string
     * @see \Magento\Core\Model\Absctract::$_eventPrefix
     */
    protected $_eventPrefix = 'paypal_payment_transaction';

    /**
     * Event object prefix
     *
     * @var string
     * @see \Magento\Core\Model\Absctract::$_eventObject
     */
    protected $_eventObject = 'paypal_payment_transaction';

    /**
     * Order website id
     *
     * @var int
     */
    protected $_orderWebsiteId;

    /**
     * @var \Magento\Stdlib\DateTime\DateTimeFactory
     */
    protected $_dateFactory;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_dateFactory = $dateFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Resource\Payment\Transaction');
        return parent::_construct();
    }

    /**
     * Transaction ID setter
     *
     * @param string $txnId
     * @return $this
     */
    public function setTxnId($txnId)
    {
        $this->_verifyTxnId($txnId);
        return $this->setData('txn_id', $txnId);
    }

    /**
     * Check object before loading by by specified transaction ID
     *
     * @param string $txnId
     * @return $this
     */
    protected function _beforeLoadByTxnId($txnId)
    {
        $this->_eventManager->dispatch(
            $this->_eventPrefix . '_load_by_txn_id_before',
            $this->_getEventData() + array('txn_id' => $txnId)
        );
        return $this;
    }

    /**
     * Load self by specified transaction ID. Requires the valid payment object to be set
     *
     * @param string $txnId
     * @return $this
     */
    public function loadByTxnId($txnId)
    {
        $this->_beforeLoadByTxnId($txnId);
        $this->getResource()->loadObjectByTxnId($this, $txnId);
        $this->_afterLoadByTxnId();
        return $this;
    }

    /**
     * Check object after loading by by specified transaction ID
     *
     * @return $this
     */
    protected function _afterLoadByTxnId()
    {
        $this->_eventManager->dispatch($this->_eventPrefix . '_load_by_txn_id_after', $this->_getEventData());
        return $this;
    }

    /**
     * Additional information setter
     * Updates data inside the 'additional_information' array
     * Doesn't allow to set arrays
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     * @throws \Magento\Model\Exception
     */
    public function setAdditionalInformation($key, $value)
    {
        if (is_object($value)) {
            throw new \Magento\Model\Exception(__('Payment transactions disallow storing objects.'));
        }
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = array();
        }
        $info[$key] = $value;
        return $this->setData('additional_information', $info);
    }

    /**
     * Getter for entire additional_information value or one of its element by key
     * 
     * @param string|null $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = array();
        }
        if ($key) {
            return isset($info[$key]) ? $info[$key] : null;
        }
        return $info;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     *
     * @param string|null $key
     * @return $this
     */
    public function unsAdditionalInformation($key = null)
    {
        if ($key) {
            $info = $this->_getData('additional_information');
            if (is_array($info)) {
                unset($info[$key]);
            }
        } else {
            $info = array();
        }
        return $this->setData('additional_information', $info);
    }

    /**
     * Setter/Getter whether transaction is supposed to prevent exceptions on saving
     *
     * @param bool|null $setFailsafe
     * @return $this|bool
     */
    public function isFailsafe($setFailsafe = null)
    {
        if (null === $setFailsafe) {
            return $this->_isFailsafe;
        }
        $this->_isFailsafe = (bool)$setFailsafe;
        return $this;
    }

    /**
     * Verify data required for saving
     *
     * @return $this
     * @throws \Magento\Model\Exception
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setCreatedAt($this->_dateFactory->create()->gmtDate());
        }
        return parent::_beforeSave();
    }

    /**
     * Check whether specified transaction ID is valid
     *
     * @param string $txnId
     * @return void
     * @throws \Magento\Model\Exception
     */
    protected function _verifyTxnId($txnId)
    {
        if (null !== $txnId && 0 == strlen($txnId)) {
            throw new \Magento\Model\Exception(__('You need to enter a transaction ID.'));
        }
    }

    /**
     * Make sure this object is a valid transaction
     *
     * TODO for more restriction we can check for data consistency
     *
     * @return void
     * @throws \Magento\Model\Exception
     */
    protected function _verifyThisTransactionExists()
    {
        if (!$this->getId()) {
            throw new \Magento\Model\Exception(__('This operation requires an existing transaction object.'));
        }
    }
}
