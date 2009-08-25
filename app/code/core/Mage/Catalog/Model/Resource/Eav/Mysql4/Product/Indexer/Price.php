<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog Product Price Indexer Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price extends Mage_Index_Model_Mysql4_Abstract
{
    /**
     * Default Product Type Price indexer resource model
     *
     * @var string
     */
    protected $_defaultPriceIndexer = 'catalog/product_indexer_price_default';

    /**
     * Product Type Price indexer resource models
     *
     * @var array
     */
    protected $_indexers;

    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_price', 'entity_id');
    }

/**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if price attribute values were updated
         */
        if (!isset($data['reindex_price'])) {
            return $this;
        }

        $write = $this->_getWriteAdapter();
        $this->cloneIndexTable(true);

        $indexer = $this->_getIndexer($data['product_type_id']);
        $processIds = array($productId);
        if ($indexer->getIsComposite()) {
            $this->_copyRelationIndexData($productId);
            $indexer->reindexEntity($productId);
        } else {
            $select = $write->select()
                ->from($this->getTable('catalog/product_relation'), array('parent_id'))
                ->where('child_id=?', $productId);
            $parentIds = $write->fetchCol($select);

            if ($parentIds) {
                $processIds = array_merge($processIds, $parentIds);
                $this->_copyRelationIndexData($parentIds);
                $indexer->reindexEntity($productId);

                $types = $this->_getProductTypes();
                foreach ($types as $typeIndexer) {
                    if ($typeIndexer->getIsComposite()) {
                        $typeIndexer->reindexEntity($parentIds);
                    }
                }
            } else {
                $indexer->reindexEntity($productId);
            }
        }

        $write->beginTransaction();
        try {
            // remove old index
            $where = $write->quoteInto('entity_id IN(?)', $processIds);
            $write->delete($this->getMainTable(), $where);

            // remove additional data from index
            $where = $write->quoteInto('entity_id NOT IN(?)', $processIds);
            $write->delete($this->getIdxTable(), $where);

            // insert new index
            $this->insertFromTable($this->getIdxTable(), $this->getMainTable());

            $this->commit();
        } catch (Exception $e) {
            $this->rollBack();
            throw $e;
        }

        return $this;
    }

    /**
     * Copy relations product index from primary index to temporary index table by parent entity
     *
     * @param array|int $parentIds
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price
     */
    protected function _copyRelationIndexData($parentIds)
    {
        $write  = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), array('child_id'))
            ->where('parent_id IN(?)', $parentIds);
        $children = $write->fetchCol($select);

        $select = $write->select()
            ->from($this->getMainTable())
            ->where('entity_id IN(?)', $children);
        $query  = $select->insertFromSelect($this->getIdxTable());
        $write->query($query);

        return $this;
    }

    /**
     * Retrieve Price indexer by Product Type
     *
     * @param string $productTypeId
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface
     */
    protected function _getIndexer($productTypeId)
    {
        $types = $this->_getProductTypes();
        if (!isset($types[$productTypeId])) {
            Mage::throwException(Mage::helper('catalog')->__('Unsupported product type "%s"', $productTypeId));
        }
        return $types[$productTypeId];
    }

    /**
     * Retrieve product type indexers
     *
     * @return array
     */
    protected function _getProductTypes()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = Mage::getSingleton('catalog/product_type')->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['price_indexer'])) {
                    $modelName = $typeInfo['price_indexer'];
                } else {
                    $modelName = $this->_defaultPriceIndexer;
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
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->cloneIndexTable(true);

        $indexers = $this->_getProductTypes();
        foreach ($indexers as $indexer) {
            /* @var $indexer Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Price_Interface */
            $indexer->reindexAll();
        }

        $this->syncData();
        return $this;
    }
}
