<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment transaction model
 * Tracks transaction history
 *
 * @method \Magento\Paypal\Model\Resource\Payment\Transaction _getResource()
 * @method \Magento\Paypal\Model\Resource\Payment\Transaction getResource()
 * @method string getTxnId()
 * @method string getCreatedAt()
 * @method \Magento\Paypal\Model\Payment\Transaction setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Model\Payment;

class Transaction extends \Magento\Core\Model\AbstractModel
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
     * @see Magento_Core_Model_Absctract::$_eventPrefix
     * @var string
     */
    protected $_eventPrefix = 'paypal_payment_transaction';

    /**
     * Event object prefix
     *
     * @see Magento_Core_Model_Absctract::$_eventObject
     * @var string
     */
    protected $_eventObject = 'paypal_payment_transaction';

    /**
     * Order website id
     *
     * @var int
     */
    protected $_orderWebsiteId = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Paypal\Model\Resource\Payment\Transaction');
        return parent::_construct();
    }

    /**
     * Transaction ID setter
     * @param string $txnId
     * @return \Magento\Paypal\Model\Payment\Transaction
     */
    public function setTxnId($txnId)
    {
        $this->_verifyTxnId($txnId);
        return $this->setData('txn_id', $txnId);
    }

    /**
     * Check object before loading by by specified transaction ID
     * @param $txnId
     * @return \Magento\Paypal\Model\Payment\Transaction
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
     * @param string $txnId
     * @return \Magento\Paypal\Model\Payment\Transaction
     */
    public function loadByTxnId($txnId)
    {
        $this->_beforeLoadByTxnId($txnId);
        $this->getResource()->loadObjectByTxnId(
            $this, $txnId
        );
        $this->_afterLoadByTxnId();
        return $this;
    }

    /**
     * Check object after loading by by specified transaction ID
     * @param $txnId
     * @return \Magento\Paypal\Model\Payment\Transaction
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
     * @return Magento_Paypal_Model_Order_Payment_Transaction
     * @throws \Magento\Core\Exception
     */
    public function setAdditionalInformation($key, $value)
    {
        if (is_object($value)) {
            \Mage::throwException(__('Payment transactions disallow storing objects.'));
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
     * @param string $key
     * @return array|null|mixed
     */
    public function getAdditionalInformation($key = null)
    {
        $info = $this->_getData('additional_information');
        if (!$info) {
            $info = array();
        }
        if ($key) {
            return (isset($info[$key]) ? $info[$key] : null);
        }
        return $info;
    }

    /**
     * Unsetter for entire additional_information value or one of its element by key
     * @param string $key
     * @return \Magento\Paypal\Model\Payment\Transaction
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
     * @param bool $failsafe
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
     * @return \Magento\Paypal\Model\Payment\Transaction
     * @throws \Magento\Core\Exception
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setCreatedAt(\Mage::getModel('Magento\Core\Model\Date')->gmtDate());
        }
        return parent::_beforeSave();
    }

    /**
     * Check whether specified transaction ID is valid
     * @param string $txnId
     * @throws \Magento\Core\Exception
     */
    protected function _verifyTxnId($txnId)
    {
        if (null !== $txnId && 0 == strlen($txnId)) {
            \Mage::throwException(__('You need to enter a transaction ID.'));
        }
    }

    /**
     * Make sure this object is a valid transaction
     * TODO for more restriction we can check for data consistency
     * @throws \Magento\Core\Exception
     */
    protected function _verifyThisTransactionExists()
    {
        if (!$this->getId()) {
            \Mage::throwException(__('This operation requires an existing transaction object.'));
        }
    }
}
