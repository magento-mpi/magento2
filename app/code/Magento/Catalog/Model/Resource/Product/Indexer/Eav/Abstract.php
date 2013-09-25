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
 * Catalog Product Eav Attributes abstract indexer resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
    extends Magento_Catalog_Model_Resource_Product_Indexer_Abstract
{
    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($resource, $eavConfig);
    }

    /**
     * Rebuild all index data
     *
     *
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();
            $this->_prepareIndex();
            $this->_prepareRelationIndex();
            $this->_removeNotVisibleEntityFromIndex();

            $this->syncData();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Rebuild index data by entities
     *
     *
     * @param int|array $processIds
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     * @throws Exception
     */
    public function reindexEntities($processIds)
    {
        $adapter = $this->_getWriteAdapter();

        $this->clearTemporaryIndexTable();

        if (!is_array($processIds)) {
            $processIds = array($processIds);
        }

        $parentIds = $this->getRelationsByChild($processIds);
        if ($parentIds) {
            $processIds = array_unique(array_merge($processIds, $parentIds));
        }
        $childIds  = $this->getRelationsByParent($processIds);
        if ($childIds) {
            $processIds = array_unique(array_merge($processIds, $childIds));
        }

        $this->_prepareIndex($processIds);
        $this->_prepareRelationIndex($processIds);
        $this->_removeNotVisibleEntityFromIndex();

        $adapter->beginTransaction();
        try {
            // remove old index
            $where = $adapter->quoteInto('entity_id IN(?)', $processIds);
            $adapter->delete($this->getMainTable(), $where);

            // insert new index
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Rebuild index data by attribute id
     * If attribute is not indexable remove data by attribute
     *
     *
     * @param int $attributeId
     * @param bool $isIndexable
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    public function reindexAttribute($attributeId, $isIndexable = true)
    {
        if (!$isIndexable) {
            $this->_removeAttributeIndexData($attributeId);
        } else {
            $this->clearTemporaryIndexTable();

            $this->_prepareIndex(null, $attributeId);
            $this->_prepareRelationIndex();
            $this->_removeNotVisibleEntityFromIndex();

            $this->_synchronizeAttributeIndexData($attributeId);
        }

        return $this;
    }

    /**
     * Prepare data index for indexable attributes
     *
     * @param array $entityIds      the entity ids limitation
     * @param int $attributeId      the attribute id limitation
     */
    abstract protected function _prepareIndex($entityIds = null, $attributeId = null);

    /**
     * Remove Not Visible products from temporary data index
     *
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    protected function _removeNotVisibleEntityFromIndex()
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from($idxTable, null);

        $condition = $write->quoteInto('=?',Magento_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        $this->_addAttributeToSelect(
            $select,
            'visibility',
            $idxTable . '.entity_id',
            $idxTable . '.store_id',
            $condition
        );

        $query = $select->deleteFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare data index for product relations
     *
     * @param array $parentIds  the parent entity ids limitation
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    protected function _prepareRelationIndex($parentIds = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from(array('l' => $this->getTable('catalog_product_relation')), 'parent_id')
            ->join(
                array('cs' => $this->getTable('core_store')),
                '',
                array())
            ->join(
                array('i' => $idxTable),
                'l.child_id = i.entity_id AND cs.store_id = i.store_id',
                array('attribute_id', 'store_id', 'value'))
            ->group(array(
                'l.parent_id', 'i.attribute_id', 'i.store_id', 'i.value'
            ));
        if (!is_null($parentIds)) {
            $select->where('l.parent_id IN(?)', $parentIds);
        }

        /**
         * Add additional external limitation
         */
        $this->_eventManager->dispatch('prepare_catalog_product_index_select', array(
            'select'        => $select,
            'entity_field'  => new Zend_Db_Expr('l.parent_id'),
            'website_field' => new Zend_Db_Expr('cs.website_id'),
            'store_field'   => new Zend_Db_Expr('cs.store_id')
        ));

        $query = $write->insertFromSelect($select, $idxTable, array(), Magento_DB_Adapter_Interface::INSERT_IGNORE);
        $write->query($query);

        return $this;
    }

    /**
     * Retrieve condition for retrieve indexable attribute select
     * the catalog/eav_attribute table must have alias is ca
     *
     * @return string
     */
    protected function _getIndexableAttributesCondition()
    {
        $conditions = array(
            'ca.is_filterable_in_search > 0',
            'ca.is_visible_in_advanced_search > 0',
            'ca.is_filterable > 0'
        );

        return implode(' OR ', $conditions);
    }

    /**
     * Remove index data from index by attribute id
     *
     * @param int $attributeId
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     */
    protected function _removeAttributeIndexData($attributeId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();
        try {
            $where = $adapter->quoteInto('attribute_id = ?', $attributeId);
            $adapter->delete($this->getMainTable(), $where);
            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        return $this;
    }

    /**
     * Synchronize temporary index table with index table by attribute id
     *
     * @param int $attributeId
     * @return Magento_Catalog_Model_Resource_Product_Indexer_Eav_Abstract
     * @throws Exception
     */
    protected function _synchronizeAttributeIndexData($attributeId)
    {
        $adapter = $this->_getWriteAdapter();
        $adapter->beginTransaction();
        try {
            // remove index by attribute
            $where = $adapter->quoteInto('attribute_id = ?', $attributeId);
            $adapter->delete($this->getMainTable(), $where);

            // insert new index
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        return $this;
    }
}
