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
 * Catalog Layer Attribute Filter Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Layer\Filter;

class Attribute extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize connection and define main table name
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_index_eav', 'entity_id');
    }

    /**
     * Apply attribute filter to product collection
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Attribute $filter
     * @param int $value
     * @return \Magento\Catalog\Model\Resource\Layer\Filter\Attribute
     */
    public function applyFilterToCollection($filter, $value)
    {
        $collection = $filter->getLayer()->getProductCollection();
        $attribute  = $filter->getAttributeModel();
        $connection = $this->_getReadAdapter();
        $tableAlias = $attribute->getAttributeCode() . '_idx';
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $collection->getStoreId()),
            $connection->quoteInto("{$tableAlias}.value = ?", $value)
        );

        $collection->getSelect()->join(
            array($tableAlias => $this->getMainTable()),
            implode(' AND ', $conditions),
            array()
        );

        return $this;
    }

    /**
     * Retrieve array with products counts per attribute option
     *
     * @param \Magento\Catalog\Model\Layer\Filter\Attribute $filter
     * @return array
     */
    public function getCount($filter)
    {
        // clone select from collection with filters
        $select = clone $filter->getLayer()->getProductCollection()->getSelect();
        // reset columns, order and limitation conditions
        $select->reset(\Zend_Db_Select::COLUMNS);
        $select->reset(\Zend_Db_Select::ORDER);
        $select->reset(\Zend_Db_Select::LIMIT_COUNT);
        $select->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $connection = $this->_getReadAdapter();
        $attribute  = $filter->getAttributeModel();
        $tableAlias = sprintf('%s_idx', $attribute->getAttributeCode());
        $conditions = array(
            "{$tableAlias}.entity_id = e.entity_id",
            $connection->quoteInto("{$tableAlias}.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("{$tableAlias}.store_id = ?", $filter->getStoreId()),
        );

        $select
            ->join(
                array($tableAlias => $this->getMainTable()),
                join(' AND ', $conditions),
                array('value', 'count' => new \Zend_Db_Expr("COUNT({$tableAlias}.entity_id)")))
            ->group("{$tableAlias}.value");

        return $connection->fetchPairs($select);
    }
}
