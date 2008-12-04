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
 * @package    Mage_CatalogSearch
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * CatalogSearch Fulltext Index resource model
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Mysql4_Fulltext extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
    }

    /**
     * Regenerate all Stores index
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function rebuildIndex($storeId = null, $productId = null)
    {
        if ($storeId === null) {
            foreach (Mage::app()->getStores() as $store) {
                $this->_rebuildStoreIndex($store->getId(), $productId);
            }
        } else {
            $this->_rebuildStoreIndex($storeId, $productId);
        }

        return $this;
    }

    /**
     * Regenerate index for specific store
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _rebuildStoreIndex($storeId, $productId = null)
    {
        $this->cleanIndex($storeId, $productId);

        $stmt = $this->_getSearchableProductsStatement($storeId, $productId);
        while ($row = $stmt->fetch()) {

            $childIds = null;
            if ($this->_isSuperType($row['type_id'])) {
                $childIds = $this->_getProductChildIds($row['entity_id'], $row['type_id']);
            }

            $index = $this->_prepareIndexValue($row['entity_id'], $storeId, $childIds);
            $this->_saveRowIndexData($row['entity_id'], $storeId, $index);
        }

        return $this;
    }

    /**
     * Delete index data
     *
     * @param int $storeId Store View Id
     * @param int $productId Product Entity Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function cleanIndex($storeId = null, $productId = null)
    {
        $conditions = array('1');

        if ($storeId !== null) {
            $conditions[] = $this->_getWriteAdapter()->quoteInto('store_id=?', $storeId);
        }
        if ($productId !== null) {
            $conditions[] = $this->_getWriteAdapter()->quoteInto('product_id=?', $productId);
        }

        $where = implode(' AND ', $conditions);
        $this->_getWriteAdapter()->delete($this->getMainTable(), $where);
        return $this;
    }

    /**
     * TODO: multi-line inserts
     * Save prepared index data
     *
     * @param array $data Prepared data
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _saveRowIndexData($productId, $storeId, $index)
    {
        $row = array(
            'product_id'   => $productId,
            'store_id'     => $storeId,
            'data_index'   => $index
        );
        $this->_getWriteAdapter()->insert($this->getMainTable(), $row);

        return $this;
    }

    /**
     * Prepare value for data_index column
     *
     * @param int $productId Product Entity Id
     * @param int $storeId Store View Id
     * @param array $childIds Array of product children ids
     * @return string Data index value ready to save
     */
    protected function _prepareIndexValue($productId, $storeId, $childIds = null)
    {
        if (is_array($childIds)) {
            $productIds = array_merge(array($productId), $childIds);
        } else {
            $productIds = array($productId);
        }

        $sql = $this->_getFullSourceSql($productIds, $storeId);
        $values = $this->_getReadAdapter()->fetchAll($sql);

        $separator = ',';
        $indexData = '';
        foreach ($values as $row) {
            $indexData .= $this->_getAttributeValue($row['attribute_id'], $row['value'], $storeId) . $separator;
        }

        return rtrim($indexData, $separator);
    }

    /**
     * Prepare attribute value
     *
     * @param int $attributeId
     * @param string $value
     * @param int $storeId Store View Id
     * @return string
     */
    protected function _getAttributeValue($attributeId, $value, $storeId)
    {
        /* @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        $attribute = Mage::getSingleton('catalog/resource_eav_attribute')
            ->setStoreId($storeId)
            ->load($attributeId);

        $sourceModel = $attribute->getSourceModel();
        if ($sourceModel) {
            $model = Mage::getModel($sourceModel);
            if (!is_object($model)) {
                return $value;
            }

            $_textValue = $model->setAttribute($attribute)->getOptionText($value);

            if (is_array($_textValue)) {
                return implode(',', $_textValue);
            }
            return $_textValue;
        }
        return $value;
    }

    /**
     * Return final sql query for retrieving attributes values
     *
     * @param int|array $productId Product Entity Id
     * @param int $storeId Store View Id
     * @return string Sql query ready to use
     */
    protected function _getFullSourceSql($productId, $storeId)
    {
        $unions = array(
            $this->_prepareTypedSelect($productId, 'int', $storeId)->__toString(),
            $this->_prepareTypedSelect($productId, 'text', $storeId)->__toString(),
            $this->_prepareTypedSelect($productId, 'varchar', $storeId)->__toString()
        );
        return '(' . implode(') UNION (', $unions) . ')';
    }

    /**
     * Prepare select object for specific backend type
     *
     * @param int|array $productId Product Entity Id
     * @param string $backendType Attributes Backend Type
     * @param int $storeId Store View Id
     * @return Zend_Db_Select
     */
    protected function _prepareTypedSelect ($productId, $backendType, $storeId)
    {
        if (!is_array($productId)) {
            $productId = array($productId);
        }

        $select = $this->_getReadAdapter()->select();
        $select->from(
                array('main' => $this->getTable('catalog/product') . '_' . $backendType),
                array('attribute_id', 'store_id', 'entity_id', 'value')
            )
            ->joinInner(array('eav' => $this->getTable('eav/attribute')),
                'main.attribute_id = eav.attribute_id',
                array('source_model')
            )
            ->where('eav.is_searchable=?', '1')
            ->where('main.entity_id IN(?)', $productId)
            ->where('main.store_id=?', $storeId);

        return $select;
    }

    /**
     * Retrive all product ids and initialize searchable and super ids
     *
     * @param int $storeId Store View Id
     * @param int|array $productId Product Entity Id
     * @return Zend_Db_Statement
     */
    protected function _getSearchableProductsStatement($storeId, $productId = null)
    {
        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');
        $visibility = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(),'visibility');
        $status = Mage::getModel('eav/config')->getAttribute($entityType->getEntityTypeId(),'status');

        $select = $this->_getReadAdapter()->select();
        $select->from(
                array('e' => $this->getTable('catalog/product')),
                array('entity_id', 'type_id')
            )
            ->joinInner(array('visibility' => $visibility->getBackend()->getTable()),
                'e.entity_id=visibility.entity_id',
                array()
            )
            ->joinInner(array('status' => $status->getBackend()->getTable()),
                'e.entity_id=status.entity_id',
                array()
            )
            ->where('visibility.attribute_id=?', $visibility->getAttributeId())
            ->where('visibility.value IN(?)', array(3,4))
            ->where('visibility.store_id=?', $storeId)
            ->where('status.attribute_id=?', $status->getAttributeId())
            ->where('status.value=?', '1')
            ->where('status.store_id=?', $storeId);

        if ($productId != null) {
            $productId = is_array($productId) ? $productId : array($productId);
            $select->where('e.entity_id IN(?)', $productId);
        }

        return $select->query();
    }

    /**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @param string $typeId Super Product Link Type
     * @return array
     */
    protected function _getProductChildIds($productId, $typeId)
    {
        $where = null;
        switch ($typeId) {
            case 'grouped':
                $table = 'catalog/product_link';
                $parentId = $productId;
                $parentFieldName = 'product_id';
                $childFieldName = 'linked_product_id';
                $where = 'link_type_id=3';
                break;
            case 'configurable':
                $table = 'catalog/product_super_link';
                $parentId = $productId;
                $parentFieldName = 'parent_id';
                $childFieldName = 'product_id';
                break;
            case 'bundle':
                $table = 'bundle/selection';
                $parentId = $productId;
                $parentFieldName = 'parent_product_id';
                $childFieldName = 'product_id';
                break;
            default:
                return array();
                break;
        }
        return $this->_getLinkedIds($table, $parentId, $parentFieldName, $childFieldName, $where);
    }

    /**
     * Retrieve child-linked product ids
     *
     * @param string $table Table of links parent-child
     * @param int $parentId Parent product Id value
     * @param string $parentFieldName Field name of parent product Id
     * @param string $childFieldName Field name of linked product Id
     * @param string $where Additional condition to select query
     * @return array Linked products ids
     */
    public function _getLinkedIds($table, $parentId, $parentFieldName, $childFieldName, $where = null)
    {
        $select = $this->_getReadAdapter()->select();
        $select->from(array('main' => $this->getTable($table)), array($childFieldName))
            ->where("{$parentFieldName}=?", $parentId);
        if ($where !== null) {
            $select->where($where);
        }
        return $this->_getReadAdapter()->fetchCol($select);
    }

    /**
     * Check product type
     *
     * @param string $typeId Product Type Id
     * @return boolean
     */
    protected function _isSuperType($typeId)
    {
        return in_array($typeId, array('grouped', 'configurable', 'bundle'));
    }
}