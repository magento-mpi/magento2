<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogInventory Stock Status Indexer Resource Model
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogInventory_Model_Resource_Indexer_Stock extends Magento_Catalog_Model_Resource_Product_Indexer_Abstract
{
    /**
     * Stock Indexer models per product type
     * Sorted by priority
     *
     * @var array
     */
    protected $_indexers;

    /**
     * Default Stock Indexer resource model name
     *
     * @var string
     */
    protected $_defaultIndexer   = 'Magento_CatalogInventory_Model_Resource_Indexer_Stock_Default';

    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('cataloginventory_stock_status', 'product_id');
    }

    /**
     * Process stock item save action
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    public function cataloginventoryStockItemSave(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['product_id'])) {
            return $this;
        }

        $productId = $data['product_id'];
        $this->reindexProducts($productId);

        return $this;
    }

    /**
     * Refresh stock index for specific product ids
     *
     * @param array $productIds
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    public function reindexProducts($productIds)
    {
        $adapter = $this->_getWriteAdapter();
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $parentIds = $this->getRelationsByChild($productIds);
        if ($parentIds) {
            $processIds = array_merge($parentIds, $productIds);
        } else {
            $processIds = $productIds;
        }

        // retrieve product types by processIds
        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $processIds);
        $pairs  = $adapter->fetchPairs($select);

        $byType = array();
        foreach ($pairs as $productId => $typeId) {
            $byType[$typeId][$productId] = $productId;
        }

        $adapter->beginTransaction();
        try {
            $indexers = $this->_getTypeIndexers();
            foreach ($indexers as $indexer) {
                if (isset($byType[$indexer->getTypeId()])) {
                    $indexer->reindexEntity($byType[$indexer->getTypeId()]);
                }
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();

        return $this;
    }

    /**
     * Processing parent products after child product deleted
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    public function catalogProductDelete(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_parent_ids'])) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();

        $parentIds  = array();
        foreach ($data['reindex_stock_parent_ids'] as $parentId => $parentType) {
            $parentIds[$parentType][$parentId] = $parentId;
        }

        $adapter->beginTransaction();
        try {
            foreach ($parentIds as $parentType => $entityIds) {
                $this->_getIndexer($parentType)->reindexEntity($entityIds);
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }

    /**
     * Process product mass update action
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    public function catalogProductMassAction(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (empty($data['reindex_stock_product_ids'])) {
            return $this;
        }

        $adapter = $this->_getWriteAdapter();
        $processIds = $data['reindex_stock_product_ids'];
        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), 'COUNT(*)');
        $pCount = $adapter->fetchOne($select);

        // if affected more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds)) {
            return $this->reindexAll();
        }

        // calculate relations
        $select = $adapter->select()
            ->from($this->getTable('catalog_product_relation'), 'COUNT(DISTINCT parent_id)')
            ->where('child_id IN(?)', $processIds);
        $aCount = $adapter->fetchOne($select);
        $select = $adapter->select()
            ->from($this->getTable('catalog_product_relation'), 'COUNT(DISTINCT child_id)')
            ->where('parent_id IN(?)', $processIds);
        $bCount = $adapter->fetchOne($select);

        // if affected with relations more 30% of all products - run reindex all products
        if ($pCount * 0.3 < count($processIds) + $aCount + $bCount) {
            return $this->reindexAll();
        }


        // retrieve affected parent relation products
        $parentIds = $this->getRelationsByChild($processIds);
        if ($parentIds) {
            $processIds = array_merge($processIds, $parentIds);
        }

        // retrieve products types
        $select = $adapter->select()
            ->from($this->getTable('catalog_product_entity'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $processIds);
        $query  = $select->query(Zend_Db::FETCH_ASSOC);
        $byType = array();
        while ($row = $query->fetch()) {
            $byType[$row['type_id']][] = $row['entity_id'];
        }

        $adapter->beginTransaction();
        try {
            $indexers = $this->_getTypeIndexers();
            foreach ($indexers as $indexer) {
                if (!empty($byType[$indexer->getTypeId()])) {
                    $indexer->reindexEntity($byType[$indexer->getTypeId()]);
                }
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;
        }
        $adapter->commit();

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock
     */
    public function reindexAll()
    {
        $this->useIdxTable(true);
        $this->beginTransaction();
        try {
            $this->clearTemporaryIndexTable();

            foreach ($this->_getTypeIndexers() as $indexer) {
                $indexer->reindexAll();
            }

            $this->syncData();
            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }
        return $this;
    }

    /**
     * Retrieve Stock Indexer Models per Product Type
     *
     * @return array
     */
    protected function _getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = Mage::getSingleton('Magento_Catalog_Model_Product_Type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['stock_indexer'])) {
                    $modelName = $typeInfo['stock_indexer'];
                } else {
                    $modelName = $this->_defaultIndexer;
                }
                $isComposite = !empty($typeInfo['composite']);
                $indexer = Mage::getResourceModel($modelName)
                    ->setTypeId($typeId)
                    ->setIsComposite($isComposite);

                $this->_indexers[$typeId] = $indexer;
            }
        }
        return $this->_indexers;
    }

    /**
     * Retrieve Stock indexer by Product Type
     *
     * @param string $productTypeId
     * @return Magento_CatalogInventory_Model_Resource_Indexer_Stock_Interface
     */
    protected function _getIndexer($productTypeId)
    {
        $types = $this->_getTypeIndexers();
        if (!isset($types[$productTypeId])) {
            Mage::throwException(__('Unsupported product type "%1".', $productTypeId));
        }
        return $types[$productTypeId];
    }

    /**
     * Retrieve parent ids and types by child id
     * Return array with key product_id and value as product type id
     *
     * @param int $childId
     * @return array
     */
    public function getProductParentsByChild($childId)
    {
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from(array('l' => $this->getTable('catalog_product_relation')), array('parent_id'))
            ->join(
                array('e' => $this->getTable('catalog_product_entity')),
                'l.parent_id=e.entity_id',
                array('e.type_id')
            )
            ->where('l.child_id = :child_id');
        return $write->fetchPairs($select, array(':child_id' => $childId));
    }

    /**
     * Retrieve temporary index table name
     *
     * @param string $table
     * @return string
     */
    public function getIdxTable($table = null)
    {
        if ($this->useIdxTable()) {
            return $this->getTable('cataloginventory_stock_status_idx');
        }
        return $this->getTable('cataloginventory_stock_status_tmp');
    }
}
