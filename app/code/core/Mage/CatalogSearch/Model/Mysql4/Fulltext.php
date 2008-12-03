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
    protected $_idsAreInitialized;

    protected function _construct()
    {
        $this->_init('catalogsearch/fulltext', 'product_id');
    }

    /**
     * Regenerate product index including its childs (for super products)
     *
     * @param int $productId Product Entity Id
     * @param int $storeId Store View Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
//    public function rebuildProductIndex($productId, $storeId = null)
//    {
//        $this->cleanIndex($storeId, $productId);
//        $data = $this->_prepareIndexData($storeId, $productId);
//        $this->_saveIndexData($data);
//        return $this;
//    }

    /**
     * Regenerate all products index for specific store
     *
     * @param int $storeId Store View Id
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    public function rebuildAllIndex($storeId)
    {
        $this->cleanIndex($storeId);

        $_allProductIds = $this->_getSearchableProductIds();
        $_superProductIds = $this->_getSuperProductIds();

        foreach ($_allProductIds as $productId) {

            $childIds = null;
            if (in_array($productId, $_superProductIds)) {
                $childIds = $this->_getProductChildIds($productId);
            }

            $index = $this->_prepareIndexValue($productId, $storeId, $childIds);
            $this->_saveRowIndexData($productId, $storeId, $index);
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
            $indexData .= $row['value'] . $separator;
        }

        return rtrim($indexData, $separator);
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
                array()
            )
            ->where('eav.is_searchable=?', '1')
            ->where('main.entity_id IN(?)', $productId)
            ->where('main.store_id=?', $storeId);

        return $select;
    }

    /**
     * Return all product children ids
     *
     * @param int $productId Product Entity Id
     * @return array
     */
    protected function _getProductChildIds($productId)
    {
        return array();
    }

    /**
     * Return all searchable product ids
     *
     * @return array Product Entity Ids
     */
    protected function _getSearchableProductIds()
    {
        $this->_initProductIds();
        return $this->_searchableProductIds;
    }

    /**
     * Return all product ids which have children products
     *
     * @return array Product Entity Ids
     */
    protected function _getSuperProductIds()
    {
        $this->_initProductIds();
        return $this->_superProductIds;
    }

    /**
     * Retrive all product ids and initialize searchable and super ids
     *
     * @return Mage_CatalogSearch_Model_Mysql4_Fulltext
     */
    protected function _initProductIds()
    {
        if (!$this->_idsAreInitialized) {

            $this->_searchableProductIds = array();
            $this->_superProductIds = array();

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
                ->where('status.attribute_id=?', $status->getAttributeId())
                ->where('status.value=?', '1');

            $rows = $this->_getReadAdapter()->fetchPairs($select);
            foreach ($rows as $productId => $typeId) {
                $this->_searchableProductIds[] = $productId;
                if ($this->_isSuperType($typeId)) {
                    $this->_superProductIds[] = $productId;
                }
            }

            $this->_idsAreInitialized = true;
        }
        return $this;
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