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
 * Catalog Product Eav Indexer Resource Model
 *
 * @category   Mage
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav extends Mage_Index_Model_Mysql4_Abstract
{
    /**
     * Define main index table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog/product_index_eav', 'entity_id');
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
         * Check if filterable attribute values were updated
         */
        if (!isset($data['eav_changes'])) {
            return $this;
        }

        $write  = $this->_getWriteAdapter();
        $this->cloneIndexTable(true);

        $select = $write->select()
            ->from($this->getTable('catalog/product_relation'), 'parent_id')
            ->where('child_id=?', $productId);
        $parentIds = $write->fetchCol($select);
        if ($parentIds) {
            $select = $write->select()
                ->from($this->getTable('catalog/product_relation'), 'child_id')
                ->where('parent_id IN(?)', $parentIds);
            $childIds = $write->fetchCol($select);
        } else {
            $childIds = array($productId);
        }

        $entityIds = array_unique(array_merge($childIds, $parentIds));

        $this->_prepareSelectIndex($entityIds);
        $this->_prepareMultiselectIndex($entityIds);
        if ($parentIds) {
            $this->_prepareRelationIndex($parentIds);
        }
        $this->_removeNotVisibleEntityFromIndex();

        $write->beginTransaction();
        try {
            // remove old index
            $where = $write->quoteInto('entity_id IN(?)', $entityIds);
            $write->delete($this->getMainTable(), $where);

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
     * Prepare temporary data index for select filtrable attribute
     *
     * @param array $entityIds    the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareSelectIndex($entityIds = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();
        // prepare select attributes
        $attrIds    = $this->_getFilterableAttributeIds(false);

        $select = $write->select()
            ->from(
                array('pid' => $this->getValueTable('catalog/product', 'int')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pis' => $this->getValueTable('catalog/product', 'int')),
                'pis.entity_id = pid.entity_id AND pis.attribute_id = pid.attribute_id'
                    . ' AND pis.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pis.value_id > 0, pis.value, pid.value)')))
            ->where('pid.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pid.attribute_id IN(?)', $attrIds)
            ->where('IF(pis.value_id > 0, pis.value, pid.value) IS NOT NULL');

        $statusCond = $write->quoteInto('=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeConditionToSelect($select, 'status', 'pid.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pid.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary data index for multiselect filtrable attribute
     *
     * @param array $entityIds    the entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareMultiselectIndex($entityIds = null)
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();
        // prepare multiselect attributes
        $attrIds    = $this->_getFilterableAttributeIds(true);

        $select = $write->select()
            ->from(
                array('pvd' => $this->getValueTable('catalog/product', 'varchar')),
                array('entity_id', 'attribute_id'))
            ->join(
                array('cs' => $this->getTable('core/store')),
                '',
                array('store_id'))
            ->joinLeft(
                array('pvs' => $this->getValueTable('catalog/product', 'varchar')),
                'pvs.entity_id = pvd.entity_id AND pvs.attribute_id = pvd.attribute_id'
                    . ' AND pvs.store_id=cs.store_id',
                array('value' => new Zend_Db_Expr('IF(pvs.value_id > 0, pvs.value, pvd.value)')))
            ->join(
                array('eo' => $this->getTable('eav/attribute_option')),
                'FIND_IN_SET(eo.option_id, IF(pvs.value_id, pvs.value, pvd.value))',
                array()
            )
            ->where('pvd.store_id=?', 0)
            ->where('cs.store_id!=?', 0)
            ->where('pvd.attribute_id IN(?)', $attrIds);

        $statusCond = $write->quoteInto('=?', '=' . Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        $this->_addAttributeConditionToSelect($select, 'status', 'pvd.entity_id', 'cs.store_id', $statusCond);

        if (!is_null($entityIds)) {
            $select->where('pvd.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare temporary data index for product relations
     *
     * @param array $parentIds  the parent entity ids limitation
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _prepareRelationIndex($parentIds = array())
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from(array('l' => $this->getTable('catalog/product_relation')), 'parent_id')
            ->join(
                array('i' => $idxTable),
                'l.child_id=i.entity_id',
                array('attribute_id', 'store_id', 'value'));
        if (!is_null($parentIds)) {
            $select->where('l.parent_id IN(?)', $parentIds);
        }

        $query = $select->insertIgnoreFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Remove Not Visible products from temporary data index
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _removeNotVisibleEntityFromIndex()
    {
        $write      = $this->_getWriteAdapter();
        $idxTable   = $this->getIdxTable();

        $select = $write->select()
            ->from($idxTable, null);

        $visibilityCond = $write->quoteInto('=?',Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE);
        $this->_addAttributeConditionToSelect($select, 'visibility', $idxTable.'.entity_id', $idxTable.'.store_id',
            $visibilityCond);

        $query = $select->deleteFromSelect($idxTable);
        $write->query($query);

        return $this;
    }

    /**
     * Rebuild all index data
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    public function reindexAll()
    {
        $this->cloneIndexTable(true);

        $this->_prepareSelectIndex();
        $this->_prepareMultiselectIndex();
        $this->_prepareRelationIndex();
        $this->_removeNotVisibleEntityFromIndex();

        $this->syncData();
        return $this;
    }

    /**
     * Retrieve attribute instance by attribute code
     *
     * @param string $attributeCode
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeCode);
    }

    /**
     * Retrieve filterable (used in LN) attribute ids
     *
     * @param bool $multiSelect
     * @return array
     */
    protected function _getFilterableAttributeIds($multiSelect)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('ca' => $this->getTable('catalog/eav_attribute')), 'attribute_id')
            ->join(
                array('ea' => $this->getTable('eav/attribute')),
                'ca.attribute_id = ea.attribute_id',
                array())
            ->where('ca.is_filterable_in_search>0 OR ca.is_filterable>0');

        if ($multiSelect == true) {
            $select->where('ea.backend_type = ?', 'varchar')
                ->where('ea.frontend_input = ?', 'multiselect');
        } else {
            $select->where('ea.backend_type = ?', 'int')
                ->where('ea.frontend_input = ?', 'select');
        }

        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Add attribute limitation to select object
     *
     * Condition variable must be contain "sign"
     *
     * @param Varien_Db_Select $select
     * @param string $attributeCode
     * @param string $entityField           the entity field name in base table
     * @param string $storeCond             the store SQL condition or field name
     * @param string $condition             the SQL limitation condition
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Indexer_Eav
     */
    protected function _addAttributeConditionToSelect($select, $attributeCode, $entityField, $storeCond, $condition)
    {
        $attribute   = $this->_getAttribute($attributeCode);
        $attributeId = (int)$attribute->getId();
        if ($attribute->isScopeGlobal()) {
            $alias = 'ta_' . $attributeCode;
            $select->join(
                array($alias => $attribute->getBackend()->getTable()),
                "{$entityField} = {$alias}.entity_id AND {$alias}.attribute_id = {$attributeId}"
                    . " AND {$alias}.store_id = 0",
                array()
            );
            $select->where("{$alias}.value {$condition}");
        } else {
            $dAlias = 'tad_' . $attributeCode;
            $sAlias = 'tas_' . $attributeCode;
            $table  = $attribute->getBackend()->getTable();

            $select->join(
                array($dAlias => $table),
                "{$entityField} = {$dAlias}.entity_id AND {$dAlias}.attribute_id = {$attributeId}"
                    . " AND {$dAlias}.store_id = 0",
                array()
            );
            $select->joinLeft(
                array($sAlias => $table),
                "{$dAlias}.entity_id = {$dAlias}.entity_id AND {$dAlias}.attribute_id = {$sAlias}.attribute_id"
                    . " AND {$sAlias}.store_id = {$storeCond}",
                array()
            );
            $select->where("IF({$sAlias}.value_id > 0, {$sAlias}.value, {$dAlias}.value) {$condition}");
        }

        return $this;
    }
}
