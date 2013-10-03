<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customized customers collection
 *
 * @category    Magento
 * @package     Magento_ScheduledImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\ScheduledImportExport\Model\Resource\Customer;

class Collection
    extends \Magento\Customer\Model\Resource\Customer\Collection
{
    /**
     * Additional filters to use
     *
     * @var array
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
     * @param \Magento\Reward\Model\Resource\Reward $resourceReward
     * @param \Magento\CustomerBalance\Model\Resource\Balance $resourceBalance
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Logger $logger
     * @param \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Core\Model\EntityFactory $entityFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Core\Model\Resource $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Validator\UniversalFactory $universalFactory
     * @param \Magento\Core\Model\Fieldset\Config $fieldsetConfig
     */
    public function __construct(
        \Magento\Reward\Model\Resource\Reward $resourceReward,
        \Magento\CustomerBalance\Model\Resource\Balance $resourceBalance,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Logger $logger,
        \Magento\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Core\Model\EntityFactory $entityFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Core\Model\Resource $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Validator\UniversalFactory $universalFactory,
        \Magento\Core\Model\Fieldset\Config $fieldsetConfig
    ) {
        $this->_resourceReward = $resourceReward;
        $this->_resourceBalance = $resourceBalance;
        $this->_storeManager = $storeManager;
        parent::__construct(
            $eventManager,
            $logger,
            $fetchStrategy,
            $entityFactory,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $fieldsetConfig
        );
    }

    /**
     * Join with reward points table
     *
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
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
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
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
     * @return \Magento\ScheduledImportExport\Model\Resource\Customer\Collection
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
