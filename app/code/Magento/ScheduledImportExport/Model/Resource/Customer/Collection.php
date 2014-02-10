<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ScheduledImportExport\Model\Resource\Customer;

/**
 * Customized customers collection
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * Additional filters to use
     *
     * @var string[]
     */
    protected $_usedFiltersNotNull = array();

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Reward\Model\Resource\Reward
     */
    protected $_resourceReward;

    /**
     * @var \Magento\CustomerBalance\Model\Resource\Balance
     */
    protected $resourceBalance;

    /**
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\App\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Object\Copy\Config $fieldsetConfig
     * @param \Magento\Reward\Model\Resource\Reward $resourceReward
     * @param \Magento\CustomerBalance\Model\Resource\Balance $resourceBalance
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param mixed $connection
     * @param string $modelName
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\App\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Object\Copy\Config $fieldsetConfig,
        \Magento\Reward\Model\Resource\Reward $resourceReward,
        \Magento\CustomerBalance\Model\Resource\Balance $resourceBalance,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        $connection = null,
        $modelName = self::CUSTOMER_MODEL_NAME
    ) {
        $this->_resourceReward = $resourceReward;
        $this->_resourceBalance = $resourceBalance;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $fieldsetConfig,
            $connection,
            $modelName
        );
    }

    /**
     * Join with reward points table
     *
     * @return $this
     */
    public function joinWithRewardPoints()
    {
        $joinFlag = 'join_reward_points';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website \Magento\Core\Model\Website */
            foreach ($this->_storeManager->getWebsites() as $website) {
                $tableName  = $this->_resourceReward->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.points_balance';
                $fieldAlias = $website->getCode() . '_'
                    . \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
                    ::COLUMN_REWARD_POINTS;

                $this->joinTable(
                    array($tableAlias => $tableName),
                    'customer_id = entity_id',
                    array($fieldAlias => $fieldName),
                    array('website_id' => $website->getId()),
                    'left'
                );

                $this->_usedFiltersNotNull[] = $fieldName;
            }
            $this->setFlag($joinFlag, true);
        }

        return $this;
    }

    /**
     * Join with store credit table
     *
     * @return $this
     */
    public function joinWithCustomerBalance()
    {
        $joinFlag = 'join_customer_balance';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website \Magento\Core\Model\Website */
            foreach ($this->_storeManager->getWebsites() as $website) {
                $tableName  = $this->_resourceBalance->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.amount';
                $fieldAlias = $website->getCode() . '_'
                    . \Magento\ScheduledImportExport\Model\Resource\Customer\Attribute\Finance\Collection
                    ::COLUMN_CUSTOMER_BALANCE;

                $this->joinTable(
                    array($tableAlias => $tableName),
                    'customer_id = entity_id',
                    array($fieldAlias => $fieldName),
                    array('website_id' => $website->getId()),
                    'left'
                );

                $this->_usedFiltersNotNull[] = $fieldName;
            }
            $this->setFlag($joinFlag, true);
        }

        return $this;
    }

    /**
     * Additional filters
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        if ($this->_usedFiltersNotNull) {
            $filterArray = array();
            foreach ($this->_usedFiltersNotNull as $filter) {
                $filterArray[] = $this->getSelect()
                    ->getAdapter()
                    ->prepareSqlCondition($filter, array('notnull' => true));
            }
            $conditionStr = implode(' OR ', $filterArray);
            $this->getSelect()->where($conditionStr);
        }

        return parent::_beforeLoad();
    }
}
