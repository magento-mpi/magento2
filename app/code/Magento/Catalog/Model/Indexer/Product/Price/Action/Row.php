<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

/**
 * Class Row reindex action
 *
 * @package Magento\Catalog\Model\Indexer\Product\Price\Action
 */
class Row extends \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
{
    /**
     * Execute Row reindex
     *
     * @param int|null $id
     * @throws \Magento\Catalog\Exception
     */
    public function execute($id = null)
    {
        if (!isset($id) || empty($id)) {
            throw new \Magento\Catalog\Exception(__('Could not rebuild index for undefined product'));
        }
        try {
            $this->_reindex(array($id));
            $this->_logger->log('Row reindex for product - ' . $id . '');
        } catch (\Exception $e) {
            throw new \Magento\Catalog\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Refresh entities index
     *
     * @param array $changedIds
     * @return array Affected ids
     */
    protected function _reindex($changedIds = array())
    {
        $this->_emptyTable($this->_getIdxTable());
        $this->_prepareWebsiteDateTable();
        // retrieve products types
        $select = $this->_connection->select()
            ->from($this->_getTable('catalog_product_entity'), array('entity_id', 'type_id'))
            ->where('entity_id IN(?)', $changedIds);
        $pairs  = $this->_connection->fetchPairs($select);
        $byType = array();
        foreach ($pairs as $productId => $productType) {
            $byType[$productType][$productId] = $productId;
        }

        $compositeIds    = array();
        $notCompositeIds = array();

        foreach ($byType as $productType => $entityIds) {
            $indexer = $this->_getIndexer($productType);
            if ($indexer->getIsComposite()) {
                $compositeIds += $entityIds;
            } else {
                $notCompositeIds += $entityIds;
            }
        }

        if (!empty($notCompositeIds)) {
            $select = $this->_connection->select()
                ->from(
                    array('l' => $this->_getTable('catalog_product_relation')),
                    'parent_id'
                )
                ->join(
                    array('e' => $this->_getTable('catalog_product_entity')),
                    'e.entity_id = l.parent_id',
                    array('type_id')
                )
                ->where('l.child_id IN(?)', $notCompositeIds);
            $pairs  = $this->_connection->fetchPairs($select);
            foreach ($pairs as $productId => $productType) {
                if (!in_array($productId, $changedIds)) {
                    $changedIds[] = $productId;
                    $byType[$productType][$productId] = $productId;
                    $compositeIds[$productId] = $productId;
                }
            }
        }

        if (!empty($compositeIds)) {
            $this->_copyRelationIndexData($compositeIds, $notCompositeIds);
        }
        $this->_prepareTierPriceIndex($compositeIds + $notCompositeIds);
        $this->_prepareGroupPriceIndex($compositeIds + $notCompositeIds);

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (!empty($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        $this->_syncData($changedIds);
        return $compositeIds + $notCompositeIds;
    }

    /**
     * Copy relations product index from primary index to temporary index table by parent entity
     *
     * @param array $parentIds
     * @param array $excludeIds
     * @return \Magento\Catalog\Model\Indexer\Product\Price\Action\Row
     */
    protected function _copyRelationIndexData($parentIds, $excludeIds = null)
    {
        $write  = $this->_connection;
        $select = $write->select()
            ->from($this->_getTable('catalog_product_relation'), array('child_id'))
            ->where('parent_id IN(?)', $parentIds);
        if (!empty($excludeIds)) {
            $select->where('child_id NOT IN(?)', $excludeIds);
        }

        $children = $write->fetchCol($select);

        if ($children) {
            $select = $write->select()
                ->from($this->_getTable('catalog_product_index_price'))
                ->where('entity_id IN(?)', $children);
            $query  = $select->insertFromSelect($this->_getIdxTable(), array(), false);
            $write->query($query);
        }

        return $this;
    }

    /**
     * Retrieve price indexers per product type
     *
     * @return array
     */
    public function _getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = $this->_catalogProductType->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['price_indexer'])) {
                    $modelName = $typeInfo['price_indexer'];
                } else {
                    $modelName = $this->_defaultPriceIndexer;
                }
                $isComposite = !empty($typeInfo['composite']);
                $indexer = $this->_indexerPriceFactory->create($modelName)
                    ->setTypeId($typeId)
                    ->setIsComposite($isComposite);

                $this->_indexers[$typeId] = $indexer;
            }
        }

        return $this->_indexers;
    }
}
