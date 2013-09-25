<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog EAV collection resource abstract model
 * Implement using different stores for retrieve attribute values
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Collection_Abstract extends Magento_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $coreResource
     * @param Magento_Eav_Model_EntityFactory $eavEntityFactory
     * @param Magento_Eav_Model_Resource_Helper $resourceHelper
     * @param Magento_Validator_UniversalFactory $universalFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $coreResource,
        Magento_Eav_Model_EntityFactory $eavEntityFactory,
        Magento_Eav_Model_Resource_Helper $resourceHelper,
        Magento_Validator_UniversalFactory $universalFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $eavConfig, $coreResource,
            $eavEntityFactory, $resourceHelper, $universalFactory);
    }

    /**
     * Set store scope
     *
     * @param int|string|Magento_Core_Model_Store $store
     * @return Magento_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStore($store)
    {
        $this->setStoreId($this->_storeManager->getStore($store)->getId());
        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|Magento_Core_Model_Store $storeId
     * @return Magento_Catalog_Model_Resource_Collection_Abstract
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof Magento_Core_Model_Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID;
    }

    /**
     * Retrieve attributes load select
     *
     * @param string $table
     * @param array|int $attributeIds
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getLoadAttributesSelect($table, $attributeIds = array())
    {
        if (empty($attributeIds)) {
            $attributeIds = $this->_selectAttributes;
        }
        $storeId = $this->getStoreId();

        if ($storeId) {

            $adapter        = $this->getConnection();
            $entityIdField  = $this->getEntity()->getEntityIdField();
            $joinCondition  = array(
                't_s.attribute_id = t_d.attribute_id',
                't_s.entity_id = t_d.entity_id',
                $adapter->quoteInto('t_s.store_id = ?', $storeId)
            );
            $select = $adapter->select()
                ->from(array('t_d' => $table), array($entityIdField, 'attribute_id'))
                ->joinLeft(
                    array('t_s' => $table),
                    implode(' AND ', $joinCondition),
                    array())
                ->where('t_d.entity_type_id = ?', $this->getEntity()->getTypeId())
                ->where("t_d.{$entityIdField} IN (?)", array_keys($this->_itemsById))
                ->where('t_d.attribute_id IN (?)', $attributeIds)
                ->where('t_d.store_id = ?', 0);
        } else {
            $select = parent::_getLoadAttributesSelect($table)
                ->where('store_id = ?', $this->getDefaultStoreId());
        }

        return $select;
    }

    /**
     * @param Magento_DB_Select $select
     * @param string $table
     * @param string $type
     * @return Magento_DB_Select
     */
    protected function _addLoadAttributesSelectValues($select, $table, $type)
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            $adapter        = $this->getConnection();
            $valueExpr      = $adapter->getCheckSql(
                't_s.value_id IS NULL',
                't_d.value',
                't_s.value'
            );

            $select->columns(array(
                'default_value' => 't_d.value',
                'store_value'   => 't_s.value',
                'value'         => $valueExpr
            ));
        } else {
            $select = parent::_addLoadAttributesSelectValues($select, $table, $type);
        }
        return $select;
    }

    /**
     * Adding join statement to collection select instance
     *
     * @param string $method
     * @param object $attribute
     * @param string $tableAlias
     * @param array $condition
     * @param string $fieldCode
     * @param string $fieldAlias
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    protected function _joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias)
    {
        if (isset($this->_joinAttributes[$fieldCode]['store_id'])) {
            $storeId = $this->_joinAttributes[$fieldCode]['store_id'];
        } else {
            $storeId = $this->getStoreId();
        }

        $adapter = $this->getConnection();

        if ($storeId != $this->getDefaultStoreId() && !$attribute->isScopeGlobal()) {
            /**
             * Add joining default value for not default store
             * if value for store is null - we use default value
             */
            $defCondition = '(' . implode(') AND (', $condition) . ')';
            $defAlias     = $tableAlias . '_default';
            $defAlias     = $this->getConnection()->getTableName($defAlias);
            $defFieldAlias= str_replace($tableAlias, $defAlias, $fieldAlias);
            $tableAlias   = $this->getConnection()->getTableName($tableAlias);

            $defCondition = str_replace($tableAlias, $defAlias, $defCondition);
            $defCondition.= $adapter->quoteInto(
                " AND " . $adapter->quoteColumnAs("$defAlias.store_id", null) . " = ?",
                $this->getDefaultStoreId());

            $this->getSelect()->$method(
                array($defAlias => $attribute->getBackend()->getTable()),
                $defCondition,
                array()
            );

            $method = 'joinLeft';
            $fieldAlias = $this->getConnection()->getCheckSql("{$tableAlias}.value_id > 0",
                $fieldAlias, $defFieldAlias);
            $this->_joinAttributes[$fieldCode]['condition_alias'] = $fieldAlias;
            $this->_joinAttributes[$fieldCode]['attribute']       = $attribute;
        } else {
            $storeId = $this->getDefaultStoreId();
        }
        $condition[] = $adapter->quoteInto(
            $adapter->quoteColumnAs("$tableAlias.store_id", null) . ' = ?', $storeId
        );
        return parent::_joinAttributeToSelect($method, $attribute, $tableAlias, $condition, $fieldCode, $fieldAlias);
    }
}
