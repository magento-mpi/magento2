<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content items collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Resource_Item_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Config
     *
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * Resource helper
     *
     * @var Magento_Core_Model_Resource_Helper_Mysql4
     */
    protected $_resourceHelper;

    /**
     * @param Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Eav_Model_Config $config
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Magento_Core_Model_EntityFactory $entityFactory
     * @param Magento_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Eav_Model_Config $config,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Logger $logger,
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Magento_Core_Model_EntityFactory $entityFactory,
        Magento_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_resourceHelper = $resourceHelper;
        $this->_eavConfig = $config;
        parent::__construct($eventManager, $logger, $fetchStrategy, $entityFactory, $resource);
    }

    protected function _construct()
    {
        $this->_init('Magento_GoogleShopping_Model_Item', 'Magento_GoogleShopping_Model_Resource_Item');
    }

    /**
     * Init collection select
     *
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Filter collection by specified product id
     *
     * @param int $productId
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    public function addProductFilterId($productId)
    {
        $this->getSelect()->where('main_table.product_id=?', $productId);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     * @param string $field
     * @param null|string|array $condition
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition=null)
    {
        if ($field == 'name') {
            $conditionSql = $this->_getConditionSql(
                $this->getConnection()->getIfNullSql('p.value', 'p_d.value'), $condition
            );
            $this->getSelect()->where($conditionSql, null, Magento_DB_Select::TYPE_CONDITION);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Join product and type data
     *
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _joinTables()
    {
        $entityType = $this->_eavConfig->getEntityType('catalog_product');
        $attribute = $this->_eavConfig->getAttribute($entityType->getEntityTypeId(),'name');

        $joinConditionDefault =
            sprintf("p_d.attribute_id=%d AND p_d.store_id='0' AND main_table.product_id=p_d.entity_id",
                $attribute->getAttributeId()
            );
        $joinCondition =
            sprintf("p.attribute_id=%d AND p.store_id=main_table.store_id AND main_table.product_id=p.entity_id",
                $attribute->getAttributeId()
            );

        $this->getSelect()
            ->joinLeft(
                array('p_d' => $attribute->getBackend()->getTable()),
                $joinConditionDefault,
                array());

        $this->getSelect()
            ->joinLeft(
                array('p' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array('name' => $this->getConnection()->getIfNullSql('p.value', 'p_d.value')));

        $this->getSelect()
            ->joinLeft(
                array('types' => $this->getTable('googleshopping_types')),
                'main_table.type_id=types.type_id'
            );
        $this->_resourceHelper->prepareColumnsList($this->getSelect()); // avoid column name collision

        return $this;
    }
}
