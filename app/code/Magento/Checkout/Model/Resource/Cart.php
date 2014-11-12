<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Model\Resource;

/**
 * Resource model for Checkout Cart
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Cart extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_quote', 'entity_id');
    }

    /**
     * Fetch items summary
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItemsSummary($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from(
            array('q' => $this->getTable('sales_quote')),
            array('items_qty', 'items_count')
        )->where(
            'q.entity_id = :quote_id'
        );

        $result = $read->fetchRow($select, array(':quote_id' => $quoteId));
        return $result ? $result : array('items_qty' => 0, 'items_count' => 0);
    }

    /**
     * Fetch items
     *
     * @param int $quoteId
     * @return array
     */
    public function fetchItems($quoteId)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from(
            array('qi' => $this->getTable('sales_quote_item')),
            array('id' => 'item_id', 'product_id', 'super_product_id', 'qty', 'created_at')
        )->where(
            'qi.quote_id = :quote_id'
        );

        return $read->fetchAll($select, array(':quote_id' => $quoteId));
    }

    /**
     * Make collection not to load products that are in specified quote
     *
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @param int $quoteId
     * @return $this
     */
    public function addExcludeProductFilter($collection, $quoteId)
    {
        $adapter = $this->_getReadAdapter();
        $exclusionSelect = $adapter->select()->from(
            $this->getTable('sales_quote_item'),
            array('product_id')
        )->where(
            'quote_id = ?',
            $quoteId
        );
        $condition = $adapter->prepareSqlCondition('e.entity_id', array('nin' => $exclusionSelect));
        $collection->getSelect()->where($condition);
        return $this;
    }
}
