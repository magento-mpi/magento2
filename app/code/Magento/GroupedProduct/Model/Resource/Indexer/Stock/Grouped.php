<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GroupedProduct
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grouped Products Stock Status Indexer Resource Model
 *
 * @category    Magento
 * @package     Magento_GroupedProduct
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GroupedProduct\Model\Resource\Indexer\Stock;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;

class Grouped extends \Magento\CatalogInventory\Model\Resource\Indexer\Stock\DefaultStock
{
    /**
     * Get the select object for get stock status by grouped product ids
     *
     * @param int|array $entityIds
     * @param bool $usePrimaryTable use primary or temporary index table
     * @return \Magento\DB\Select
     */
    protected function _getStockStatusSelect($entityIds = null, $usePrimaryTable = false)
    {
        $adapter = $this->_getWriteAdapter();
        $idxTable = $usePrimaryTable ? $this->getMainTable() : $this->getIdxTable();
        $select = $adapter->select()->from(
            array('e' => $this->getTable('catalog_product_entity')),
            array('entity_id')
        );
        $this->_addWebsiteJoinToSelect($select, true);
        $this->_addProductWebsiteJoinToSelect($select, 'cw.website_id', 'e.entity_id');
        $select->columns(
            'cw.website_id'
        )->join(
            array('cis' => $this->getTable('cataloginventory_stock')),
            '',
            array('stock_id')
        )->joinLeft(
            array('cisi' => $this->getTable('cataloginventory_stock_item')),
            'cisi.stock_id = cis.stock_id AND cisi.product_id = e.entity_id',
            array()
        )->joinLeft(
            array('l' => $this->getTable('catalog_product_link')),
            'e.entity_id = l.product_id AND l.link_type_id=' .
            \Magento\GroupedProduct\Model\Resource\Product\Link::LINK_TYPE_GROUPED,
            array()
        )->joinLeft(
            array('le' => $this->getTable('catalog_product_entity')),
            'le.entity_id = l.linked_product_id',
            array()
        )->joinLeft(
            array('i' => $idxTable),
            'i.product_id = l.linked_product_id AND cw.website_id = i.website_id AND cis.stock_id = i.stock_id',
            array()
        )->columns(
            array('qty' => new \Zend_Db_Expr('0'))
        )->where(
            'cw.website_id != 0'
        )->where(
            'e.type_id = ?',
            $this->getTypeId()
        )->group(
            array('e.entity_id', 'cw.website_id', 'cis.stock_id')
        );

        // add limitation of status
        $productStatusExpr = $this->_addAttributeToSelect($select, 'status', 'e.entity_id', 'cs.store_id');
        $productStatusCond = $adapter->quoteInto($productStatusExpr . '=?', ProductStatus::STATUS_ENABLED);

        if ($this->_isManageStock()) {
            $statusExpression = $adapter->getCheckSql(
                'cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 0',
                1,
                'cisi.is_in_stock'
            );
        } else {
            $statusExpression = $adapter->getCheckSql(
                'cisi.use_config_manage_stock = 0 AND cisi.manage_stock = 1',
                'cisi.is_in_stock',
                1
            );
        }

        $optExpr = $adapter->getCheckSql("{$productStatusCond} AND le.required_options = 0", 'i.stock_status', 0);
        $stockStatusExpr = $adapter->getLeastSql(array("MAX({$optExpr})", "MIN({$statusExpression})"));

        $select->columns(array('status' => $stockStatusExpr));

        if (!is_null($entityIds)) {
            $select->where('e.entity_id IN(?)', $entityIds);
        }

        return $select;
    }
}
