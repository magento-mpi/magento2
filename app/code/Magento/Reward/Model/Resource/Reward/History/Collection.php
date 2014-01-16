<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward history collection
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Model\Resource\Reward\History;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Expiry config
     *
     * @var array
     */
    protected $_expiryConfig     = array();

    /**
     * @var \Magento\Core\Model\Locale
     */
    protected $_locale;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\Locale $locale
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param mixed $connection
     * @param \Magento\Core\Model\Resource\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\Locale $locale,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Stdlib\DateTime $dateTime,
        $connection = null,
        \Magento\Core\Model\Resource\Db\AbstractDb $resource = null
    ) {
        $this->_locale = $locale;
        $this->_customerFactory = $customerFactory;
        $this->dateTime = $dateTime;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }


    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\Reward\Model\Reward\History', 'Magento\Reward\Model\Resource\Reward\History');
    }

    /**
     * Unserialize fields of each loaded collection item
     *
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $item) {
            $this->getResource()->unserializeFields($item);
        }
        return parent::_afterLoad();
    }

    /**
     * Join reward table and retrieve total balance total with customer_id
     *
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    protected function _joinReward()
    {
        if ($this->getFlag('reward_joined')) {
            return $this;
        }
        $this->getSelect()->joinInner(
            array('reward_table' => $this->getTable('magento_reward')),
            'reward_table.reward_id = main_table.reward_id',
            array('customer_id', 'points_balance_total' => 'points_balance')
        );
        $this->setFlag('reward_joined', true);
        return $this;
    }

    /**
     * Getter for $_expiryConfig
     *
     * @param int $websiteId Specified Website Id
     * @return array|\Magento\Object
     */
    protected function _getExpiryConfig($websiteId = null)
    {
        if ($websiteId !== null && isset($this->_expiryConfig[$websiteId])) {
            return $this->_expiryConfig[$websiteId];
        }
        return $this->_expiryConfig;
    }

    /**
     * Setter for $_expiryConfig
     *
     * @param array $config
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function setExpiryConfig($config)
    {
        if (!is_array($config)) {
            return $this;
        }
        $this->_expiryConfig = $config;
        return $this;
    }

    /**
     * Join reward table to filter history by customer id
     *
     * @param string $customerId
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function addCustomerFilter($customerId)
    {
        if ($customerId) {
            $this->_joinReward();
            $this->getSelect()->where('reward_table.customer_id = ?', $customerId);
        }
        return $this;
    }

    /**
     * Skip Expired duplicates records (with action = -1)
     *
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function skipExpiredDuplicates()
    {
        $this->getSelect()->where('main_table.is_duplicate_of IS NULL');
        return $this;
    }

    /**
     * Add filter by website id
     *
     * @param integer|array $websiteId
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function addWebsiteFilter($websiteId)
    {
        $this->getSelect()->where(
            is_array($websiteId) ? 'main_table.website_id IN (?)' : 'main_table.website_id = ?', $websiteId
        );
        return $this;
    }

    /**
     * Join additional customer information, such as email, name etc.
     *
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function addCustomerInfo()
    {
        if ($this->getFlag('customer_added')) {
            return $this;
        }

        $this->_joinReward();

        $customer = $this->_customerFactory->create();
        /* @var $customer \Magento\Customer\Model\Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        $warningNotification = $customer->getAttribute('reward_warning_notification');

        $connection = $this->getConnection();
        /* @var $connection \Zend_Db_Adapter_Abstract */

        $this->getSelect()
            ->joinInner(
                array('ce' => $customer->getAttribute('email')->getBackend()->getTable()),
                'ce.entity_id=reward_table.customer_id',
                array('customer_email' => 'email')
            )
            ->joinInner(
                array('cg' => $customer->getAttribute('group_id')->getBackend()->getTable()),
                'cg.entity_id=reward_table.customer_id',
                array('customer_group_id' => 'group_id')
            )
            ->joinLeft(
                array('clt' => $lastname->getBackend()->getTable()),
                $connection->quoteInto('clt.entity_id=reward_table.customer_id AND clt.attribute_id = ?',
                    $lastname->getAttributeId()),
                array('customer_lastname' => 'value')
            )
            ->joinLeft(
                array('cft' => $firstname->getBackend()->getTable()),
                $connection->quoteInto(
                    'cft.entity_id=reward_table.customer_id AND cft.attribute_id = ?',
                    $firstname->getAttributeId()
                ),
                array('customer_firstname' => 'value')
            )
            ->joinLeft(
                array('warning_notification' => $warningNotification->getBackend()->getTable()),
                $connection->quoteInto(
                    'warning_notification.entity_id=reward_table.customer_id AND warning_notification.attribute_id = ?',
                    $warningNotification->getAttributeId()
                ),
                array('reward_warning_notification' => 'value')
            );

        $this->setFlag('customer_added', true);
        return $this;
    }

    /**
     * Add correction to expiration date based on expiry calculation
     * CASE ... WHEN ... THEN is used only in admin area to show expiration date for all stores
     *
     * @param int $websiteId
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function addExpirationDate($websiteId = null)
    {
        $expiryConfig = $this->_getExpiryConfig($websiteId);
        $adapter = $this->getConnection();
        if (!$expiryConfig) {
            return $this;
        }

        if ($websiteId !== null) {
            $field = $expiryConfig->getExpiryCalculation()== 'static' ? 'expired_at_static' : 'expired_at_dynamic';
            $this->getSelect()->columns(array('expiration_date' => $field));
        } else {
            $cases = array();
            foreach ($expiryConfig as $wId => $config) {
                $field = $config->getExpiryCalculation()== 'static' ? 'expired_at_static' : 'expired_at_dynamic';
                $cases[$wId] = $field;
            }

            if (count($cases) > 0) {
                $sql = $adapter->getCaseSql('main_table.website_id', $cases);
                $this->getSelect()->columns(array('expiration_date' => new \Zend_Db_Expr($sql)));
            }
        }

        return $this;
    }

    /**
     * Return total amounts of points that will be expired soon (pre-configured days value) for specified website
     * Result is grouped by customer
     *
     * @param int $websiteId Specified Website
     * @param bool $subscribedOnly Whether to load expired soon points only for subscribed customers
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function loadExpiredSoonPoints($websiteId, $subscribedOnly = true)
    {
        $expiryConfig = $this->_getExpiryConfig($websiteId);
        if (!$expiryConfig) {
            return $this;
        }
        $inDays = (int)$expiryConfig->getExpiryDayBefore();
        // Empty Value disables notification
        if (!$inDays) {
            return $this;
        }

        // join info about current balance and filter records by website
        $this->_joinReward();
        $this->addWebsiteFilter($websiteId);

        $field = $expiryConfig->getExpiryCalculation()== 'static' ? 'expired_at_static' : 'expired_at_dynamic';
        $locale = $this->_locale->getLocale();
        $expireAtLimit = new \Zend_Date($locale);
        $expireAtLimit->addDay($inDays);
        $expireAtLimit = $this->dateTime->formatDate($expireAtLimit);

        $this->getSelect()
            ->columns(
                array('total_expired' => new \Zend_Db_Expr('SUM(points_delta-points_used)'))
            )
            ->where('points_delta-points_used > 0')
            ->where('is_expired=0')
            ->where("{$field} IS NOT NULL") // expire_at - BEFORE_DAYS < NOW
            ->where("{$field} < ?", $expireAtLimit) // eq. expire_at - BEFORE_DAYS < NOW
            ->group(array('reward_table.customer_id', 'main_table.store_id'));

        if ($subscribedOnly) {
            $this->addCustomerInfo();
            $this->getSelect()->where('warning_notification.value=1');
        }

        $this->setFlag('expired_soon_points_loaded', true);

        return $this;
    }

    /**
     * Add filter for notification_sent field
     *
     * @param bool $flag
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function addNotificationSentFlag($flag)
    {
        $this->addFieldToFilter('notification_sent', (bool)$flag ? 1 : 0);
        return $this;
    }

    /**
     * Return array of history ids records that will be expired.
     * Required loadExpiredSoonPoints() call first, based on its select object
     *
     * @return array|bool
     */
    public function getExpiredSoonIds()
    {
        if (!$this->getFlag('expired_soon_points_loaded')) {
            return array();
        }

        $additionalWhere = array();
        foreach ($this as $item) {
            $where = array(
                $this->getConnection()->quoteInto('reward_table.customer_id=?', $item->getCustomerId()),
                $this->getConnection()->quoteInto('main_table.store_id=?', $item->getStoreId())
            );
            $additionalWhere[] = '(' . implode(' AND ', $where). ')';
        }
        if (count($additionalWhere) == 0) {
            return array();
        }
        // filter rows by customer and store, as result of grouped query
        $where = new \Zend_Db_Expr(implode(' OR ', $additionalWhere));

        $select = clone $this->getSelect();
        $select->reset(\Zend_Db_Select::COLUMNS)
            ->columns('history_id')
            ->reset(\Zend_Db_Select::GROUP)
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->where($where);

        return $this->getConnection()->fetchCol($select);
    }

    /**
     * Order by primary key desc
     *
     * @return \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    public function setDefaultOrder()
    {
        $this->getSelect()->reset(\Zend_Db_Select::ORDER);

        return $this
            ->addOrder('created_at', 'DESC')
            ->addOrder('history_id', 'DESC');
    }
}
