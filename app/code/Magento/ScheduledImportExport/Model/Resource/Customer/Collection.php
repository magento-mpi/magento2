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
class Magento_ScheduledImportExport_Model_Resource_Customer_Collection
    extends Magento_Customer_Model_Resource_Customer_Collection
{
    /**
     * Additional filters to use
     *
     * @var array
     */
    protected $_usedFiltersNotNull = array();

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Magento_Reward_Model_Resource_Reward
     */
    protected $_resourceReward;

    /**
     * @var Magento_CustomerBalance_Model_Resource_Balance
     */
    protected $resourceBalance;

    /**
     * @param Magento_Reward_Model_Resource_Reward $resourceReward
     * @param Magento_CustomerBalance_Model_Resource_Balance $resourceBalance
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Eav_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_Fieldset_Config $fieldsetConfig
     */
    public function __construct(
        Magento_Reward_Model_Resource_Reward $resourceReward,
        Magento_CustomerBalance_Model_Resource_Balance $resourceBalance,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $resource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Eav_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_Fieldset_Config $fieldsetConfig
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
            $helperFactory,
            $fieldsetConfig
        );
    }

    /**
     * Join with reward points table
     *
     * @return Magento_ScheduledImportExport_Model_Resource_Customer_Collection
     */
    public function joinWithRewardPoints()
    {
        $joinFlag = 'join_reward_points';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website Magento_Core_Model_Website */
            foreach ($this->_storeManager->getWebsites() as $website) {
                $tableName  = $this->_resourceReward->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.points_balance';
                $fieldAlias = $website->getCode() . '_'
                    . Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection
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
     * @return Magento_ScheduledImportExport_Model_Resource_Customer_Collection
     */
    public function joinWithCustomerBalance()
    {
        $joinFlag = 'join_customer_balance';
        if (!$this->getFlag($joinFlag)) {
            /** @var $website Magento_Core_Model_Website */
            foreach ($this->_storeManager->getWebsites() as $website) {
                $tableName  = $this->_resourceBalance->getMainTable();
                $tableAlias = $tableName . $website->getId();
                $fieldName  = $tableAlias . '.amount';
                $fieldAlias = $website->getCode() . '_'
                    . Magento_ScheduledImportExport_Model_Resource_Customer_Attribute_Finance_Collection
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
     * @return Magento_ScheduledImportExport_Model_Resource_Customer_Collection
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
